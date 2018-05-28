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

Autoloader::add_core_namespace('Currency');

Autoloader::add_classes(array(
	/**
	 * Rates classes.
	 */
	'Currency\\Currency'							=> __DIR__.'/classes/currency.php',
	'Currency\\Currency_Driver'					    => __DIR__.'/classes/currency/driver.php',
	'Currency\\Currency_Driver_Openexchangerates'	=> __DIR__.'/classes/currency/driver/openexchangerates.php',
	'Currency\\Currency_Driver_Google'				=> __DIR__.'/classes/currency/driver/google.php',
	'Currency\\Currency_Driver_Yahoo'				=> __DIR__.'/classes/currency/driver/yahoo.php',
    	'Currency\\Currency_Driver_Json'				=> __DIR__.'/classes/currency/driver/json.php',
    
));