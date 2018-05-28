<?php

return array(
    /**
     * Default settings
     */
    /**
     * Default currency converter driver
     */
    'default_driver' => 'openexchangerates',
    'default_currency_from' => 'eur',
    'default_currency_to' => 'usd',
    'formatters' => array(
        'eur' => function($value) {
            return '€' . number_format($value, 2, ',', '.');
        },
        'usd' => function($value) {
            return '$' . number_format($value, 2, '.', ',');
        },
        'ltl' => function($value) {
            return 'LT ' . number_format($value, 2, '.', ',');
        },
    ),
    /*
     * Sets timeout for Request cURL
     *
     */
    'timeout' => 5,
    /*
     * How long to cache the response
     *
     */
    'cache' => 1800,
    /*
     * Drivers default config
     *
     */
    'drivers' => array(
        'openexchangerates' => array(
            'url' => 'http://openexchangerates.org/api/latest.json',
            'app_id' => 'cdea87da3e8e46a5951857a5f44712b1',
            'currency_base' => 'usd', // openexchangerates.org has all ratios based on USD
        ),
        'google' => array(
            // %d - amount, 1st %s is currency from, and 2nd - currency to
            'url' => 'http://www.google.com/ig/calculator?hl=en&q=%d%s=?%s',
        ),
        'yahoo' => array(
            // 1st %s is currency from, and 2nd - currency to
            'url' => 'http://download.finance.yahoo.com/d/quotes.csv?s=%s%s=X&f=sl1d1t1ba&e=.csv',
        ),
        'json' => array(
            /**
             * URL to your json data
             */
            'url' => 'https://dl.dropbox.com/u/135243/fxrates.json',
            /**
             * A function to get the exchange rate from your json data. 
             */
            'walker' => function ($currency, $jsondata) {
                return (isset($jsondata->$currency->mid)) ? $jsondata->$currency->mid : false;
            },
            /**
             * The base currency used 
             */
            'base' => 'usd',
            /**
             * The currencies in your json data
             */
            'currencies' => array(
                'usd', 'eur', 'gbp', 'chf',
                'jpy', 'aud', 'cad', 'cny',
                'inr', 'nzd', 'rub', 'zar',
                'hkd', 'dkk', 'sek', 'nok',
            ),
        ),
    ),
);
