<?php

/**
 * @package Activity Bar mod
 * @version 2.0
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2015, Jessica González
 * @license http://www.mozilla.org/MPL/ MPL 2.0
 */

function template_activity_profile($activity)
{
	global $txt, $modSettings;

	if (empty($activity) || !is_array($activity))
		return false;

	$width = !empty($modSettings['ActivityBar_max_width']) ? (int) $modSettings['ActivityBar_max_width'] : 139;
	$template = '';

	$template .='
			<div class="activityBar blue stripes" style="width:'. $width .'px;" title="'. $activity['percentage'] .'%">
				<span style="width: '. $activity['width'] .'%;" title="'. $activity['percentage'] .'%"></span>
			</div>';

	return $template;
}

function template_activity_display($activity)
{
	global $txt, $modSettings;

	if (empty($activity) || !is_array($activity))
		return false;

	// Don't show the label if the placement is "standard with title".
	$label = !empty($activity['placement']) && $activity['placement'] != 0 ? $activity['label'] .':' : '';
	$width = !empty($modSettings['ActivityBar_max_width']) ? (int) $modSettings['ActivityBar_max_width'] : 139;
	$template = '';

	$template .='
			'. $label .'
			<div class="activityBar blue stripes" style="width:'. $width .'px;" title="'. $activity['percentage'] .'%">
				<span style="width: '. $activity['width'] .'%;" title="'. $activity['percentage'] .'%"></span>
			</div>';

	return $template;
}
