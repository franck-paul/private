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

use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;

class Frontend
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        App::frontend()->template()->addValue('PrivateReqPage', FrontendTemplate::PrivateReqPage(...));
        App::frontend()->template()->addValue('PrivateMsg', FrontendTemplate::PrivateMsg(...));

        $settings = My::settings();

        if ($settings->private_flag) {
            App::behavior()->addBehavior('publicBeforeDocumentV2', FrontendUrl::privateHandler(...));
        }

        if ($settings->private_conauto_flag) {
            App::behavior()->addBehavior('publicPrivateFormAfterContent', FrontendBehaviors::publicPrivateFormAfterContent(...));
        }

        App::behavior()->addBehavior('publicPrivateFormBeforeContent', FrontendBehaviors::publicPrivateFormBeforeContent(...));

        App::behavior()->addBehavior('initWidgets', Widgets::initWidgets(...));

        return true;
    }
}
