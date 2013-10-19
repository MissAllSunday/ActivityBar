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

function template_activity_profile($user)
{
	global $modSettings, $context, $txt;

	$template = '';

	if(!empty($modSettings['ActivityBar_enable']) && !empty($modSettings['ActivityBar_show_in_profile']) && !empty($context['ActivityBar']))
		$template .= '
	<dt>'. !empty($modSettings['ActivityBar_label']) ? $modSettings['ActivityBar_label'] : $txt['ActivityBar_standardlabel'] .':</dt>
	<dd>
		<div class="activity_holder" style="width: '. !empty($modSettings['ActivityBar_max_width']) ? $modSettings['ActivityBar_max_width'] : 139 .'px;">
			<div class="ActivityBar" style="width: '. $context['ActivityBar']['width'] .'px;">
				<div class="activity_percentage smalltext">'. $context['ActivityBar']['percentage'] .'%</div>
			</div>
		</div>
	</dd>';

	return $template;
}

function template_activity_display($user)
{
	global $context, $txt, $modSettings;

	if (empty($user) || empty($context[$user]['ActivityBar']))
		return false;

	$template = '';
	$bar = $context[$user]['ActivityBar'];

	$template .='
		<div class="activity_div">
			'. (!empty($modSettings['ActivityBar_label']) ? $modSettings['ActivityBar_label'] : $txt['ActivityBar_standardlabel']) .':
			<div class="activity_holder" style="width: '. (!empty($modSettings['ActivityBar_max_width']) ? $modSettings['ActivityBar_max_width'] : 139) .'px;">
				<div class="ActivityBar" style="width: '. $bar['width'] .'px;">
					<div class="activity_percentage">'. $bar['percentage'] .'%</div>
				</div>
			</div>
		</div>';

	return $template;
}
