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
use dcNsProcess;

class Frontend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::FRONTEND);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->tpl->addValue('PrivateReqPage', [FrontendTemplate::class, 'PrivateReqPage']);
        dcCore::app()->tpl->addValue('PrivateMsg', [FrontendTemplate::class, 'PrivateMsg']);

        $settings = dcCore::app()->blog->settings->private;

        if ($settings->private_flag) {
            dcCore::app()->addBehavior('publicBeforeDocumentV2', [FrontendUrl::class, 'privateHandler']);
        }

        if ($settings->private_conauto_flag) {
            dcCore::app()->addBehavior('publicPrivateFormAfterContent', [FrontendBehaviors::class, 'publicPrivateFormAfterContent']);
        }

        dcCore::app()->addBehavior('publicPrivateFormBeforeContent', [FrontendBehaviors::class, 'publicPrivateFormBeforeContent']);

        dcCore::app()->addBehavior('initWidgets', [Widgets::class, 'initWidgets']);

        return true;
    }
}
