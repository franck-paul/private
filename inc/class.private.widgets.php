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

use Dotclear\Helper\Html\Html;

class widgetsPrivate
{
    public static function widgetLogout($w)
    {
        if (dcCore::app()->blog->settings->private->private_flag) {
            if ($w->offline) {
                return;
            }

            if (($w->homeonly == 1 && !dcCore::app()->url->isHome(dcCore::app()->url->type)) || ($w->homeonly == 2 && dcCore::app()->url->isHome(dcCore::app()->url->type))) {
                return;
            }

            $res = ($w->title ? $w->renderTitle(Html::escapeHTML($w->title)) : '') .
            '<p>' . $w->text . '</p>' .
            '<form action="' . dcCore::app()->blog->url . '" method="post">' .
            '<p class="buttons">' .
            '<input type="hidden" name="blogout" id="blogout" value="" />' .
            '<input type="submit" value="' . Html::escapeHTML($w->label) . '" class="logout" /></p>' .
                '</form>';

            return $w->renderDiv($w->content_only, 'blogout ' . $w->class, '', $res);
        }
    }
}
