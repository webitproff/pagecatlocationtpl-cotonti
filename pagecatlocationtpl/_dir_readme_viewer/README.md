# Page Category Location Templates (`pagecatlocationtpl`)

- `pagecatlocationtpl` plugin for Cotonti v1.+ (https://github.com/Cotonti/Cotonti), PHP 8.5+, MySQL 8.4
- Date: Nov 25th, 2026
- package `pagecatlocationtpl`
- version 1.0.0
- author webitproff
- copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/pagecatlocationtpl-cotonti
- license BSD

A plugin for [Cotonti Siena](https://github.com/Cotonti/Cotonti) that allows connecting custom `header` and `footer` templates for categories of the **Page** module and individual pages inside those categories.

Works when `$cfg['enablecustomhf'] = true` is enabled.

---

## Features

| Page type                 | Header template                            | Footer template                            |
|---------------------------|--------------------------------------------|--------------------------------------------|
| Category list `news`      | `header.page.news.tpl`                     | `footer.page.news.tpl`                     |
| Individual page in `news` | `header.page.news.pagehasid.tpl` (priority) → `header.page.news.tpl` | `footer.page.news.pagehasid.tpl` (priority) → `footer.page.news.tpl` |

- Support for SEO-friendly URLs and classic URLs (`?c=`, `?id=`, `?al=`).
- Automatic fallback: if `*.pagehasid.tpl` is missing, the category template is used.
- No modifications to the Cotonti core.
- Safe input handling (`cot_import`, prepared statements).

---

## Requirements

- Cotonti ≥ 1
- PHP ≥ 8.5
- `page` module
- In `datas/config.php`: `$cfg['enablecustomhf'] = true;`

---

## Installation

1. Copy the `pagecatlocationtpl` folder into `plugins/`.
2. In the admin panel: **Extensions → Plugins** → activate **Page Category Location Templates**.
3. Create the required templates in the theme folder, for example:

```
themes/your_theme/
├── header.page.news.tpl
├── header.page.news.pagehasid.tpl
├── footer.page.news.tpl
└── footer.page.news.pagehasid.tpl
```

4. Clear the Cotonti cache (Admin Panel → Tools → Clear cache).

---

## How it works

1. The `header.first` / `footer.first` hook fires before the template is selected.
2. The plugin determines the current category:
   - by `?id=` / `?al=` (page) → the page category from the database;
   - by `?c=` (list);
   - by the first SEO URL segment after the language.
3. It checks whether the template file exists on disk.
4. It sets `Cot::$env['location']` to a value like `page.news` or `page.news.pagehasid`.
5. `system/header.php` / `system/footer.php` loads the corresponding `header.$location.tpl` / `footer.$location.tpl`.

---

## URL examples

| URL                                      | Result (if templates exist)                            |
|------------------------------------------|--------------------------------------------------------|
| `/ru/news`                               | `header.page.news.tpl` + `footer.page.news.tpl`        |
| `/ru/news/my-article` (or `?id=42`)      | `header.page.news.pagehasid.tpl` (or fallback)         |
| `?c=news`                                | `header.page.news.tpl`                                 |
| `?id=42` (page in the news category)     | `header.page.news.pagehasid.tpl`                       |

---

## Plugin structure

```
plugins/pagecatlocationtpl/
├── pagecatlocationtpl.setup.php          # Metadata
├── pagecatlocationtpl.functions.php      # Template checking and category detection functions
├── pagecatlocationtpl.header.first.php   # header.first hook
├── pagecatlocationtpl.footer.first.php   # footer.first hook
├── lang/
│   ├── pagecatlocationtpl.en.lang.php
│   └── pagecatlocationtpl.ru.lang.php
└── README.md
```

---

## License

BSD © 2026 webitproff

GitHub: https://github.com/webitproff

---  


# Page Category Location Templates (`pagecatlocationtpl`)

- pagecatlocationtpl plugin for Cotonti v1.+ (https://github.com/Cotonti/Cotonti), PHP 8.5+, MySQL 8.4
- Date: Nov 25Th, 2026
- package pagecatlocationtpl
- version 1.0.0
- author webitproff
- copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/pagecatlocationtpl-cotonti
- license BSD

Плагин для [Cotonti Siena](https://github.com/Cotonti/Cotonti), который позволяет подключать кастомные `header` и `footer` шаблоны для категорий модуля **Page** и отдельных страниц внутри этих категорий.

Работает при включённом `$cfg['enablecustomhf'] = true`.

---

## Возможности

| Тип страницы              | Шаблон header                              | Шаблон footer                              |
|---------------------------|--------------------------------------------|--------------------------------------------|
| Список категории `news`   | `header.page.news.tpl`                     | `footer.page.news.tpl`                     |
| Отдельная страница в `news` | `header.page.news.pagehasid.tpl` (приоритет) → `header.page.news.tpl` | `footer.page.news.pagehasid.tpl` (приоритет) → `footer.page.news.tpl` |

- Поддержка SEO-ЧПУ и классических URL (`?c=`, `?id=`, `?al=`).
- Автоматический fallback: если `*.pagehasid.tpl` отсутствует — используется шаблон категории.
- Без изменений ядра Cotonti.
- Безопасная обработка входных данных (cot_import, prepared statements).

---

## Требования

- Cotonti ≥ 1
- PHP ≥ 8.5
- Модуль `page`
- В `datas/config.php`: `$cfg['enablecustomhf'] = true;`

---

## Установка

1. Скопируйте папку `pagecatlocationtpl` в `plugins/`.
2. В админке: **Расширения → Плагины** → активируйте **Page Category Location Templates**.
3. Создайте нужные шаблоны в папке темы, например:

```
themes/your_theme/
├── header.page.news.tpl
├── header.page.news.pagehasid.tpl
├── footer.page.news.tpl
└── footer.page.news.pagehasid.tpl
```

4. Очистите кэш Cotonti (Админка → Инструменты → Очистить кэш).

---

## Как это работает

1. Хук `header.first` / `footer.first` срабатывает до выбора шаблона.
2. Плагин определяет текущую категорию:
   - по `?id=` / `?al=` (страница) → категория страницы из БД;
   - по `?c=` (список);
   - по первому сегменту SEO-URL после языка.
3. Проверяет наличие файла шаблона на диске.
4. Устанавливает `Cot::$env['location']` в значение вида `page.news` или `page.news.pagehasid`.
5. `system/header.php` / `system/footer.php` загружает соответствующий `header.$location.tpl` / `footer.$location.tpl`.

---

## Примеры URL

| URL                                      | Результат (если шаблоны есть)                          |
|------------------------------------------|--------------------------------------------------------|
| `/ru/news`                               | `header.page.news.tpl` + `footer.page.news.tpl`        |
| `/ru/news/my-article` (или `?id=42`)     | `header.page.news.pagehasid.tpl` (или fallback)        |
| `?c=news`                                | `header.page.news.tpl`                                 |
| `?id=42` (страница в категории news)     | `header.page.news.pagehasid.tpl`                       |

---

## Структура плагина

```
plugins/pagecatlocationtpl/
├── pagecatlocationtpl.setup.php          # Метаданные
├── pagecatlocationtpl.functions.php      # Функции проверки шаблонов и определения категории
├── pagecatlocationtpl.header.first.php   # Хук header.first
├── pagecatlocationtpl.footer.first.php   # Хук footer.first
├── lang/
│   ├── pagecatlocationtpl.en.lang.php
│   └── pagecatlocationtpl.ru.lang.php
└── README.md
```

---

## Лицензия

BSD © 2026 webitproff

GitHub: https://github.com/webitproff
