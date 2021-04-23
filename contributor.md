Replaced now()->toDateTimeString() with Carbon::now()

I got the below error so changed below lines.
Uncaught exception 'Error' with message 'Call to undefined function Anik\ElasticApm\Middleware\now()' 
in /var/www/vendor/anik/elastic-apm-php/src/Middleware/RecordForegroundTransaction.php:87
