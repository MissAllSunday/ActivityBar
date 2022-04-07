<?php

declare(strict_types=1);

/**
 * @package Activity Bar mod
 * @version 2.0
 * @author Michel Mendiola <suki@missallsunday.com>
 * @copyright Copyright (c), Michel Mendiola
 * @license http://www.mozilla.org/MPL/ MPL 2.0
 */

if (!defined('SMF'))
	die('No direct access...');

class ActivityBar
{
    protected  const CACHE_TIME = 900;
    protected const MAX_POSTS = 500;
    protected const MAX_WIDTH = 139;
    protected const DAYS = 30;
    protected const DEFAULT_COLOR = 'blue';

    public string $name = __CLASS__;
    protected array $activity = [];
    protected string $fieldLabel = '';
    protected int $fieldPlacement = 0;

    public function __construct()
	{
		$this->fieldPlacement = (int) $this->setting('placement', 0);
		$this->fieldLabel = (string) $this->setting('label', $this->text('standardLabel'));
	}

	public function addGeneralSettings(array &$config_vars): void
	{
		global $txt;

        loadLanguage($this->name);

		$config_vars[] = $this->text('modName');
		$config_vars[] = array('check', $this->name .'_master', 'subtext' => $this->text('master_sub'));
		$config_vars[] = array('check', $this->name .'_show_in_posts', 'subtext' => $this->text('show_in_posts_sub'));
		$config_vars[] = array('check', $this->name .'_show_in_profile', 'subtext' => $this->text('show_in_profile_sub'));
		$config_vars[] = array('text', $this->name .'_label', 'subtext' => $this->text('label_sub'));
		$config_vars[] = array('int', $this->name .'_timeframe', 'subtext' => $this->text('timeframe_sub'));
		$config_vars[] = array('int', $this->name .'_max_posts', 'subtext' => $this->text('max_posts_sub'));
		$config_vars[] = array('int', $this->name .'_max_width', 'subtext' => $this->text('max_width_sub'));
		$config_vars[] = array('check', $this->name .'_colors', 'subtext' => $this->text('colors_sub'));

		// Option to select the placement.
		$config_vars[] = ['select', $this->name .'_placement',
			[
				$txt['custom_profile_placement_standard'],
				$txt['custom_profile_placement_icons'],
				$txt['custom_profile_placement_above_signature'],
				$txt['custom_profile_placement_below_signature'],
				$txt['custom_profile_placement_below_avatar'],
				$txt['custom_profile_placement_above_member'],
				$txt['custom_profile_placement_bottom_poster'],
				$txt['custom_profile_placement_before_member'],
				$txt['custom_profile_placement_after_member'],
			],
			'subtext' => $this->text('placement_sub'),
			'multiple' => false,
		];

		$config_vars[] = '';
	}

	public function addMemberContext(array &$data, $user, $display_custom_fields)
	{
        $user = (int) $user;

		// Mod is disabled, or we aren't loading any custom profile field, don't bother.
		if(!$this->setting('master') || empty($display_custom_fields))
			return;

		loadTemplate($this->name);

		// Get this user's activity.
		$activity = $this->getActivity($user);

		// Append the data.
		$data['custom_fields'][] = array(
			'title' => $activity['label'],
			'col_name' => $activity['placement'],
			'value' => template_activity_display($activity),
			'placement' => $activity['placement'],
		);

		unset($activity);
	}

    public function addDisplayContext(&$output, &$message): void
    {
        global $context;

        // Mod is disabled or the user does not want to show this field on users posts.
        if(!$this->setting('master') ||
            $this->setting('show_in_posts') ||
            empty($output['custom_fields']))
            return;

        // This is going to be awkward... need to know the placement to properly unset our field...
        $placement = $context['cust_profile_fields_placement'][$this->fieldPlacement];

        foreach ($output['custom_fields'][$placement] as $key => $value)
            if ($value['title'] === $this->fieldLabel)
                unset($output['custom_fields'][$placement][$key]);
    }

	public function addProfile($memID, $area): void
	{
		global $context;

		// Mod is disabled, or we aren't in summary page and show in profile is disabled
		if(!$this->setting('master') ||
            ($area === 'summary' && $this->setting('show_in_profile')))
			return;

        // Get this user's activity.
        $activity = $this->getActivity((int) $memID);

        loadTemplate($this->name);

        $context['custom_fields'][] = array(
            'name' => $this->fieldLabel,
            'placement' => $this->fieldPlacement,
            'output_html' => template_activity_display($activity),
            'show_reg' => false,
        );
	}

	public function create(int $user = 0): array
	{
		global $smcFunc;

		// Meh...
		if (empty($user))
			return [];

		if (($this->activity[$user] = cache_get_data($this->name .'_' . $user,
			self::CACHE_TIME)) == null)
		{
			// Make sure everything is set. If something is missing, use a default value.
			$maxWidth = (int) $this->setting('max_width', self::MAX_WIDTH);
			$maxPosts = (int) $this->setting('max_posts', self::MAX_POSTS);
			$days = (int) $this->setting('timeframe', self::DAYS);

			// Calculate the starting date.
			$startingDate = time() - ($days * 86400);

			// Get all posts since the starting date.
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
			$posts = (int) $smcFunc['db_num_rows']($request);

			$smcFunc['db_free_result']($request);

			// Calculate everything.
			$numPosts = min($posts / $maxPosts, 1);
			$percentage = round(($numPosts * 100), 2);
			$barWidth = $maxWidth * $numPosts;

			// Store the result in an array.
			$this->activity[$user] = [
                'maxWidth' => $maxWidth,
				'width' => $barWidth,
				'percentage' => $percentage,
				'post' => $numPosts,
				'realPost' => $posts,
				'color' => $this->setColor($percentage),
                'label' => $this->fieldLabel,
                'overallText' => $this->fieldPlacement === 0 ? '' : ($this->fieldLabel . ' ' . $percentage . '%'),
                'title' => $this->fieldLabel . ' ' . $percentage . '%',
                'placement' => $this->fieldPlacement
			];

			cache_put_data($this->name .'_' . $user, $this->activity[$user], self::CACHE_TIME);
		}

		// There you go. Anything else?
		return $this->activity[$user];
	}

    public function setColor(float $percentage): string
    {
        $color = self::DEFAULT_COLOR;

        // Which color should we use?
        if ($this->setting('colors'))
        {
            if ($percentage <= 33)
                $color = 'green';

            elseif ($percentage >= 34 && $percentage <= 66)
                $color = 'yellow';

            else
                $color = 'red';
        }

        return $color;
    }

	public function getActivity(int $user = 0): array
	{
		// Let create() to handle the checks.
		$this->create($user);

		return $user ? $this->activity[$user] : $this->activity;
	}

    protected function text(string $textLabel = ''): string
    {
        global $txt;

        return $txt[$this->name . '_' . $textLabel] ?? '';
    }

    protected function setting(string $settingLabel, $defaultValue = false)
    {
        global  $modSettings;

        return $modSettings[$this->name . '_' . $settingLabel] ?? $defaultValue;
    }
}
