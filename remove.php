<?php

/**
 * @package Activity Bar mod
 * @version 2.0
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2014, Jessica González
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

	if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
		require_once(dirname(__FILE__) . '/SSI.php');

	elseif (!defined('SMF'))
		exit('Error Cannot remove - please verify you put this in the same place as SMF\'s index.php.');

	$hooks = array(
		'integrate_pre_load' => '$sourcedir/ActivityBar.php|ActivityBar::runTimeHooks#',
	);


	foreach ($hooks as $hook => $function)
		remove_integration_function($hook, $function);
