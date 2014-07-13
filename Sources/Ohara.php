<?php

/**
 * @package Ohara helper class
 * @version 1.0
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2014, Jessica González
 * @license http://www.mozilla.org/MPL/2.0/
 */

namespace Suki;

class Ohara
{
	public $name = '';
	protected $_text = array();
	protected static $_registry = array();
	protected $_request = array();

	public function getName()
	{
		return $this->name;
	}

	public function setRegistry()
	{
		self::$_registry[$this->name] = $this;
	}

	public function getRegistry($instance = '')
	{
		return $instance ? self::$_registry[$instance] : self::$_registry;
	}

	public function text($var)
	{
		global $txt;

		// This should be extended by somebody else...
		if (empty($this->name) || empty($var))
			return false;

		if (!isset($this->_text[$var]))
			$this->setText($var);

		return $this->_text[$var];
	}

	protected function setText($var)
	{
		global $txt;

		// No var no set.
		if (empty($var))
			return false;

		// Load the mod's language file.
		loadLanguage($this->name);

		if (!empty($txt[$this->name .'_'. $var]))
			$this->_text[$var] =  $txt[$this->name .'_'. $var];

		else
			$this->_text[$var] = false;
	}

	public function getAllText()
	{
		return $this->_text;
	}

	public function enable($var)
	{
		global $modSettings;

		if (empty($var))
			return false;

		if (isset($modSettings[$this->name .'_'. $var]) && !empty($modSettings[$this->name .'_'. $var]))
			return true;

		else
			return false;
	}

	public function setting($var)
	{
		global $modSettings;

		// This should be extended by somebody else...
		if (empty($this->name) || empty($var))
			return false;

		if (true == $this->enable($var))
			return $modSettings[$this->name .'_'. $var];

		else
			return false;
	}

	public function modSetting($var)
	{
		global $modSettings;

		// This should be extended by somebody else...
		if (empty($this->name))
			return false;

		if (empty($var))
			return false;

		global $modSettings;

		if (isset($modSettings[$var]))
			return $modSettings[$var];

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
