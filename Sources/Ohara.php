<?php

/**
 * @package Ohara helper class
 * @version 1.0
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2014, Jessica González
 * @license http://www.mozilla.org/MPL/2.0/
 */

class Ohara
{
	public static $name = '';

	public function text($var)
	{
		global $txt;

		// This should be extended by somebody else...
		if (!empty(static::$name))
			return false;

		// No var to check.
		if (empty($var))
			return false;

		// Load the mod's language file.
		loadLanguage(static::$name);

		if (!empty($txt[static::$name .'_'. $var]))
			return $txt[static::$name .'_'. $var];

		else
			return false;
	}

	public function enable($var)
	{
		global $modSettings;

		if (empty($var))
			return false;

		if (isset($modSettings[static::$name .'_'. $var]) && !empty($modSettings[static::$name .'_'. $var]))
			return true;

		else
			return false;
	}

	public function setting($var)
	{
		global $modSettings;

		if (empty($var))
			return false;

		global $modSettings;

		if (true == $this->enable($var))
			return $modSettings[static::$name .'_'. $var];

		else
			return false;
	}

	public function data($var)
	{
		return $this->sanitize($var);
	}

	public function sanitize($var)
	{
		global $smcFunc;

		if (empty($var))
			return false;

		$return = false;

		// Is this an array?
		if (is_array($var))
			foreach ($var as $item)
			{
				if (!in_array($item, $_REQUEST))
					continue;

				if (empty($_REQUEST[$item]))
					$return[$item] = '';

				if (ctype_digit($_REQUEST[$item]))
					$return[$item] = (int) trim($_REQUEST[$item]);

				elseif (is_string($_REQUEST[$item]))
					$return[$item] = $smcFunc['htmlspecialchars'](trim($_REQUEST[$item]), ENT_QUOTES);
			}

		// No? a single item then, check it boy, check it!
		elseif (empty($_REQUEST[$var]))
			return false;

		else
		{
			if (ctype_digit($_REQUEST[$var]))
				$return = (int) trim($_REQUEST[$var]);

			elseif (is_string($_REQUEST[$var]))
				$return = $smcFunc['htmlspecialchars'](trim($_REQUEST[$var]), ENT_QUOTES);
		}

		return $return;
	}
}
