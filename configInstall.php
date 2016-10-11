<?php
/**
 * @package Activity Bar mod
 * @version 2.0
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2015, Jessica González
 * @license http://www.mozilla.org/MPL/ MPL 2.0
 */

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

else if(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin priveleges required.');

// Prepare and insert this mod's config array.
$_config = array(
	'_availableHooks' => array(
		'memberContext' => 'integrate_member_context',
		'generalSettings' => 'integrate_general_mod_settings',
		'displayContext' => 'integrate_prepare_display_context',
		'profile' => 'integrate_load_custom_profile_fields',
	),
);

// All good.
updateSettings(array('_configActivityBar' => json_encode($_config)));

// Insert our very own version of composer autoload feature.
add_integration_function(array(
	'integrate_pre_include' => '$sourcedir/ohara/src/Suki/autoload.php',
));


if (SMF == 'SSI')
	echo 'Database changes are complete!';