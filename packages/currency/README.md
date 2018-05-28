#FuelPHP currency converter

Currently work with OpenXChangeRates.org, Google, Yahoo and custom JSON feed

TODO: any other drivers?

Usage example:

    \Package::load('currency');

    // This will use the default driver:
    echo \Currency::forge()->convert(100, 'usd')->to('eur');
    // €80,88
    echo \Currency::forge()->convert(10, 'eur')->to('usd');
    // $12.36

    // This will use specific driver
    echo \Currency::forge('google')->convert(10, 'eur')->to('usd');
    // $12.37

    // to() method accepts formatter closure as 2nd argument, like:
    echo \Currency::forge('google')->convert(110, 'USD')->to('eur', function($value) {return number_format($value, 4).' EUR/min';})
    // 88.8961 EUR/min

Config supports formatters, config looks like:

	'formatters' => array(
		'eur' => function($value)
		{
			return '€'.number_format($value, 2, ',', '.');
		},
		'usd' => function($value)
		{
			return '$'.number_format($value, 2, '.', ',');
		},
	),

JSON
----

To use your own JSON data, here is the config:

    'json' => array(
        /**
        * URL to your json data
        */
        'url' => 'https://dl.dropbox.com/u/135243/fxrates.json',
        /**
        * A function to get the exchange rate from your json data. 
        */
        'walker' => function ($currency, $jsondata) {
            return $jsondata->$currency->mid;
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
            'hkd', 'dkk', 'sek', 'nok'
        )
    )

The walker function is used to find the currency rate from your JSON data. Everything else is straight forward.

