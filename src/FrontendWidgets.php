<?php
/**
 * @brief private, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\private;

use dcCore;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsElement;

class FrontendWidgets
{
    public static function widgetLogout(WidgetsElement $w)
    {
        if (dcCore::app()->blog->settings->get(My::id())->private_flag) {
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
