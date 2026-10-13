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

    // Broken editor CSS: "margin: px …", "margin: 0px px 1.5em", "1.px"
    $html = preg_replace('/\bmargin\s*:\s*px\b/iu', 'margin: 0', $html);
    $html = preg_replace('/(\d+)\.(px)\b/iu', '$1$2', $html);
    // Bare "px" length token (not part of "10px"): "0px px 1.5em" → "0px 0 1.5em"
    $html = preg_replace('/(?<=[\s:;,])px\b/iu', '0', $html);
    // Leading decimal without 0 (some CSS parsers: ".2" → "0.2" in rgba/opacity)
    $html = preg_replace('/\brgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*\.(\d+)\s*\)/iu', 'rgba($1, $2, $3, 0.$4)', $html);
    // IE-only filters in inline styles → CSS parse noise for Yandex
    $html = preg_replace('/\s*filter\s*:\s*progid:[^;"]*;?/iu', '', $html);
    $html = preg_replace('/\s*-ms-filter\s*:\s*[^;"]*;?/iu', '', $html);

    // Invalid list type on <ol> (disc/circle/square belong to <ul>)
    $html = preg_replace('/(<ol\b[^>]*)\s+type\s*=\s*(["\'])(?:disc|circle|square)\2/iu', '$1', $html);

    // Drop editor <style> blocks inside content (invalid under div/body fragments)
    $html = preg_replace('#<style\b[^>]*>[\s\S]*?</style>#iu', '', $html);

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

    // Headings with orphan </p>/<p> and/or media inside (editor leftovers)
    // e.g. <h2>Text: </p> <p> <img …></h2> → <h2>Text:</h2><img …>
    $html = preg_replace_callback(
        '#<(h[1-6])(\s[^>]*)?>([\s\S]*?)</\1>#iu',
        function ($m) {
            $tag = $m[1];
            $attrs = isset($m[2]) ? $m[2] : '';
            $inner = $m[3];
            $moved = '';
            $inner = preg_replace_callback(
                '#\s*((?:<figure\b[\s\S]*?</figure>)|(?:<img\b[^>]*/?>))#iu',
                function ($im) use (&$moved) {
                    $moved .= $im[1];
                    return ' ';
                },
                $inner
            );
            $inner = preg_replace('#</?p\b[^>]*>#iu', '', $inner);
            $inner = trim(preg_replace('/\s+/u', ' ', $inner));
            if ($inner === '' && $moved === '') {
                return $m[0];
            }
            if ($inner === '') {
                return $moved;
            }
            return '<' . $tag . $attrs . '>' . $inner . '</' . $tag . '>' . $moved;
        },
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
            if (!preg_match('/\balt\s*=/i', $attrs)) {
                $attrs .= ' alt=""';
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

    // Orphan <li> outside ul/ol (incl. after </ul>, with nested lists) → wrap in <ul>
    $html = riester_sanitize_wrap_orphan_lis($html);

    // <li><p>text</p></li> → <li>text</li> (avoids “p child of ul” noise)
    $html = preg_replace(
        '#<li(\s[^>]*)?>\s*<p\b[^>]*>\s*([\s\S]*?)\s*</p>\s*</li>#iu',
        '<li$1>$2</li>',
        $html
    );

    // Drop orphan closing tags (extra </ol></ul></li></p>) via a shallow stack walk
    $html = riester_sanitize_drop_orphan_closings($html);

    // Heading hierarchy: close level skips without inventing a false outline.
    // If $pageH1 is set, the page already has an H1 → remap content to H2+.
    // Otherwise keep a content H1 (static pages like /about/ content.php).
    if (preg_match_all('#<h([1-6])\b#iu', $html, $hm)) {
        $used = array_values(array_unique(array_map('intval', $hm[1])));
        sort($used, SORT_NUMERIC);
        $map = array();
        $next = (is_string($pageH1) && $pageH1 !== '') ? 2 : 1;
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

/**
 * Include a Bitrix area file and sanitize its HTML for validator noise.
 */
function riester_include_html_file($relativePath, $params = array(), $arParams = array())
{
    global $APPLICATION;
    if (!is_array($params)) {
        $params = array();
    }
    if (!is_array($arParams)) {
        $arParams = array();
    }
    ob_start();
    $APPLICATION->IncludeFile($relativePath, $params, $arParams);
    echo riester_sanitize_html_content(ob_get_clean());
}

/**
 * Wrap <li> sequences that appear outside any ul/ol (editor leftovers after </ul>).
 * Nested lists inside those li are preserved.
 */
function riester_sanitize_wrap_orphan_lis($html)
{
    if (!is_string($html) || $html === '' || stripos($html, '<li') === false) {
        return $html;
    }

    $voidish = array(
        'br' => true, 'hr' => true, 'img' => true, 'meta' => true,
        'input' => true, 'link' => true, 'source' => true, 'wbr' => true,
    );
    $out = '';
    $offset = 0;
    $len = strlen($html);
    $listDepth = 0;
    $orphan = '';
    $orphanDepth = 0;

    $flushOrphan = function () use (&$out, &$orphan, &$orphanDepth) {
        if ($orphan === '') {
            return;
        }
        $out .= '<ul>' . $orphan . '</ul>';
        $orphan = '';
        $orphanDepth = 0;
    };

    while ($offset < $len) {
        if (!preg_match('/<\/?([a-zA-Z][a-zA-Z0-9]*)\b[^>]*>/', $html, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $chunk = substr($html, $offset);
            if ($orphan !== '') {
                $orphan .= $chunk;
                $flushOrphan();
            } else {
                $out .= $chunk;
            }
            break;
        }

        $tagStart = $m[0][1];
        $tagHtml = $m[0][0];
        $tagName = strtolower($m[1][0]);
        $text = substr($html, $offset, $tagStart - $offset);
        $isClose = (isset($tagHtml[1]) && $tagHtml[1] === '/');
        $isSelf = (!$isClose && (substr($tagHtml, -2) === '/>' || isset($voidish[$tagName])));

        if ($orphan !== '') {
            $orphan .= $text;
            if (!$isSelf && ($tagName === 'li' || $tagName === 'ul' || $tagName === 'ol')) {
                if ($isClose) {
                    $orphanDepth--;
                } else {
                    $orphanDepth++;
                }
            }
            $orphan .= $tagHtml;
            $offset = $tagStart + strlen($tagHtml);
            if ($orphanDepth <= 0) {
                $orphanDepth = 0;
                if (!preg_match('/\G(?:\s|<br\s*\/?>)*<li\b/i', $html, $pm, 0, $offset)) {
                    $flushOrphan();
                }
            }
            continue;
        }

        $out .= $text;

        if (!$isClose && !$isSelf && $tagName === 'li' && $listDepth === 0) {
            $orphan = $tagHtml;
            $orphanDepth = 1;
            $offset = $tagStart + strlen($tagHtml);
            continue;
        }

        if (!$isSelf && ($tagName === 'ul' || $tagName === 'ol')) {
            if ($isClose) {
                if ($listDepth > 0) {
                    $listDepth--;
                }
            } else {
                $listDepth++;
            }
        }

        $out .= $tagHtml;
        $offset = $tagStart + strlen($tagHtml);
    }

    $flushOrphan();
    return $out;
}

/**
 * Strip IE-only / validator-hostile CSS from inlined <style> blocks (cssinliner).
 */
function riester_sanitize_page_css(&$content)
{
    if (!is_string($content) || $content === '') {
        return;
    }
    // After cssinliner (sort 510): strip IE filters / unset from inlined <style>
    if (stripos($content, '<style') === false) {
        return;
    }
    $replaced = preg_replace_callback(
        '#(<style\b[^>]*>)([\s\S]*?)(</style>)#i',
        function ($m) {
            $css = $m[2];
            $css = preg_replace('/\s*filter\s*:\s*progid:[^;{}]+;?/i', '', $css);
            $css = preg_replace('/\s*-ms-filter\s*:\s*[^;{}]+;?/i', '', $css);
            $css = preg_replace('/\blist-style\s*:\s*unset\b/i', 'list-style: none', $css);
            return $m[1] . $css . $m[3];
        },
        $content
    );
    if (is_string($replaced)) {
        $content = $replaced;
    }
}

/**
 * Remove stray closing tags for p/li/ul/ol when the stack is empty.
 */
function riester_sanitize_drop_orphan_closings($html)
{
    if (!is_string($html) || $html === '') {
        return $html;
    }

    $voidish = array('br' => true, 'hr' => true, 'img' => true, 'meta' => true, 'input' => true, 'link' => true);
    $track = array('p' => true, 'li' => true, 'ul' => true, 'ol' => true);
    $stack = array();
    $out = '';
    $offset = 0;
    $len = strlen($html);

    while ($offset < $len) {
        if (!preg_match('/<\/?([a-zA-Z][a-zA-Z0-9]*)\b[^>]*>/', $html, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $out .= substr($html, $offset);
            break;
        }

        $tagStart = $m[0][1];
        $tagHtml = $m[0][0];
        $tagName = strtolower($m[1][0]);
        $out .= substr($html, $offset, $tagStart - $offset);

        $isClose = (isset($tagHtml[1]) && $tagHtml[1] === '/');
        $isSelf = (!$isClose && (substr($tagHtml, -2) === '/>' || isset($voidish[$tagName])));

        if ($isClose && isset($track[$tagName])) {
            $idx = null;
            for ($i = count($stack) - 1; $i >= 0; $i--) {
                if ($stack[$i] === $tagName) {
                    $idx = $i;
                    break;
                }
            }
            if ($idx === null) {
                // orphan closing tag — drop it
                $offset = $tagStart + strlen($tagHtml);
                continue;
            }
            // pop through nested opens down to this tag
            $stack = array_slice($stack, 0, $idx);
            $out .= $tagHtml;
            $offset = $tagStart + strlen($tagHtml);
            continue;
        }

        if (!$isClose && !$isSelf && isset($track[$tagName])) {
            // auto-close <p> before block / another <p>
            if ($tagName === 'p' || $tagName === 'ul' || $tagName === 'ol') {
                while (!empty($stack) && end($stack) === 'p') {
                    array_pop($stack);
                    $out .= '</p>';
                }
            }
            // <li> closes previous <li>
            if ($tagName === 'li') {
                while (!empty($stack) && end($stack) === 'li') {
                    array_pop($stack);
                    $out .= '</li>';
                }
            }
            $stack[] = $tagName;
        }

        $out .= $tagHtml;
        $offset = $tagStart + strlen($tagHtml);
    }

    return $out;
}
