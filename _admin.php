<?php
/**
 * @brief PrivateMode, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Osku and contributors
 *
 * @copyright Osku
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

$icon_img = $core->blog->settings->private->private_flag ? 'index.php?pf=private/icon-alt.png' : 'index.php?pf=private/icon.png';

$_menu['Blog']->addItem(__('Private mode'),
    'plugin.php?p=private', $icon_img,
    preg_match('/plugin.php\?p=private(&.*)?$/', $_SERVER['REQUEST_URI']),
    $core->auth->check('admin', $core->blog->id)
);
