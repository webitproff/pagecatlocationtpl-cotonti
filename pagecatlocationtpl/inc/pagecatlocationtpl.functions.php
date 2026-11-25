<?php
/**
 * Filename: pagecatlocationtpl.functions.php
 * Page Category Location Templates
 *
 * Общие функции определения категории/страницы и проверки пользовательских
 * header/footer шаблонов для модуля Page.
 *
 *
 * pagecatlocationtpl plugin for Cotonti v1.+ (https://github.com/Cotonti/Cotonti), PHP 8.5+, MySQL 8.4
 * 
 *
 * Date: Nov 25Th, 2026
 * @package pagecatlocationtpl
 * @version 1.0.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/pagecatlocationtpl-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Проверяет, что код категории безопасен для использования в имени шаблона.
 *
 * Разрешены только буквы, цифры, дефис и подчёркивание. Это исключает
 * разделители каталогов, точки и попытки обхода директорий.
 */
function pagecatlocationtpl_valid_code(string $code): bool
{
    return $code !== ''
        && strlen($code) <= 128
        && preg_match('/\A[a-zA-Z0-9_-]+\z/D', $code) === 1;
}

/**
 * Возвращает путь к существующему пользовательскому шаблону.
 *
 * $kind: header или footer.
 * $suffix: пустая строка или pagehasid.
 */
function pagecatlocationtpl_template_exists(
    string $kind,
    string $cat,
    string $suffix = ''
): bool {
    if (!in_array($kind, ['header', 'footer'], true)
        || !pagecatlocationtpl_valid_code($cat)
        || !in_array($suffix, ['', 'pagehasid'], true)
    ) {
        return false;
    }

    $theme = (string) (Cot::$cfg['defaulttheme'] ?? '');
    $themesDir = (string) (Cot::$cfg['themes_dir'] ?? 'themes');

    // Имя темы задаётся администратором, но всё равно не допускаем путь.
    if (!pagecatlocationtpl_valid_code($theme)) {
        return false;
    }

    $filename = $kind . '.page.' . $cat
        . ($suffix !== '' ? '.' . $suffix : '') . '.tpl';

    $themeRoot = rtrim($themesDir, '/\\') . DIRECTORY_SEPARATOR . $theme;
    $path = $themeRoot . DIRECTORY_SEPARATOR . $filename;

    return is_file($path) && is_readable($path);
}

/**
 * Разбирает URL-путь и возвращает сегменты без query string.
 * Языковой префикс вида /ru/ или /en/ удаляется.
 */
function pagecatlocationtpl_url_segments(): array
{
    $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($requestUri, PHP_URL_PATH);

    if (!is_string($path) || $path === '') {
        return [];
    }

    $segments = array_values(array_filter(
        explode('/', trim($path, '/')),
        static fn($segment) => $segment !== ''
    ));

    foreach ($segments as &$segment) {
        $segment = rawurldecode($segment);
    }
    unset($segment);

    if (isset($segments[0]) && preg_match('/\A[a-z]{2,3}(?:-[A-Z]{2})?\z/', $segments[0])) {
        array_shift($segments);
    }

    return $segments;
}

/**
 * Получает контекст текущего запроса Page.
 *
 * Результат:
 * [
 *   'category' => string,
 *   'is_page'  => bool
 * ]
 *
 * Страница определяется по числовому ID, alias из GET или alias из ЧПУ.
 * Категория берётся из записи страницы, окружения Cotonti, ?c= или URL.
 */
function pagecatlocationtpl_get_context(): array
{
    static $context = null;

    if ($context !== null) {
        return $context;
    }

    $context = ['category' => '', 'is_page' => false];

    if (($GLOBALS['cot_pagecatlocationtpl_context_loaded'] ?? false) === true) {
        return $context;
    }
    $GLOBALS['cot_pagecatlocationtpl_context_loaded'] = true;

    if ((Cot::$env['ext'] ?? '') !== 'page') {
        return $context;
    }

    // Модуль Page обеспечивает таблицу страниц и штатные функции модуля.
    require_once cot_incfile('page', 'module');

    $segments = pagecatlocationtpl_url_segments();

    $category = '';
    if (!empty(Cot::$env['cat']) && is_string(Cot::$env['cat'])) {
        $category = Cot::$env['cat'];
    } elseif (!empty($_GET['c'])) {
        $category = (string) cot_import('c', 'G', 'TXT');
    } elseif (!empty($segments[0])) {
        $category = (string) $segments[0];
    }

    $pageRow = false;

    // 1. Legacy URL / прямой ID страницы.
    $pageId = 0;
    if (isset($_GET['id']) && is_scalar($_GET['id'])) {
        $pageId = (int) $_GET['id'];
    }

    if ($pageId > 0) {
        $pageRow = Cot::$db->query(
            'SELECT page_cat FROM ' . Cot::$db->pages . ' WHERE page_id = ? LIMIT 1',
            [$pageId]
        )->fetch();
    }

    // 2. Alias из параметра запроса.
    $alias = '';
    if (!$pageRow && !empty($_GET['al'])) {
        $alias = (string) cot_import('al', 'G', 'TXT');
    }

    // 3. SEO URL: /ru/category/page-alias
    if (!$pageRow && $alias === '' && count($segments) >= 2) {
        $alias = (string) $segments[count($segments) - 1];
    }

    if (!$pageRow && $alias !== '' && pagecatlocationtpl_valid_code($alias)) {
        if (pagecatlocationtpl_valid_code($category)) {
            $pageRow = Cot::$db->query(
                'SELECT page_cat FROM ' . Cot::$db->pages
                . ' WHERE page_alias = ? AND page_cat = ? LIMIT 1',
                [$alias, $category]
            )->fetch();
        }

        // Запасной поиск по alias, если категория не была определена.
        if (!$pageRow) {
            $pageRow = Cot::$db->query(
                'SELECT page_cat FROM ' . Cot::$db->pages
                . ' WHERE page_alias = ? LIMIT 1',
                [$alias]
            )->fetch();
        }
    }

    if (is_array($pageRow) && !empty($pageRow['page_cat'])) {
        $category = (string) $pageRow['page_cat'];
        $context['is_page'] = true;
    }

    if (pagecatlocationtpl_valid_code($category)) {
        $context['category'] = $category;
    }

    return $context;
}

/**
 * Определяет location для header/footer.
 *
 * Сначала проверяется индивидуальный шаблон страницы (.pagehasid.tpl),
 * затем общий шаблон категории. Возвращает location либо null.
 */
function pagecatlocationtpl_get_location(string $kind): ?string
{
    if (!in_array($kind, ['header', 'footer'], true)) {
        return null;
    }

    $context = pagecatlocationtpl_get_context();
    $category = $context['category'];

    if ($category === '') {
        return null;
    }

    if ($context['is_page']
        && pagecatlocationtpl_template_exists($kind, $category, 'pagehasid')
    ) {
        return 'page.' . $category . '.pagehasid';
    }

    if (pagecatlocationtpl_template_exists($kind, $category)) {
        return 'page.' . $category;
    }

    return null;
}
