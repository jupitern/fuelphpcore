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

class Currency_Driver_Google extends \Currency_Driver
{

	/**
	 * Gets data from Google calculator
	 *
	 * @return	bool	success boolean
	 */
	protected function _execute()
	{
        $currency_from = strtoupper($this->currency_from);
        $currency_to = strtoupper($this->currency_to);
        $amount = $this->amount;

        $url = sprintf($this->url, $amount, $currency_from, $currency_to);

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
            $response = $request->response()->body();

            $search = array('lhs',    'rhs',   'error',   'icc');
            $replace = array('"lhs"', '"rhs"', '"error"', '"icc"');

            $response = str_replace($search, $replace, $response);

            $return = json_decode($response);
            if (empty($return->error))
            {
                $result = (float) $return->rhs;

                return $result;
            }
        }
        else
        {
            throw new \FuelException('Got invalid status/body from '.$this->config['driver']);
        }

        return false;
    }

}
