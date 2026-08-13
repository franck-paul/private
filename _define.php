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
declare(strict_types=1);

if (isset($this) && is_object($this) && method_exists($this, 'registerModule') && isset($this->id) && is_string($this->id)) {
    $this->registerModule(
        'PrivateMode',
        'Protect your blog with a password',
        'Osku and contributors',
        '8.0',
        [
            'date'     => '2026-08-03T10:09:02+0200',
            'requires' => [
                ['core', '2.39'],
                ['TemplateHelper'],
            ],
            'permissions' => 'My',
            'priority'    => 1501,
            'type'        => 'plugin',

            'details'    => 'https://open-time.net/?q=private',
            'support'    => 'https://github.com/franck-paul/private',
            'repository' => 'https://raw.githubusercontent.com/franck-paul/private/main/dcstore.xml',
            'license'    => 'gpl2',
        ]
    );
}
