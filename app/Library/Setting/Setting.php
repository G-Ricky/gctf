<?php

namespace App\Library\Setting;

use App\Library\Setting\Facades\Loader;
use Illuminate\Config\Repository;

class Setting extends Repository
{
    public function __construct()
    {
        $items = Loader::load();
        $settings = [];

        foreach($items as $item) {
            array_set(
                $settings,
                $item->name,
                $this->cast($item->value, $item->type)
            );
        }

        parent::__construct($settings);
    }

    private function cast($value, $type)
    {
        switch(strtolower($type)) {
            case 'stdclass':
                return json_decode($value);
            case 'array':
                return json_decode($value, true);
            case 'object':
                return serialize($value);
            case 'bool':
            case 'boolean':
            case 'int':
            case 'integer':
            case 'float':
                settype($value, $type);
                return $value;
            case 'null':
                return null;
            case 'string':
            default:
                return $value;
        }
    }
}