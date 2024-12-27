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

use ArrayObject;

class BackendBehaviors
{
    /**
     * @param      ArrayObject<string, mixed>  $rte
     */
    public static function adminRteFlags(ArrayObject $rte): string
    {
        $rte['private'] = [true, __('Private mode message')];

        return '';
    }
}
