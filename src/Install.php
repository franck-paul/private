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
use Exception;

class Install
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            // Init
            $settings = My::settings();

            $settings->put('private_flag', false, App::blogWorkspace()::NS_BOOL, 'Private mode activation flag', false, true);
            $settings->put('private_conauto_flag', false, App::blogWorkspace()::NS_BOOL, 'Private mode automatic connection option', false, true);
            $settings->put('message', __('<h2>Private blog</h2><p class="message">You need the password to view this blog.</p>'), App::blogWorkspace()::NS_STRING, 'Private mode public welcome message', false, true);
            $settings->put('redirect_url', '', App::blogWorkspace()::NS_STRING, 'Private mode redirect URL after disconnection', false, true);
        } catch (Exception $exception) {
            App::error()->add($exception->getMessage());
        }

        return true;
    }
}
