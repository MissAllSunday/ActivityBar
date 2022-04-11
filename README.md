# Activity Bar


Original idea and original developer: [Nas](http://www.simplemachines.org/community/index.php?action=profile;u=143954)

### Description

**For SMF 2.1.x only**

_Activity Bar_ displays a bar in posts and/or profiles, showing how active the user is.
The admin can choose where to display it (Posts, Profile or both), max width, how many posts that are required for a full bar, and the timeframe in which posts are counted.
Can be fully customized via CSS.

Uses the settings set by the administrator to calculate the activity.
It starts off by getting all messages posted within the set timeframe, and counts them.
Calculating starts off by dividing the number of posts by the max number of posts possible (Also a setting), and then multiplies that with 100. What it gets is the percentage displayed inside the bar.
It then figures out how much of the bar should be filled, by multiplying the result from the earlier division, width the max possible with the bar can have (Again, set by administrators).
This information is then used to create the bar.


### Settings

- Admin > Configuration > Modification Settings > Miscellaneous


### Languages

- English
- Spanish
- Russian


### Changelog

```
2.0.1 April 2022
- Change logic when displaying the bar on profile pages

2.0 April 2022
- Use SMF's bar css style for bar colors
- Remove Ohara library
- Remove old dependencies
- PHP 7.4 or above support
- Remove utf-8 language files
- Clean up template functions

2.0 beta June 2015
- Changed license to MPL 2.0
- Uses Ohara helper library
- Removes hook specific files in favour of xml based tags

2.0 alpha June 2014
- Compatibility with SMF 2.1 only.
- No more file edits.
- CSS3 CSS3 progress bars thanks to https://red-team-design.com/stylish-css3-progress-bars/
- All css moved to activity.css
- Select the position to show the bar on the Display page.
- Fixed some minor bugs.

1.2 October 2013
- Changed folder structure.
- Removed all template edits.
- Re-wrote all language strings.
- Increase readability for Spanish users.
- Added cache and the ability to clean it whenever a post count increases.
- Re-wrote all source code into a single class.
- Now has its own template file and both the display and the profile HTML are separated into their own functions.
- Uses the custom profile field system to avoid template edits.

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
```
