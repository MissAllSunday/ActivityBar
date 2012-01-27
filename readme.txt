[center][color=purple][size=5][b]Activity Bar [/b][/size][/color]
[b]Author:[/b] [url=http://missallsunday.com]Suki[/url][/center]


Original idea and original developer: [url=http://www.simplemachines.org/community/index.php?action=profile;u=143954]Nas[/url]

[color=purple][b][size=12pt]Description[/size][/b][/color]

[b]For SMF 2.0.x only[/b]

[i]Activity Bar[/i] displays a bar in posts and/or profiles, showing how active the user is.
The admin can choose where to display it (Posts, Profile or both), max width, how many posts that are required for a full bar, and the timeframe in which posts are counted.
Can be fully customized via CSS.

Uses the settings set by the administrator to calculate the activity.
It starts off by getting all messages posted within the set timeframe, and counts them.
Calculating starts off by dividing the number of posts by the max number of posts possible (Also a setting), and then multiplies that with 100. What it gets is the percentage displayed inside the bar.
It then figures out how much of the bar should be filled, by multiplying the result from the earlier division, width the max possible with the bar can have (Again, set by administrators).
This information is then used to create the bar.


[color=purple][b][size=12pt]License[/size][/b][/color]

 * This SMF modification is subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this SMF modification except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/

 
[color=purple][b][size=12pt]Settings[/size][/b][/color]

-Admin > Configuration > Modifications


[color=purple][b][size=12pt]Languages[/size][/b][/color]

-English/utf8
-Spanish/utf8


[color=purple][b][size=12pt]Changelog[/size][/b][/color]

1.1 January 2012
-Added compatibility for 2.0.x
-Removed compatibility for 1.1.x
-Reduce queries per page
-Use of hooks
-Less file edits

1.0.1.3 | 28 June 2011
*Now compatible with SMF 2.0

1.0.1.2 | 9 November 2009
* Now compatible with SMF 2.0 RC2 and Curve.
$ Older version of SMF 2.0 are no longer supported, nor is the old default theme Core (The 1.1.x version keeps its Core support, of course).
1.0.1.1 | 1 July 2009
* Small edit to make compatible with SMF 2.0 RC1.2.
> Added translations for Turkish, czech-utf8 and portuguese_pt. 
1.0.1 | 1 July 2009
* Added support for SMF 1.1.x.
1.0 | 29 June 2009
$ Created and added to the mod site.
