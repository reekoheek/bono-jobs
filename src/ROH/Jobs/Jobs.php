<?php

namespace ROH\Jobs;

class Jobs
{
    protected static $options;

    protected static $spools;

    protected static $defaultSpool;

    protected static $aliases = array(
        'cron' => '\\ROH\\Jobs\\Spool\\CronSpool'
    );

    public static function init($options)
    {
        static::$options = $options;

        $first = null;
        if (!empty($options['spools'])) {
            foreach ($options['spools'] as $key => $spool) {
                $spool['name'] = $key;
                if (isset(static::$aliases[$spool['driver']])) {
                    $spool['driver'] = static::$aliases[$spool['driver']];
                }

                $Driver = $spool['driver'];

                static::$spools[$key] = new $Driver($spool);

                if (is_null($first)) {
                    $first = $key;
                }
            }

            if (!static::$defaultSpool) {
                static::$defaultSpool = $first;
            }

        }
    }

    public static function getSpool($name = null)
    {

        if (is_null($name)) {
            $name = static::$defaultSpool;
        }

        return static::$spools[$name];
    }

    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(static::getSpool(), $method), $parameters);
    }
}
