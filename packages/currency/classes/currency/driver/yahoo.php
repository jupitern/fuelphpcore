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

class Currency_Driver_Yahoo extends \Currency_Driver
{

	/**
	 * Gets data from Yahoo finance
	 *
	 * @return	bool	success boolean
	 */
	protected function _execute()
	{
        $currency_from = strtoupper($this->currency_from);
        $currency_to = strtoupper($this->currency_to);
        $amount = $this->amount;

        $url = sprintf($this->url, $currency_from, $currency_to);

        try
        {
            $request = \Request::forge($url, 'curl')->execute();
        }
        catch (\Exception $e)
        {
            throw new \FuelException('_execute() error in driver : '.$this->config['driver']. '. Error message returned: '.$e->getMessage());
        }

        if ($request and $request->response()->status === 200 and $request->response()->body())
        {
            $array = explode(',', $request->response()->body());

            if ($array and isset($array[1]))
            {
                return bcmul($amount, $array[1], 7);
            }
            else
            {
                throw new \FuelException('Got buggy data from '.$this->config['driver'].', api changed?');
            }
        }
        else
        {
            throw new \FuelException('Got invalid status/body from '.$this->config['driver']);
        }

        return false;
    }

}
