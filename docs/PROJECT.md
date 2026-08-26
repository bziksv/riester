# riester.su — документация проекта

Bitrix-интернет-магазин медицинского оборудования **Riester** (шаблон **medical-templates**).

## Репозиторий и окружения

| | |
|---|---|
| GitHub | https://github.com/bziksv/riester |
| **Git root** | `riester.su/` (= корень сайта на prod) |
| Prod IP | `62.109.16.215` (SSH: `root@62.109.16.215`, хост `s2.prime-ltd.su`) |
| Prod path | `/var/www/riester_su_usr87/data/www/riester.su` |
| Домен | https://riester.su |
| Локально | http://127.0.0.1:8103/ |

Родительская папка `riester/` — **не в git**: дамп БД (`riester_su.sql`), `.cursorignore`.

## Структура

```
riester/                       # workspace (Mac)
├── riester_su.sql             # дамп БД (~170 MB, не в git)
├── .cursorignore
└── riester.su/                # git root
    ├── .local/                # nginx/php-fpm (Mac)
    ├── docs/                  # документация
    ├── scripts/               # dev + deploy
    ├── bitrix/
    │   ├── modules/           # в git (prime.* и ядро)
    │   └── templates/medical-templates/
    ├── local/modules/prime.alerts/
    └── upload/                # не в git
```

## Git — что в репозитории

**В git:** код сайта, `bitrix/modules/`, шаблоны, компоненты, `local/`, `scripts/`, `docs/`, `.local/*.example`.

**Не в git:** `upload/`, кэш Bitrix, секреты (`.settings.php`, `dbconn.php`, `license_key.php`, `.htaccess`), дампы `*.sql`.

Старый `.git` внутри `riester.su/` был от проекта kawe — заменён на `bziksv/riester`.

## Локальная разработка (Mac, soft)

Порты: **8103** (nginx), **9103** (php-fpm). MySQL 3306 (Homebrew `mysql@8.0`). PHP **7.4** (Homebrew `php@7.4`).

```bash
cd riester.su
cp .local/db.env.example .local/db.env   # один раз
./scripts/setup-local-db.sh --background # один раз, импорт дампа
./scripts/start-dev.sh
./scripts/stop-dev.sh
```

Soft-режим: php-fpm `ondemand`, max 2 workers, **512M** RAM.

### Занятые порты (соседние проекты)

| Порт | Проект |
|------|--------|
| 8080 | almamed |
| 8087 | oftalmag |
| 8090 | insortex |
| 8098 | lorshop |
| 8100 | medplakaty |
| 8101 | oftal-med |
| 8102 | proktolog |
| **8103** | **riester** |

## Деплой на prod

**Git после правок — всегда.** **Prod — только по явной просьбе.**

```bash
cd riester.su
git add … && git commit -m "…" && git push origin main   # после правок

# только когда пользователь просит:
./scripts/deploy-prod.sh
```

**Запрещено:** автодеплой на prod, правки на prod без commit, `scp` файлов кода.

## База данных

| | Prod | Local |
|---|---|---|
| Имя БД | `riester_su` | `riester_su` |
| Пользователь | `riester_su` | `riester_local` |
| Хост | `localhost` | `127.0.0.1` |

Дамп: `../riester_su.sql` (в корне workspace).

## Шаблон и кастомизация

- **Шаблон:** `bitrix/templates/medical-templates`
- **Каталог:** инфоблок ID `33` (`IBLOCK_CATALOG` в `bitrix/php_interface/init.php`)
- **Слайдер:** инфоблок типа `sliders`, ID `5`

### Кастомные модули

| Модуль | Назначение |
|--------|------------|
| `prime.cleaner` | Очистка/обслуживание |
| `prime.updateprice` | Обновление цен |
| `prime.roistatbitrixcms` | Интеграция Roistat |
| `prime.alerts` | Политика e-mail (`.ru`/`.su`) — в `local/modules/prime.alerts/` |

### Интеграции

- **Roistat** — модуль `prime.roistatbitrixcms`
- **RedBox visit** — скрипт `prime.visit.js` в footer шаблона
- **1С** — `hand1CtoSite.php` (обмен с 1С)

## PHP 8 и модуль lazyload

На **prod** обычно PHP 7.x. Локально используем **PHP 7.4** (`/opt/homebrew/opt/php@7.4`) — модуль `arturgolubev.lazyload` несовместим с PHP 8.3 (curly braces, static callbacks).

При необходимости PHP 8: в `bitrix/modules/arturgolubev.lazyload/` уже частично исправлен `phpQuery-onefile.php` и статические обработчики в `include.php`; полная совместимость потребует доработки `Unitools`.

## Проверка после изменений

```bash
curl -sS -o /dev/null -w '%{http_code}\n' https://riester.su/
```

Локально: `./scripts/start-dev.sh` и curl на `:8103`.

## Полезные пути

| Путь | Описание |
|------|----------|
| `/catalog/` | Каталог товаров |
| `/personal/` | Личный кабинет |
| `/about/` | О компании |
| `/kontakty/` | Контакты |
| `/articles/` | Статьи |
| `/dev/` | Dev-страница (закрыть на prod) |
