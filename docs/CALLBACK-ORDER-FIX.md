# «Заказать звонок» — перенос с kawe.su на riester.su

Bitrix, шаблон `medical-templates`, компонент `nbrains:main.feedback`, шаблон `popup-callback`.

## Эталон

| | |
|---|---|
| GitHub | https://github.com/bziksv/kawe |
| Коммиты | `8d80f9a`, `94cd289`, `b9778c9`, `c30524d`, `d51c475`, `da2b3fc`, `5382fc82` |
| Локально | `/Users/stanislav/Documents/projects/kawe/kawe.su` (порт 8097) |

## riester.su — параметры проекта

| Параметр | Значение |
|----------|----------|
| Шаблон | `medical-templates` |
| `IBLOCK_ID` | `37` |
| `EVENT_NAME` | `CALLBACK` |
| `EVENT_MESSAGE_ID` | `53` |
| `EMAIL_TO` (footer) | `info@riester.su` |
| Planfix (шаблон 53) | `riester@almamed.planfix.ru` |
| Заказы BCC | `riester@almamed.planfix.ru` (шаблон 29) |
| Согласие ПД | `/upload/legal/legal-consent.png` |
| Политика ПД | `/upload/legal/legal-personal-data.png` |
| Cookie | `/upload/legal/legal-cookie.png` |
| Реком. технологии | `/upload/legal/legal-recommendation.png` |
| Локально | http://127.0.0.1:8103/ |

## Что изменено (2026-08-26)

```
bitrix/components/nbrains/main.feedback/component.php
bitrix/templates/medical-templates/components/nbrains/main.feedback/popup-callback/template.php
bitrix/templates/medical-templates/components/nbrains/main.feedback/popup-callback/style.css
bitrix/templates/medical-templates/js/functions.js          (блок callback)
bitrix/templates/medical-templates/footer.php
bitrix/php_interface/init.php                               (BX_COMPOSITE_DISABLED)
index.php                                                   (убран блок «только GET» — ломал POST с главной)
tools/migrations/2026-08-26-callback-email-planfix.sql
```

### SQL (prod)

```sql
UPDATE b_event_message
SET EMAIL_TO = 'riester@almamed.planfix.ru'
WHERE ID = 53 AND EVENT_NAME = 'CALLBACK';
```

Локально уже применено. На prod — после деплоя кода.

## Критерий готовности

Пользователь **всегда** видит один из трёх исходов:
1. Ошибка валидации — форма на месте
2. Ошибка сервера — текст ошибки
3. Успех — явное сообщение

Письмо/заявка доходит **туда же, куда заказы** (`riester@almamed.planfix.ru`).

## Типичные ошибки

| Симптом | Причина | Фикс |
|---------|---------|------|
| Форма очистилась, тишина | AJAX + LocalRedirect | component.php: `$isAjax` |
| Форма очистилась, тишина | Нет `submit` в POST | functions.js: `serializeArray + push` |
| HTTP 404 на главной | `index.php` блокирует POST | убрать GET-only |
| Инкогнито не работает | Пустой sessid в кеше | `COMPOSITE_FRAME_MODE=N` + `ensureBitrixSessid` |
| «Обновите страницу» | ROI_VISIT в hash | unset из `$hashParams` |
| Успех, но письма нет в Planfix | EMAIL_TO = `#DEFAULT_EMAIL_FROM#` | SQL → Planfix |
| SUCCESS_EXEC=Y, Planfix пуст | Смотрите не туда | диагностика B4 |

## curl-тест (локально)

```bash
HTML=$(curl -sS -c /tmp/cookies.txt http://127.0.0.1:8103/)
SESSID=$(echo "$HTML" | sed -n "s/.*'bitrix_sessid':'\([^']*\)'.*/\1/p" | head -1)
HASH=$(echo "$HTML" | sed -n 's/.*id="callback" data-params-hash="\([^"]*\)".*/\1/p' | head -1)

# Без consent → ошибка
curl -sS -b /tmp/cookies.txt -X POST http://127.0.0.1:8103/ \
  -H "X-Requested-With: XMLHttpRequest" \
  -d "submit=Отправить&PARAMS_HASH=${HASH}&sessid=${SESSID}&NAME=Test&PHONE=%2B79999999999&MAIL=t@test.ru&QUERY=test&URL=/" \
  | grep -o 'errortext\|согласие'

# С consent → успех
curl -sS -b /tmp/cookies.txt -X POST http://127.0.0.1:8103/ \
  -H "X-Requested-With: XMLHttpRequest" \
  -d "submit=Отправить&PARAMS_HASH=${HASH}&sessid=${SESSID}&NAME=Test&PHONE=%2B79999999999&MAIL=t@test.ru&QUERY=test&URL=/&callback-consent=on" \
  | grep -o 'mf-ok-text'
```

## Деплой на prod

1. `git commit` + `git push origin main`
2. `./scripts/deploy-prod.sh` (по запросу)
3. SQL на prod: `tools/migrations/2026-08-26-callback-email-planfix.sql`
4. Очистить кеш:
   ```bash
   rm -rf bitrix/cache/* bitrix/managed_cache/* bitrix/stack_cache/* bitrix/html_pages/*
   ```

## Полный промпт для других проектов

Универсальная инструкция — см. исходный промпт в чате или скопировать блоки A–E из kawe с адаптацией `<TEMPLATE>`, `IBLOCK_ID`, Planfix-адреса.
