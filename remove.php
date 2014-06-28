<?php

/**
 * @package Activity Bar mod
 * @version 1.2
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2013, Jessica González
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

/*
 * Version: MPL 1.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is http://www.marcusforsberg.net/ code.
 *
 * The Initial Developer of the Original Code is
 * Marcus Forsberg <http://www.marcusforsberg.net>.
 * Portions created by the Initial Developer are Copyright (C)
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 * Jessica González <suki@missallsunday.com>
 */

	if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
		require_once(dirname(__FILE__) . '/SSI.php');

	elseif (!defined('SMF'))
		exit('Error Cannot remove - please verify you put this in the same place as SMF\'s index.php.');

	$hooks = array(
		'integrate_member_context' => '$sourcedir/ActivityBar.php|ActivityBar::data#',
		'integrate_credits' => '$sourcedir/ActivityBar.php|ActivityBar::who#',
		'integrate_general_mod_settings' => '$sourcedir/ActivityBar.php|ActivityBar::settings#',
		'integrate_prepare_display_context' => '$sourcedir/ActivityBar.php|ActivityBar::showDisplay#',
		'integrate_load_custom_profile_fields' => '$sourcedir/ActivityBar.php|ActivityBar::showProfile#',
		'integrate_load_theme' => '$sourcedir/ActivityBar.php|ActivityBar::css#',
	);


	foreach ($hooks as $hook => $function)
		remove_integration_function($hook, $function);
