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
if (!defined('DC_CONTEXT_ADMIN')) {
    exit;
}

$new_version     = $core->plugins->moduleInfo('private', 'version');
$current_version = $core->getVersion('private');

if (version_compare($current_version, $new_version, '>=')) {
    return;
}

$s = $core->blog->settings->private;

$s->put('private_flag',
    false,
    'boolean',
    'Private mode activation flag',
    true, true
);

$s->put('private_conauto_flag',
    false,
    'boolean',
    'Private mode automatic connection option',
    true, true
);

$s->put('message',
    __('<h2>Private blog</h2><p class="message">You need the password to view this blog.</p>'),
    'string',
    'Private mode public welcome message',
    true, true
);

$s->put('redirect_url',
    '',
    'string',
    'Private mode redirect URL after disconnection',
    true, true
);

$core->setVersion('private', $new_version);

return true;
