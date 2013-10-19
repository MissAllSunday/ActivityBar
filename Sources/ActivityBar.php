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

require_once($sourcedir . '/Ohara.php');

class ActivityBar extends Ohara
{
	protected static $className = __CLASS__;
	protected $hooks = array();

	/**
	 * Setup the object, gather all of the relevant settings
	 */
	protected function __construct()
	{
		$this->hooks = array(
			'integrate_menu_buttons' => 'call',
			'integrate_general_mod_settings' => 'settings',
		);

		// Call the helper
		parent::__construct();
	}

	protected function settings(&$config_vars)
	{
		$config_vars[] = $this->text('title');
		$config_vars[] = array('check', self::$className .'_enable', 'subtext' => $this->text('title_sub'));
		$config_vars[] = array('check', self::$className .'_show_in_posts', 'subtext' => $this->text('show_in_posts_sub'));
		$config_vars[] = array('check', self::$className .'_show_in_profile', 'subtext' => $this->text('show_in_profile_sub'));
		$config_vars[] = array('text', self::$className .'_label', 'subtext' => $this->text('label_sub'));
		$config_vars[] = array('int', self::$className .'_timeframe', 'subtext' => $this->text('timeframe_sub'));
		$config_vars[] = array('int', self::$className .'_max_posts', 'subtext' => $this->text('max_posts_sub'));
		$config_vars[] = array('int', self::$className .'_max_width', 'subtext' => $this->text('width_sub'));
		$config_vars[] = '';
	}

	protected function call(&$menu_buttons)
	{
		global $context;

		if (isset($context['current_action']) && $context['current_action'] === 'credits')
			$context['copyrights']['mods'][] = $this->who();

		// Call the css bits
		$context['html_headers'] .= $this->css();
	}

	public function activity($user)
	{
		global $smcFunc, $context;

		/* No user, no fun */
		if (empty($user))
			return false;

		/* Safety first! */
		else
			$user = (int) $user;

		/* We already have what we need */
		if (($context[$user][self::$className] = cache_get_data(self::$className .'_' . $user,
			120)) == null)
		{
			/* Make sure everything is set. If something is missing, use a default value. */
			$max_width = $this->setting('max_width') ? $this->setting('max_width') : 139;
			$max_posts = $this->setting('max_posts') ? $this->setting('max_posts') : 500;
			$days = $this->setting('timeframe') ? $this->setting('timeframe') : 30;
			$context[$user][self::$className] = array();

			/* Calculate the starting date */
			$startingDate = time() - ($days * 86400);

			/* Get all posts posted since the starting date. */
			$request = $smcFunc['db_query']('', '
				SELECT poster_time, id_member
				FROM {db_prefix}messages
				WHERE poster_time > {int:startingdate} AND id_member = {int:user}',
				array(
					'startingdate' => $startingDate,
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
			$context[$user][self::$className] = array(
				'width' => $bar_width,
				'percentage' => round($percentage,2),
			);

			cache_put_data(self::$className .'_' . $user, $context[$user][self::$className], 120);
		}

		/* There you go. Anything else? */
		return $context[$user][self::$className];
	}

	public function activityDisplay($user)
	{
		// Get the activity bar
		$this->activity($user);

		// Done
		return array(
			'placement' => 1,
			'value' =>  template_activity_display($user),
		);
	}

	public function activityProfile($user)
	{
		// Get the activity bar
		$this->activity($user);

		// Done
		return array(
			'name' => $this->setting('label') ? $this->setting('label') : $this->text('standardlabel'),
			'placement' => 0,
			'output_html' => template_activity_profile(),
		);
	}

	protected function css()
	{
		global $settings;

		$return = '';

		/* Only show this stuff if we are on a message page or the profile */
		if($this->setting('enable') && isset($_REQUEST['topic']) || (isset($_REQUEST['action']) && $_REQUEST['action'] == 'profile'))
		{
			loadTemplate(self::$className);
			loadLanguage(self::$className);

			$return .= '
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
	}
		return $return;
	}

	/* DUH! WINNING! */
	protected function who()
	{
		return '<a href="http://missallsunday.com" title="Free SMF Mods">Activity Bar mod &copy Suki</a>';
	}
}

ActivityBar::getInstance();
