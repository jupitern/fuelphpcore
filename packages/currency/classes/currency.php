<?php
/**
 * Flexible currency converter for FuelPHP
 *
 * @package    Currency
 * @version    1.0
 * @author     Jaroslav Petrusevic (huglester)
 * @license    MIT License
 * @copyright  2012 Jaroslav Petrusevic (huglester)
 * @link       http://www.webas.lt
 */

namespace Currency;

class Currency
{

	/**
	 * Instance for singleton usage.
	 */
	public static $_instance = false;

	/**
	 * Driver config defaults.
	 */
	protected static $_defaults;

	/**
	 * Currency driver forge.
	 *
	 * @param	string|array	$driver		driver
	 * @param	array			$config		extra config array
	 * @return  Currency_Driver    one of the pingsearch drivers
	 */
	public static function forge($driver = null)
	{
		($driver) ? $config['driver'] = $driver : $config = array();

		$config = \Arr::merge(static::$_defaults, $config);

		( ! isset($config['driver']) or empty($config['driver'])) and $config['driver'] = \Config::get('currency.default_driver', 'openexchangerates');

		/*
		 * Multiple driver support per request
		 * */
/*		if (is_array($config['driver']))
		{
			return new \Currency_Collection($config);
		}*/

		$driver = '\\Currency_Driver_'.ucfirst(strtolower($config['driver']));

		if ( ! class_exists($driver, true))
		{
			throw new \FuelException('Could not find Currency driver: '.$config['driver']. ' ('.$driver.')');
		}

		$driver = new $driver($config);

		return $driver;
	}

	/**
	 * Init, config loading.
	 */
	public static function _init()
	{
		\Config::load('currency', true);
		static::$_defaults = \Config::get('currency');
	}

	/**
	 * Call rerouting for static usage.
	 *
	 * @param	string	$method		method name called
	 * @param	array	$args		supplied arguments
	 */
	public static function __callStatic($method, $args = array())
	{
		if (static::$_instance === false)
		{
			$instance = static::forge();
			static::$_instance = &$instance;
		}

		if (is_callable(array(static::$_instance, $method)))
		{
			return call_user_func_array(array(static::$_instance, $method), $args);
		}

		throw new \BadMethodCallException('Invalid method: '.get_called_class().'::'.$method);
	}

}
