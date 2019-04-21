<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 19.03.2019
 * Time: 10:29
 */

class Department_Statuses
{
    const NOT_DECIDED = 1;
    const APPROVED = 2;
    const NOT_APPROVED = 3;
    const TAKEN =4;

    public function getConstants()
    {
        $reflectionClass = new ReflectionClass($this);
        return $reflectionClass->getConstants();
    }

}