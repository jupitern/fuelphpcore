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

class Currency_Driver_Openexchangerates extends \Currency_Driver
{

	/**
	 * Gets data from Openxchangerates site
	 *
	 * @return	bool	success boolean
	 */
	protected function _execute()
	{
		//http://openexchangerates.org/api/latest.json?app_id=XXXXXXXXXXXX
		$app_id = $this->get_config('drivers.openexchangerates.app_id');

		if ( ! $app_id)
		{
			throw new \FuelException('_execute() error in driver : '.$this->config['driver']. '. app_id not set.');
		}

        $cache_found = false;
        $response = null;

		// try to retrieve the cache
		try
		{
			$response = \Cache::get('currency_openexchangerates');
            $cache_found = true;
		}
		catch (\CacheNotFoundException $e)
		{
			try
			{
				$url = $this->url.'?app_id='.$app_id;

				$request = \Request::forge($url, 'curl')->execute();
			}
			catch (\Exception $e)
			{
				throw new \FuelException('_execute() error in driver : '.$this->config['driver']. '. Error message returned: '.$e->getMessage());
			}
		}

		if ($response or $request)
		{
            if ( ! $cache_found)
            {
                if ($request->response()->status === 200 and $request->response()->body())
                {
                    $response = $request->response()->body();
                    \Cache::set('currency_openexchangerates', $response, $this->get_config('currency.cache', 1800));
                }
                else
                {
                    throw new \FuelException('Got invalid status/body from '.$this->config['driver']);
                }
            }

			$return = json_decode($response);

			$currency_from = strtoupper($this->currency_from);
			$currency_to = strtoupper($this->currency_to);
			$amount = $this->amount;

			if (isset($return->rates->$currency_to) and isset($return->rates->$currency_from))
			{
				$part1 = bcdiv($amount, $return->rates->$currency_from, 6);
				return bcmul($part1, $return->rates->$currency_to, 6);
			}
			else
			{
				throw new \FuelException('Driver: '.$this->config['driver'].' does not support $currency_to '.$currency_to.' and $currency_from: '.$currency_from);
			}
		}

		return false;
	}

}
