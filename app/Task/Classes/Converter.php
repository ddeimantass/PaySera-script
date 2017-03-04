<?php
namespace Task\Classes;

class Converter
{
    const EUR_LIMIT = 1000;
    const EUR_MIN = 0.5;
    const EUR_MAX = 5;
    const USD = 1.1497;
    const JPY = 129.53;
    
    public function EurToUsd($amount, $reverse = false)
    {
        self::isValid($amount, $reverse);
        if (!$reverse) {
            return $amount * self::USD;
        } else {
            return $amount / self::USD;
        }
    }
    public function EurToJpy($amount, $reverse = false)
    {
        self::isValid($amount, $reverse);
        if (!$reverse) {
            return $amount * self::JPY;
        } else {
            return $amount / self::JPY;
        }
    }
    public function isValid($amount, $reverse)
    {
        if (!is_numeric($amount) || $amount < 0 || !is_bool($reverse)) {
            throw new \InvalidArgumentException;
        }
    }
    public function getUsd()
    {
        return self::USD;
    }
    public function getJpy()
    {
        return self::JPY;
    }
    public function getEurMin()
    {
        return self::EUR_MIN;
    }
    public function getEurLimit()
    {
        return self::EUR_LIMIT;
    }
    public function getEurMax()
    {
        return self::EUR_MAX;
    }
    public function getUsdMin()
    {
        return self::EurToUsd(self::EUR_MIN);
    }
    public function getJpyMin()
    {
        return self::EurToJpy(self::EUR_MIN);
    }
    public function getUsdMax()
    {
        return self::EurToUsd(self::EUR_MAX);
    }
    public function getJpyMax()
    {
        return self::EurToJpy(self::EUR_MAX);
    }
}
