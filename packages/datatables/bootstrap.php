<?php
/**
 * Fuel
 *
 * Datatables package
 *
 * @package    Datatables
 * @version    1.0
 * @author     Nuno Chaves
 * @license    MIT License
 * @copyright  2013 Nuno Chaves
 * @link       http://nunochaves.com
 */


Autoloader::add_core_namespace('Datatables');

Autoloader::add_classes(array(
	/**
	 * Datatables classes.
	 */
	'Datatables\\Datatables'	=> __DIR__.'/classes/datatables.php',
));
