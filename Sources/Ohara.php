<?php

/**
 * @package Ohara helper class mod
 * @version 1.0
 * @author Jessica Gonz�lez <missallsunday@simplemachines.org>
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
 * The Original Code is http://code.mattzuba.com code.
 *
 * The Initial Developer of the Original Code is
 * Matt Zuba.
 * Portions created by the Initial Developer are Copyright (C) 2010-2011
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 * Jessica Gonz�lez <missallsunday@simplemachines.org>
 */

class Ohara
{
	/**
	 * @var SMF_Singleton Instance of the bridge class
	 */
	protected static $instance;

	/**
	 * @var array Comma separated list of hooks this class implements
	 */
	protected $hooks = array();

	/**
	 * @var boolean Should the hooks only be installed once?
	 */
	protected $persistHooks = FALSE;

	/**
	 * This should be overwritten
	 */
	protected function __construct()
	{
		if (!$this->persistHooks)
			$this->installHooks();
	}

	/**
	 * Installs the hooks to be used by this module.
	 */
	public function installHooks()
	{
		foreach ($this->hooks as $hook)
			add_integration_function($hook, static::$__CLASS__ . '::handleHook', $this->persistHooks);
	}

	/**
	 * Takes all call_integration_hook calls from SMF and figures out what
	 * method to call within the class
	 */
	public static function handleHook()
	{
		$backtrace = debug_backtrace();
		$method = NULL;
		$args = NULL;
		foreach ($backtrace as $item)
			if ($item['function'] === 'call_integration_hook')
			{
				$method = $item['args'][0];
				$args = !empty($item['args'][1]) ? $item['args'][1] : array();
				break;
			}

		if (!isset($method) || !is_callable(array(self::$instance, $method)))
			trigger_error('Invalid call to handleHook', E_USER_ERROR);

		return call_user_func_array(array(self::$instance, $method), $args);
	}

	/**
	 * Let's try the singleton method
	 *
	 * @return void
	 */
	public static function getInstance()
	{
		if (!isset(static::$__CLASS__))
			trigger_error('<strong>protected static $__CLASS__ = __CLASS__;</strong> must be contained in child class', E_USER_ERROR);

		if (!isset(self::$instance) || !(self::$instance instanceof static::$__CLASS__))
			self::$instance = new static::$__CLASS__();

		return self::$instance;
	}

	protected function text($string)
	{
		global $txt;

		if (empty($string))
			return false;

		loadLanguage(static::$__CLASS__);

		if (!empty($txt[static::$__CLASS__ .'_'. $string]))
			return $txt[static::$__CLASS__ .'_'. $string];

		else
		return false;
	}

	protected function setting($var)
	{
		global $modSettings;

		if (!empty($modSettings[static::$__CLASS__ .'_'. $var]))
			return $modSettings[static::$__CLASS__ .'_'. $var];

		else
			return false;
	}
}
