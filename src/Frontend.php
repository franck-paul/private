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
use Dotclear\Core\Process;

class Frontend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        dcCore::app()->tpl->addValue('PrivateReqPage', FrontendTemplate::PrivateReqPage(...));
        dcCore::app()->tpl->addValue('PrivateMsg', FrontendTemplate::PrivateMsg(...));

        $settings = dcCore::app()->blog->settings->private;

        if ($settings->private_flag) {
            dcCore::app()->addBehavior('publicBeforeDocumentV2', FrontendUrl::privateHandler(...));
        }

        if ($settings->private_conauto_flag) {
            dcCore::app()->addBehavior('publicPrivateFormAfterContent', FrontendBehaviors::publicPrivateFormAfterContent(...));
        }

        dcCore::app()->addBehavior('publicPrivateFormBeforeContent', FrontendBehaviors::publicPrivateFormBeforeContent(...));

        dcCore::app()->addBehavior('initWidgets', Widgets::initWidgets(...));

        return true;
    }
}
