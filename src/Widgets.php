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

use Dotclear\Plugin\widgets\WidgetsStack;

class Widgets
{
    public static function initWidgets(WidgetsStack $w)
    {
        $w
            ->create('privateblog', __('PrivateMode'), [FrontendWidgets::class, 'widgetLogout'], null, __('Blog logout'))
            ->addTitle(__('Blog logout'))
            ->setting('text', __('Text:'), '', 'textarea')
            ->setting('label', __('Button:'), __('Disconnect'))
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();
    }
}
