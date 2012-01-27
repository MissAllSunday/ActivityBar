<?php

/**
 * @package Activity Bar mod
 * @version 1.1
 * @author Jessica Gonz�lez <missallsunday@simplemachines.org>
 * @copyright Copyright (c) 2011, Jessica Gonz�lez
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
 * Jessica Gonz�lez <missallsunday@simplemachines.org>
 */

if (!defined('SMF'))
	die('Hacking attempt...');

	function Activity_Bar_settings(&$config_vars)
	{
		global $txt;

		$config_vars[] = $txt['Activity_Bar_title'];
		$config_vars[] = array('check', 'Activity_Bar_enable');
		$config_vars[] = array('check', 'Activity_Bar_show_in_posts');
		$config_vars[] = array('check', 'Activity_Bar_show_in_profile');
		$config_vars[] = array('text', 'Activity_Bar_label');
		$config_vars[] = array('int', 'Activity_Bar_timeframe');
		$config_vars[] = array('int', 'Activity_Bar_max_posts');
		$config_vars[] = array('int', 'Activity_Bar_max_width');
		$config_vars[] = '';
	}

	function Activity_Bar($user)
	{
		global $modSettings, $smcFunc, $context;

		/* No user, no fun */
		if (empty($user))
			return false;

		/* Safety first! */
		else
			$user = (int) $user;

		/* We already have what we need */
		if ( isset($context[$user]['activity_bar']) && !empty($context[$user]['activity_bar']))
			return $context[$user]['activity_bar'];

		/* No? then get it!!! */
		else
		{
			/* Make sure everything is set. If something is missing, use a default value. */
			$max_width = !empty($modSettings['Activity_Bar_max_width']) ? $modSettings['Activity_Bar_max_width'] : 139;
			$max_posts = !empty($modSettings['Activity_Bar_max_posts']) ? $modSettings['Activity_Bar_max_posts'] : 500;
			$days = !empty($modSettings['Activity_Bar_timeframe']) ? $modSettings['Activity_Bar_timeframe'] : 30;
			$context[$user]['activity_bar'] = array();

			/* Calculate the startingdate */
			$startingdate = time() - ($days * 86400);

			/* Get all posts posted since the startingdate. */
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
			$context[$user]['activity_bar'] = array(
				'width' => $bar_width,
				'percentage' => round($percentage,2),
			);

			/* There you go. Anything else? */
			return $context[$user]['activity_bar'];
		}
	}

	function Activity_Bar_css()
	{
		global $modSettings, $settings;

		$return = '';

		/* Only show this sutff if we are on a message page or the profile */
		if(!empty($modSettings['Activity_Bar_enable']) && isset($_REQUEST['topic']) || isset($_REQUEST['action']) && $_REQUEST['action'] == 'profile')
			$return = '
<style type="text/css">
.activity_holder
{
	height: 15px;
	border: 1px solid #9BAEBF;
}

.activity_bar
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
	function Activity_Bar_Who()
	{
		$MAS = '<a href="http://missallsunday.com" title="Free SMF Mods">Activity Bar mod &copy Suki</a>';

		return $MAS;
	}