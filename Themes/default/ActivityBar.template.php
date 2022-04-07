<?php

declare(strict_types=1);

/**
 * @package Activity Bar mod
 * @version 2.0
 * @author Michel Mendiola <suki@missallsunday.com>
 * @copyright Copyright (c), Michel Mendiola
 * @license http://www.mozilla.org/MPL/ MPL 2.0
 */

function template_activity_display(array $activity): string
{
	if (empty($activity))
		return '';

    return '
        <div 
            id="progress_bar" 
            class="progress_bar progress_'  . $activity['color'] . '"
            style="max-width:' . $activity['maxWidth'] . 'px;"
            title="' . $activity['title'] .'">
            <span id="overall_text">
                ' . $activity['overallText'] .'
            </span>
            <span 
                id="overall_progress" 
                class="bar" 
                style="width: ' . $activity['percentage'] . '%;" />
        </div>
    ';
}
