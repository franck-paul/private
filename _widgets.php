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
class privateWidgets
{
    public static function initWidgets($w)
    {
        $w
            ->create('privateblog', __('PrivateMode'), ['widgetsPrivate', 'widgetLogout'], null, __('Blog logout'))
            ->addTitle(__('Blog logout'))
            ->setting('text', __('Text:'), '', 'textarea')
            ->setting('label', __('Button:'), __('Disconnect'))
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();
    }
}

dcCore::app()->addBehavior('initWidgets', [privateWidgets::class, 'initWidgets']);
