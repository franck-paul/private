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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'PrivateMode',                       // Name
    'Protect your blog with a password', // Description
    'Osku and contributors',             // Author
    '1.11',                              // Version
    [
        'requires'    => [['core', '2.19']],                                 // Dependencies
        'permissions' => 'admin',                                            // Permissions
        'priority'    => 1501,                                               // Priority
        'type'        => 'plugin',                                           // Type

        'details'    => 'https://open-time.net/?q=private',       // Details URL
        'support'    => 'https://github.com/franck-paul/private', // Support URL
        'repository' => 'https://raw.githubusercontent.com/franck-paul/private/master/dcstore.xml'
    ]
);
