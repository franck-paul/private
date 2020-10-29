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

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "PrivateMode",                       // Name
    "Protect your blog with a password", // Description
    "Osku and contributors",             // Author
    '1.10.1',                            // Version
    [
        'requires'    => [['core', '2.16']],                                 // Dependencies
        'permissions' => 'admin',                                            // Permissions
        'priority'    => 1501,                                               // Priority
        'type'        => 'plugin',                                           // Type
        'support'     => 'https://github.com/franck-paul/private',           // Support URL
        'details'     => 'http://plugins.dotaddict.org/dc2/details/private' // Doc URL
    ]
);
