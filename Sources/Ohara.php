<?php

/**
 * @package Ohara helper class
 * @version 1.0
 * @author Jessica Gonz�lez <suki@missallsunday.com>
 * @copyright Copyright (c) 2014, Jessica Gonz�lez
 * @license http://www.mozilla.org/MPL/2.0/
 */

namespace Suki;

class Ohara
{
	public static $name = '';

	public function text($var)
	{
		global $txt;

		// This should be extended by somebody else...
		if (empty(static::$name))
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

		// This should be extended by somebody else...
		if (empty(static::$name))
			return false;

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
		return $this->validate($var) ? $this->sanitize($this->_request[$var]) : false;
	}

	public function validate($var, $type = 'request')
	{
		$types = array('request' => $_REQUEST, 'get' => $_GET, 'post' => $_POST);

		$this->_request = (empty($type) || !isset($types[$type])) ? $_REQUEST : $types[$type];

		unset($types);
		return (in_array($var, $this->_request));
	}

	public function sanitize($var)
	{
		global $smcFunc;

		if (is_array($var))
		{
			foreach ($var as $k => $v)
				$var[$k] = $this->sanitize($v);

			return $var;
		}

		else
		{
			if (is_numeric($var))
				$var = (int)trim($var);

			else if (is_string($var))
				$var = $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($var), ENT_QUOTES);

			else
				$var = 'error_' . $var;
		}

		return $var;
	}
}
