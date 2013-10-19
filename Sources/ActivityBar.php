<?php

/**
 * @package Activity Bar mod
 * @version 1.2
 * @author Jessica González <missallsunday@simplemachines.org>
 * @copyright Copyright (c) 2011, Jessica González
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
 * Jessica González <missallsunday@simplemachines.org>
 */

if (!defined('SMF'))
	die('No direct access...');

class ActivityBar
{
	protected static $__CLASS__ = __CLASS__;

	/**
	 * Setup the object, gather all of the relevant settings
	 */
	protected function __construct()
	{
		$this->hooks = array(
			'integrate_menu_buttons',
		);

		// Call the helper
		parent::__construct();
	}

	public static function settings(&$config_vars)
	{
		global $txt;

		$config_vars[] = $txt['_title'];
		$config_vars[] = array('check', 'ActivityBar_enable');
		$config_vars[] = array('check', 'ActivityBar_show_in_posts');
		$config_vars[] = array('check', 'ActivityBar_show_in_profile');
		$config_vars[] = array('text', 'ActivityBar_label');
		$config_vars[] = array('int', 'ActivityBar_timeframe');
		$config_vars[] = array('int', 'ActivityBar_max_posts');
		$config_vars[] = array('int', 'ActivityBar_max_width');
		$config_vars[] = '';
	}

	public function activity($user)
	{
		global $modSettings, $smcFunc, $context;

		/* No user, no fun */
		if (empty($user))
			return false;

		/* Safety first! */
		else
			$user = (int) $user;

		/* We already have what we need */
		if ( isset($context[$user]['ActivityBar']) && !empty($context[$user]['ActivityBar']))
			return $context[$user]['ActivityBar'];

		/* No? then get it!!! */
		else
		{
			/* Make sure everything is set. If something is missing, use a default value. */
			$max_width = !empty($modSettings['ActivityBar_max_width']) ? $modSettings['ActivityBar_max_width'] : 139;
			$max_posts = !empty($modSettings['ActivityBar_max_posts']) ? $modSettings['ActivityBar_max_posts'] : 500;
			$days = !empty($modSettings['ActivityBar_timeframe']) ? $modSettings['ActivityBar_timeframe'] : 30;
			$context[$user]['ActivityBar'] = array();

			/* Calculate the starting date */
			$startingdate = time() - ($days * 86400);

			/* Get all posts posted since the starting date. */
			$request = $smcFunc['db_query']('', '
				SELECT poster_time, id_member
				FROM {db_prefix}messages
				WHERE poster_time > {int:startingdate} AND id_member = {int:user}',
				array(
					'startingdate' => $startingdate,
					'user' => $user,
				)
			);

			/* Count the posts. */
			$posts = $smcFunc['db_num_rows']($request);

			$smcFunc['db_free_result']($request);

			/* Calculate everything. */
			$num_posts = $posts / $max_posts;
			$num_posts = $num_posts > 1 ? 1 : $num_posts;
			$percentage = $num_posts * 100;
			$bar_width = $max_width * $num_posts;

			/* Store the result in a array. */
			$context[$user]['ActivityBar'] = array(
				'width' => $bar_width,
				'percentage' => round($percentage,2),
			);

			/* There you go. Anything else? */
			return $context[$user]['ActivityBar'];
		}
	}

	public static function css()
	{
		global $modSettings, $settings;

		$return = '';

		/* Only show this stuff if we are on a message page or the profile */
		if(!empty($modSettings['ActivityBar_enable']) && isset($_REQUEST['topic']) || isset($_REQUEST['action']) && $_REQUEST['action'] == 'profile')
			$return = '
<style type="text/css">
.activity_holder
{
	height: 15px;
	border: 1px solid #9BAEBF;
}

.ActivityBar
{
	height: 15px;
	background: url('. $settings['default_theme_url'] .'/images/theme/main_block.png) 90% -200px;
}

.activity_percentage
{
	height: 15px;
	color: #333333;
	text-align: center;
}
</style>
';
		return $return;
	}

	/* DUH! WINNING! */
	public static function who()
	{
		$MAS = '<a href="http://missallsunday.com" title="Free SMF Mods">Activity Bar mod &copy Suki</a>';

		return $MAS;
	}
}

ActivityBar::getInstance();
