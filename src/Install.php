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
use dcNamespace;
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    protected static $init = false; /** @deprecated since 2.27 */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::INSTALL);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            // Init
            $settings = dcCore::app()->blog->settings->get(My::id());

            $settings->put('private_flag', false, dcNamespace::NS_BOOL, 'Private mode activation flag', false, true);
            $settings->put('private_conauto_flag', false, dcNamespace::NS_BOOL, 'Private mode automatic connection option', false, true);
            $settings->put('message', __('<h2>Private blog</h2><p class="message">You need the password to view this blog.</p>'), dcNamespace::NS_STRING, 'Private mode public welcome message', false, true);
            $settings->put('redirect_url', '', dcNamespace::NS_STRING, 'Private mode redirect URL after disconnection', false, true);
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
