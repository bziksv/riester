<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Light HTML cleanup for W3C/Yandex validator noise in editor content.
 *
 * @param string $html
 * @param string|null $pageH1 Optional page H1 text; a leading H2–H6 with the same
 *                            wording is removed so it does not duplicate the H1.
 */
function riester_sanitize_html_content($html, $pageH1 = null)
{
    if (!is_string($html) || $html === '') {
        return $html;
    }

    // Drop leading heading that copies the page H1 (common in section DESCRIPTION)
    if (is_string($pageH1) && $pageH1 !== '') {
        $normalizeHeading = function ($s) {
            $s = html_entity_decode(strip_tags($s), ENT_QUOTES, 'UTF-8');
            $s = preg_replace('/\s+/u', ' ', $s);
            return mb_strtolower(trim($s));
        };
        $h1Norm = $normalizeHeading($pageH1);
        if ($h1Norm !== '') {
            $html = preg_replace_callback(
                '#^\s*<(h[1-6])(\s[^>]*)?>([\s\S]*?)</\1>\s*#iu',
                function ($m) use ($normalizeHeading, $h1Norm) {
                    if ($normalizeHeading($m[3]) === $h1Norm) {
                        return '';
                    }
                    return $m[0];
                },
                $html,
                1
            );
        }
    }

    // Cyrillic "с" mistaken for Latin "c" in hex colors (#ссс → #ccc)
    $html = str_replace(array('#ссс', '#ССС'), '#ccc', $html);

    // Broken editor CSS: "margin: px 0px …" / "1.px" (typo before unit)
    $html = preg_replace('/\bmargin\s*:\s*px\b/iu', 'margin: 0', $html);
    $html = preg_replace('/(\d+)\.(px)\b/iu', '$1$2', $html);

    $html = str_replace(
        array('<noindex>', '</noindex>'),
        array('<!--noindex-->', '<!--/noindex-->'),
        $html
    );

    // <h3><p>Title</p></h3> → <h3>Title</h3>
    $html = preg_replace(
        '#<(h[1-6])(\s[^>]*)?>\s*<p[^>]*>\s*([\s\S]*?)\s*</p>\s*</\1>#iu',
        '<$1$2>$3</$1>',
        $html
    );

    // Unwrap <span> that contains block-level content (p/h2/ul/…)
    $html = preg_replace(
        '#<span\b[^>]*>\s*([\s\S]*?(?:<(?:p|h[1-6]|ul|ol|div|figure|table|hr)\b)[\s\S]*?)</span>#iu',
        '$1',
        $html
    );

    // Stray closing tags with no matching open (editor leftovers)
    foreach (array('article', 'section', 'aside', 'main') as $tag) {
        if (!preg_match('/<' . $tag . '\b/i', $html)) {
            $html = preg_replace('/<\/' . $tag . '>/i', '', $html);
        }
    }

    // Encode /search/?q=… query values that still contain spaces/raw Unicode
    $html = preg_replace_callback(
        '#(href=["\'])(/search/\?q=)([^"\']+)(["\'])#u',
        function ($m) {
            $q = $m[3];
            $extra = '';
            if (($p = strpos($q, '&')) !== false) {
                $extra = substr($q, $p);
                $q = substr($q, 0, $p);
            }
            if (strpos($q, ' ') === false && !preg_match('/[^\x00-\x7F]/u', $q)) {
                return $m[1] . $m[2] . $q . $extra . $m[4];
            }
            $decoded = rawurldecode(str_replace('+', ' ', $q));
            return $m[1] . $m[2] . rawurlencode($decoded) . $extra . $m[4];
        },
        $html
    );

    // <h2><figure>…</figure> Title</h2> → figure + <h2>Title</h2>
    $html = preg_replace(
        '#<h2(\s[^>]*)?>\s*(<figure\b[\s\S]*?</figure>)\s*([^<]*)\s*</h2>#iu',
        '$2<h2$1>$3</h2>',
        $html
    );

    // Empty nested list: <ol><ul></ul></ol>
    $html = preg_replace('#<ol([^>]*)>\s*<ul[^>]*>\s*</ul>\s*</ol>#iu', '', $html);

    // Broken editor markup: alt text leaked as attributes
    // (набор="" riester="" ri-scope="" de="" luxe="") — keep a safe whitelist.
    $html = preg_replace_callback(
        '#<img\b([^>]*)>#iu',
        function ($m) {
            $allowed = array(
                'src' => true, 'alt' => true, 'width' => true, 'height' => true,
                'title' => true, 'class' => true, 'id' => true, 'style' => true,
                'loading' => true, 'decoding' => true, 'fetchpriority' => true,
                'srcset' => true, 'sizes' => true, 'crossorigin' => true,
                'referrerpolicy' => true, 'usemap' => true, 'ismap' => true,
                'align' => true, 'border' => true, 'hspace' => true, 'vspace' => true,
                'role' => true,
            );
            $attrs = '';
            if (preg_match_all(
                '#([^\s=<>/]+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]*)))?#u',
                $m[1],
                $am,
                PREG_SET_ORDER
            )) {
                foreach ($am as $a) {
                    $name = strtolower($a[1]);
                    if (
                        !isset($allowed[$name])
                        && strpos($name, 'data-') !== 0
                        && strpos($name, 'aria-') !== 0
                    ) {
                        continue;
                    }
                    if (preg_match('#=\s*"([^"]*)"#', $a[0], $vm)) {
                        $attrs .= ' ' . $name . '="' . $vm[1] . '"';
                    } elseif (preg_match("#=\s*'([^']*)'#", $a[0], $vm)) {
                        $attrs .= ' ' . $name . '="' . $vm[1] . '"';
                    } elseif (preg_match('#=\s*([^\s>]+)#', $a[0], $vm)) {
                        $attrs .= ' ' . $name . '="' . htmlspecialchars($vm[1], ENT_QUOTES) . '"';
                    } elseif ($name === 'ismap') {
                        $attrs .= ' ' . $name;
                    }
                }
            }
            return '<img' . $attrs . '>';
        },
        $html
    );

    // Close unclosed <ul>/<ol> (no matching </ul> before next list / EOS).
    $html = preg_replace_callback(
        '#<(ul|ol)(\s[^>]*)?>((?:(?!</\1>|<ul\b|<ol\b)[\s\S])*)(</\1>)?#iu',
        function ($m) {
            $tag = $m[1];
            $attrs = $m[2];
            $inner = $m[3];
            if (!empty($m[4])) {
                return $m[0];
            }
            if (preg_match('#^([\s\S]*</li>)(\s*<(?:br|p|h[1-6]|div|hr)\b[\s\S]*)$#iu', $inner, $parts)) {
                return '<' . $tag . $attrs . '>' . $parts[1] . '</' . $tag . '>' . $parts[2];
            }
            return '<' . $tag . $attrs . '>' . $inner . '</' . $tag . '>';
        },
        $html
    );

    // Fix bare text / trailing blocks / orphan closings inside ul/ol
    $html = preg_replace_callback(
        '#<(ul|ol)(\s[^>]*)?>([\s\S]*?)</\1>#iu',
        function ($m) {
            $tag = $m[1];
            $attrs = isset($m[2]) ? $m[2] : '';
            $inner = $m[3];
            $trailing = '';

            // Orphan closings inside list: "text</p></li>" → "<li><p>text</p></li>"
            for ($i = 0; $i < 20; $i++) {
                $prev = $inner;
                $inner = preg_replace(
                    '#(?:(?<=</li>)|(?<=\n)|(?<=^))(\s*)([^<\s/>][^<>]*?)\s*</p>\s*</li>#u',
                    '$1<li>$2</li>',
                    $inner
                );
                if ($inner === $prev || $inner === null) {
                    if ($inner === null) {
                        $inner = $prev;
                    }
                    break;
                }
            }

            // Move trailing non-li content out of the list
            if (preg_match('#^([\s\S]*</li>)\s*([\s\S]+)$#iu', $inner, $parts)) {
                if (!preg_match('#<li\b#i', $parts[2])) {
                    $inner = $parts[1];
                    $trailing = $parts[2];
                }
            }

            // Drop <br> that sit as direct children of the list (between/after li)
            $inner = preg_replace('#(?<=</li>)\s*<br\s*/?>\s*#iu', '', $inner);
            $inner = preg_replace('#^\s*<br\s*/?>\s*#iu', '', $inner);

            // Move <figure> out of lists (invalid as ol/ul child)
            $figures = '';
            $inner = preg_replace_callback(
                '#\s*<figure\b[\s\S]*?</figure>#iu',
                function ($fm) use (&$figures) {
                    $figures .= $fm[0];
                    return '';
                },
                $inner
            );

            // Wrap bare text nodes between list items
            $inner = preg_replace_callback(
                '#(^|</li>)\s*([^<\s/>][^<>]*?)\s*(?=<li\b|$)#iu',
                function ($mm) {
                    $text = trim($mm[2]);
                    if ($text === '') {
                        return $mm[0];
                    }
                    return $mm[1] . '<li>' . $text . '</li>';
                },
                $inner
            );

            return '<' . $tag . $attrs . '>' . $inner . '</' . $tag . '>' . $figures . $trailing;
        },
        $html
    );

    // Orphan closings outside lists (e.g. after </h2>): "text</p></li>" → "<p>text</p>"
    for ($i = 0; $i < 20; $i++) {
        $prev = $html;
        $html = preg_replace(
            '#((?:</h2>|<br\s*/?>|</p>)\s*)([^<\s/>][^<>]*?)\s*</p>\s*</li>#iu',
            '$1<p>$2</p>',
            $html
        );
        if ($html === $prev || $html === null) {
            if ($html === null) {
                $html = $prev;
            }
            break;
        }
    }

    // Orphan <li> after heading/figure (no wrapping ul/ol) → wrap in <ul>
    $html = preg_replace(
        '#(</(?:h[1-6]|figure)>)(\s*(?:<li\b[\s\S]*?</li>\s*)+)#iu',
        '$1<ul>$2</ul>',
        $html
    );

    // <li><p>text</p></li> → <li>text</li> (avoids “p child of ul” noise)
    $html = preg_replace(
        '#<li(\s[^>]*)?>\s*<p\b[^>]*>\s*([\s\S]*?)\s*</p>\s*</li>#iu',
        '<li$1>$2</li>',
        $html
    );

    // Heading hierarchy under page H1: remap used levels to H2… without skips
    // (e.g. only H3s → H2; H2+H4 → H2+H3).
    if (preg_match_all('#<h([1-6])\b#iu', $html, $hm)) {
        $used = array_values(array_unique(array_map('intval', $hm[1])));
        sort($used, SORT_NUMERIC);
        $map = array();
        $next = 2;
        foreach ($used as $lvl) {
            $map[$lvl] = min($next, 6);
            $next++;
        }
        $needsRewrite = false;
        foreach ($map as $from => $to) {
            if ($from !== $to) {
                $needsRewrite = true;
                break;
            }
        }
        if ($needsRewrite) {
            $html = preg_replace_callback(
                '#</?h([1-6])(\s[^>]*)?>#iu',
                function ($m) use ($map) {
                    $lvl = (int)$m[1];
                    $new = isset($map[$lvl]) ? $map[$lvl] : $lvl;
                    $closing = (isset($m[0][1]) && $m[0][1] === '/') ? '/' : '';
                    $attrs = isset($m[2]) ? $m[2] : '';
                    return '<' . $closing . 'h' . $new . $attrs . '>';
                },
                $html
            );
        }
    }

    return $html;
}
