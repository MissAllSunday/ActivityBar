<?php

declare(strict_types=1);


if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
    require_once(dirname(__FILE__) . '/SSI.php');

elseif (!defined('SMF'))
    exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $context;

$hookCall = !empty($context['uninstalling']) ? 'remove_integration_function' : 'add_integration_function';

// Everybody likes hooks
$hooks = [
    'integrate_pre_include' => '$sourcedir/ActivityBar.php',
    'integrate_member_context' => 'ActivityBar::addMemberContext#',
    'integrate_general_mod_settings' => 'ActivityBar::addGeneralSettings#',
    'integrate_prepare_display_context' => 'ActivityBar::addDisplayContext#',
    'integrate_load_custom_profile_fields' => 'ActivityBar::addProfile#',
];

foreach ($hooks as $hook => $function)
    $hookCall($hook, $function);