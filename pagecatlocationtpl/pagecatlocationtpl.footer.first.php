<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=footer.first
[END_COT_EXT]
==================== */

/**
 * Выбор пользовательского footer-шаблона для категории Page или отдельной
 * страницы. При найденном шаблоне сбрасывает ext, чтобы Cotonti выбрала
 * footer по Cot::$env['location'], а не footer.page.tpl.
 */
/**
 * Filename: pagecatlocationtpl.footer.first.php
 * We connect, "hang" our code on the hook ('footer.first') header.php. 
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

if ((Cot::$env['ext'] ?? '') !== 'page') {
    return;
}

require_once cot_incfile('pagecatlocationtpl', 'plug');

$location = pagecatlocationtpl_get_location('footer');

if ($location !== null) {
    Cot::$env['location'] = $location;
    Cot::$env['ext'] = '';
}
