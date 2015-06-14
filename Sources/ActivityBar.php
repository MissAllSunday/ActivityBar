<?php

/**
 * @package Activity Bar mod
 * @version 2.0
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2015, Jessica González
 * @license http://www.mozilla.org/MPL/ MPL 2.0
 */

if (!defined('SMF'))
	die('No direct access...');

// Use Ohara! manually :(
require_once ($sourcedir .'/ohara/src/Suki/Ohara.php');

class ActivityBar extends Suki\Ohara
{
	public $name = __CLASS__;
	protected static $_activity = array();
	protected $_fieldPlacement = 0;
	protected $_fieldLabel = '';

	// Define the hooks we are going to use
	protected $_availableHooks = array(
		'memberContext' => 'integrate_member_context',
		'generalSettings' => 'integrate_general_mod_settings',
		'displayContext' => 'integrate_prepare_display_context',
		'profile' => 'integrate_load_custom_profile_fields',
		'css' => 'integrate_load_theme',
	);

	public function __construct()
	{
		$this->setRegistry();

		$this->_fieldPlacement = $this->enable('placement') && $this->setting('placement') != 0 ? (int) $this->setting('placement') : 0;
		$this->_fieldLabel = $this->enable('label') ? $this->setting('label') : $this->text('standardlabel');
	}

	public function addGeneralSettings(&$config_vars)
	{
		global $txt;

		$config_vars[] = $this->text('modName');
		$config_vars[] = array('check', $this->name .'_enable', 'subtext' => $this->text('enable_sub'));
		$config_vars[] = array('check', $this->name .'_show_in_posts', 'subtext' => $this->text('show_in_posts_sub'));
		$config_vars[] = array('check', $this->name .'_show_in_profile', 'subtext' => $this->text('show_in_profile_sub'));
		$config_vars[] = array('text', $this->name .'_label', 'subtext' => $this->text('label_sub'));
		$config_vars[] = array('int', $this->name .'_timeframe', 'subtext' => $this->text('timeframe_sub'));
		$config_vars[] = array('int', $this->name .'_max_posts', 'subtext' => $this->text('max_posts_sub'));
		$config_vars[] = array('int', $this->name .'_max_width', 'subtext' => $this->text('max_width_sub'));

		// Option to select the placement.
		$config_vars[] = array('select', $this->name .'_placement',
			array(
				$txt['custom_profile_placement_standard'],
				$txt['custom_profile_placement_icons'],
				$txt['custom_profile_placement_above_signature'],
				$txt['custom_profile_placement_below_signature'],
				$txt['custom_profile_placement_below_avatar'],
				$txt['custom_profile_placement_above_member'],
				$txt['custom_profile_placement_bottom_poster'],
			),
			'subtext' => $this->text('placement_sub'),
			'multiple' => false,
		);

		$config_vars[] = '';
	}

	public function addMemberContext(&$data, $user, $display_custom_fields)
	{
		// Mod is disabled or we aren't loading any custom profile field, don't bother.
		if(!$this->setting('enable') || empty($display_custom_fields))
			return;

		loadTemplate($this->name);

		// Get this user's activity.
		$activity = $this->getActivity($user);

		// Append some vars.
		$activity['placement'] = $this->_fieldPlacement;
		$activity['label'] = $this->_fieldLabel;

		// Append the data. Cheating, I'm gonna use a string key to make it easier for me to recognize this little buddy later...
		$data['custom_fields'][] = array(
			'title' => $activity['label'],
			'col_name' => $this->setting('label') ? $this->setting('label') : $this->text('standardlabel'),
			'value' => template_activity_display($activity),
			'placement' => $activity['placement'],
		);

		unset($activity);
	}

	public function addDisplayContext(&$output, &$message)
	{
		global $context;

		// Mod is disabled or the user does want to show this field on users posts.
		if(!$this->enable('enable') || $this->enable('show_in_posts'))
			return;

		// This is going to be awkward... need to know the placement to properly unset our field...
		$placement = $context['cust_profile_fields_placement'][$this->_fieldPlacement];

		$fieldArray = $output['custom_fields'][$placement];

		if (!empty($fieldArray))
			foreach ($output['custom_fields'][$placement] as $k => $v)
				if ($v['title'] == $this->_fieldLabel)
					unset($output['custom_fields'][$placement][$k]);
	}

	public function addProfile($memID, $area)
	{
		global $context;

		// Mod is disabled.
		if(!$this->setting('enable'))
			return;

		// Eww, why do I need to abuse global scope like this... gross :(
		if ($area == 'summary' && $this->setting('show_in_profile'))
		{
			// Get this user's activity.
			$activity = $this->create($memID);

			loadTemplate($this->name);

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

		// We already have what we need.
		if (!empty(static::$_activity[$user]))
			return static::$_activity[$user];

		if ((static::$_activity[$user] = cache_get_data($this->name .'_' . $user,
			120)) == null)
		{
			// Make sure everything is set. If something is missing, use a default value.
			$maxWidth = $this->enable('max_width') ? $this->setting('max_width') : 139;
			$maxPosts = $this->enable('max_posts') ? $this->setting('max_posts') : 500;
			$days = $this->enable('timeframe') ? $this->setting('timeframe') : 30;
			static::$_activity[$user] = array();

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
			static::$_activity[$user] = array(
				'width' => $barWidth,
				'percentage' => round($percentage,2),
				'post' => $numPosts,
				'realPost' => $posts,
			);

			cache_put_data($this->name .'_' . $user, static::$_activity[$user], 120);
		}

		// There you go. Anything else?
		return static::$_activity[$user];
	}

	public function getActivity($user = 0)
	{
		if ($user && empty(static::$_activity[$user]))
			$this->create($user);

		return $user ? static::$_activity[$user] : static::$_activity;
	}

	public function addCss()
	{
		// The much needed css file. Load it fom the default theme.
		loadCSSFile('activity.css', array('force_current' => false, 'validate' => true));
	}
}
