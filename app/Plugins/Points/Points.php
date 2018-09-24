<?php

namespace App\Plugins\Points;

class Points
{
    protected static $alias = [
        'dynamic' => DynamicPoints::class,
    ];

    protected static $instance = null;
    protected static $policy = 'dynamic';

    /**
     * @return StaticPoints|DynamicPoints
     */
    protected static function instance()
    {
        if(is_null(self::$instance)) {
            self::$instance = new self::$alias[self::$policy]();
        }

        return self::$instance;
    }

    public static function calculate($basic, $solvers)
    {
        return self::instance()->calculate($basic, $solvers);
    }

    public static function using($policy)
    {
        if(!in_array(array_keys(self::$alias), $policy)) {
            throw new \Exception('Undefined policy');
        }

        self::$policy = $policy;
    }
}