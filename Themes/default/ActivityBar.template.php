<?php

/**
 * @package Activity Bar mod
 * @version 1.2
 * @author Jessica Gonz�lez <suki@missallsunday.com>
 * @copyright Copyright (c) 2013, Jessica Gonz�lez
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
 * Jessica Gonz�lez <suki@missallsunday.com>
 */

function template_activity_profile($activity)
{
	global $txt, $modSettings;

	if (empty($activity))
		return false;

	$template = '';

	$template .='
		<div class="activity_div">
			<div class="activity_holder" style="width: '. (!empty($modSettings['ActivityBar_max_width']) ? $modSettings['ActivityBar_max_width'] : 139) .'px;">
				<div class="ActivityBar" style="width: '. $activity['width'] .'px;">
					<div class="activity_percentage">'. $activity['percentage'] .'%</div>
				</div>
			</div>
		</div>';

	return $template;
}

function template_activity_display($activity)
{
	global $txt, $modSettings;

	if (empty($activity))
		return false;

	// Don't show the label if placement is "standard with title" as the code will do it automatically.
	$label = $activity['placement'] ? $activity['title'] ? '';

	$template = '';

	$template .='
		<div class="activity_div">
			'. ($label) .'
			<div class="activity_holder" style="width: '. (!empty($modSettings['ActivityBar_max_width']) ? $modSettings['ActivityBar_max_width'] : 139) .'px;">
				<div class="ActivityBar" style="width: '. $activity['width'] .'px;">
					<div class="activity_percentage">'. $activity['percentage'] .'%</div>
				</div>
			</div>
		</div>';

	return $template;
}
