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
$this->registerModule(
    'PrivateMode',
    'Protect your blog with a password',
    'Osku and contributors',
    '5.1',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => 'My',
        'priority'    => 1501,
        'type'        => 'plugin',

        'details'    => 'https://open-time.net/?q=private',
        'support'    => 'https://github.com/franck-paul/private',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/private/master/dcstore.xml',
    ]
);
