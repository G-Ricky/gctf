<?php

namespace App\Plugins\Points;

class DynamicPoints
{
    public function calculate($basic, $solvers)
    {
        $lambda = 0.1;
        return $basic * pow(M_E, -$lambda * $solvers);
    }
}