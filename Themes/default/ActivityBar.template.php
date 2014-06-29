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
