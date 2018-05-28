<?php
/**
 * Flexible currency converter for FuelPHP
 *
 * @package    Currency
 * @version    1.0
 * @author     Matthew McConnell (maca134)
 * @license    MIT License
 * @copyright  2012 Matthew McConnell (maca134)
 * @link       http://maca134.co.uk
 * @
 */

namespace Currency;

class Currency_Driver_Json extends \Currency_Driver {

    /**
     * Gets data from JSON source
     *
     * @return	bool	success boolean
     */
    protected function _execute()
    {
        $currency_from = strtolower($this->currency_from);
        $currency_to = strtolower($this->currency_to);
        $amount = $this->amount;
        $url = $this->url;

        try {
            $request = \Request::forge($url, 'curl')->execute();
        } catch (\Exception $e) {
            throw new \FuelException('_execute() error in driver : ' . $this->config['driver'] . '. Error message returned: ' . $e->getMessage());
        }

        if ($request and $request->response()->status === 200 and $request->response()->body())
        {
            $jsondata = json_decode($request->response()->body());

            return $this->_convert($amount, $currency_from, $currency_to, $jsondata);
        }
        else
        {
            throw new \FuelException('Got invalid status/body from ' . $this->config['driver']);
        }

        return false;
    }

    /**
     * Converts currency amounts using Json data
     * 
     * @param type $amount The amount to convert
     * @param type $currency_from Convert from
     * @param type $currency_to Convert to
     * @param type $jsondata The downloaded and decode json currency data
     * @return float The converted amount
     * @throws \FuelException 
     */
    private function _convert($amount, $currency_from, $currency_to, $jsondata)
    {
        $base = $this->get_config('drivers.' . $this->config['driver'] . '.base');
        $currencies = $this->get_config('drivers.' . $this->config['driver'] . '.currencies');

        if ( ! in_array($base, $currencies))
        {
            throw new \FuelException('base is set to a currency that is not available.');
        }
        if ( ! in_array($currency_from, $currencies))
        {
            throw new \FuelException('from_currency is set to a currency that is not available.');
        }
        if ( ! in_array($currency_to, $currencies))
        {
            throw new \FuelException('currency_to is set to a currency that is not available.');
        }

        $walker = $this->get_config('drivers.' . $this->config['driver'] . '.walker');
        if ( ! ($walker instanceof \Closure))
        {
            throw new \FuelException('_execute() error in driver : ' . $this->config['driver'] . '. The Json walker function could not be found.');
        }

        if ($currency_from !== $base)
            $base_currency_amount = $amount / $walker($currency_from, $jsondata);
        else
            $base_currency_amount = $amount;

        if ($currency_to == $base)
            return $base_currency_amount;

        return $base_currency_amount * $walker($currency_to, $jsondata);
    }

}
