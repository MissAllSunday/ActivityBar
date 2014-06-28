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

if (!defined('SMF'))
	die('No direct access...');

// There is no autoload feature on SMF so...
require_once($sourcedir . '/Ohara.php');

class ActivityBar extends Ohara
{
	public static $name = __CLASS__;
	protected static $_activity = array();

	protected function settings(&$config_vars)
	{
		global $txt;

		$config_vars[] = $this->text('title');
		$config_vars[] = array('check', self::$name .'_enable', 'subtext' => $this->text('enable_sub'));
		$config_vars[] = array('check', self::$name .'_show_in_posts', 'subtext' => $this->text('show_in_posts_sub'));
		$config_vars[] = array('check', self::$name .'_show_in_profile', 'subtext' => $this->text('show_in_profile_sub'));
		$config_vars[] = array('text', self::$name .'_label', 'subtext' => $this->text('label_sub'));
		$config_vars[] = array('int', self::$name .'_timeframe', 'subtext' => $this->text('timeframe_sub'));
		$config_vars[] = array('int', self::$name .'_max_posts', 'subtext' => $this->text('max_posts_sub'));
		$config_vars[] = array('int', self::$name .'_max_width', 'subtext' => $this->text('max_width_sub'));

		// Option to select the placement.
		$config_vars[] = array('select', self::$name .'_placement',
			array(
				$txt['custom_edit_placement_standard'],
				$txt['custom_edit_placement_with_icons'],
				$txt['custom_edit_placement_above_signature'],
				$txt['custom_profile_placement_below_signature'],
				$txt['custom_profile_placement_below_avatar'],
				$txt['custom_profile_placement_above_name'],
				$txt['custom_profile_placement_bottom'],
			),
			'subtext' => $this->text('placement_sub'),
			'multiple' => false,
		);

		$config_vars[] = '';
	}

	public function data(&$data, $user, $display_custom_fields)
	{
		// Mod is disabled.
		if(!$this->setting('enable'))
			return;

		// If we aren't loading any custom profile field, don't bother.
		if (empty($display_custom_fields))
			return;

		// Get this user's activity.
		$activity = $this->create($user);

		// Append the data. Cheating, I'm gonna use a string key to make it easier for me to recognize this little buddy later...
		$data['custom_fields']['Activity'] = array(
			'title' => $this->setting('label') ? $this->setting('label') : $this->text('standardlabel'),
			'col_name' => $this->setting('label') ? $this->setting('label') : $this->text('standardlabel'),
			'value' => template_activity_display($activity),
			'placement' => !empty($this->setting('placement')) ? $this->setting('placement') : 0,
		);

		unset($activity);
	}

	public function showDisplay(&$output, &$message)
	{
		// Mod is disabled.
		if(!$this->setting('enable'))
			return;

		// So yeah, lets use our own little cheat... and lots and lots of empty checks!!!
		if(!$this->setting('show_in_posts') && !empty($output['member']) && !empty($output['member']['custom_fields'] && !empty($output['member']['custom_fields']['Activity'])))
			unset($output['member']['custom_fields']['Activity']);
	}

	public function showProfile($memID, $area)
	{
		global $context;

		// Eww, why do I need to abuse global scope like this... gross :(
		if (empty($area) && $this->setting('show_in_profile'))
		{
			// Get this user's activity.
			$activity = $this->create($user);

			$context['custom_fields'][] = array(
				'name' => $this->setting('label') ? $this->setting('label') : $this->text('standardlabel'),
				'placement' => 0,
				'output_html' => template_activity_profile($activity),
				'show_reg' => false,
			);
		}
	}

	public function create($user)
	{
		global $smcFunc;

		// Meh...
		if (empty($user))
			return false;

		else
			$user = (int) $user;

		// The much needed css file.
		loadCSSFile('activity.css');

		// We already have what we need.
		if (!empty(self::$_activity[$user]))
			return self::$_activity[$user];

		if ((self::$_activity[$user] = cache_get_data(self::$name .'_' . $user,
			120)) == null)
		{
			// Make sure everything is set. If something is missing, use a default value.
			$maxWidth = $this->setting('max_width') ? $this->setting('max_width') : 139;
			$maxPosts = $this->setting('max_posts') ? $this->setting('max_posts') : 500;
			$days = $this->setting('timeframe') ? $this->setting('timeframe') : 30;
			self::$_activity[$user] = array();

			// Calculate the starting date.
			$startingDate = time() - ($days * 86400);

			// Get all posts posted since the starting date.
			$request = $smcFunc['db_query']('', '
				SELECT poster_time, id_member
				FROM {db_prefix}messages
				WHERE poster_time > {int:startingdate} AND id_member = {int:user}',
				array(
					'startingdate' => $startingDate,
					'user' => $user,
				)
			);

			// Count the posts.
			$posts = $smcFunc['db_num_rows']($request);

			$smcFunc['db_free_result']($request);

			// Calculate everything.
			$numPosts = $posts / $maxPosts;
			$numPosts = $numPosts > 1 ? 1 : $numPosts;
			$percentage = $numPosts * 100;
			$barWidth = $maxWidth * $numPosts;

			// Store the result in a array.
			self::$_activity[$user] = array(
				'width' => $barWidth,
				'percentage' => round($percentage,2),
			);

			cache_put_data(self::$name .'_' . $user, self::$_activity[$user], 120);
		}

		// There you go. Anything else?
		return self::$_activity[$user];
	}

	/* DUH! WINNING! */
	public function who()
	{
		global $context;

		if (isset($context['current_action']) && $context['current_action'] === 'credits')
			$context['copyrights']['mods'][] = '<a href="http://missallsunday.com" title="Free SMF Mods">Activity Bar mod &copy Suki</a>';
	}
}
