<?php

use Task\Classes\Converter as Converter;

class ConverterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->converter = new Converter();
    }
    public function eurToUsdNumbers()
    {
        return [
          [200, false, 229.94],
          [200, true, 173.95842393667914],
        ];
    }
    /**
    * @dataProvider eurToUsdNumbers
    */
    public function testEurToUsd($cash, $reverse, $sum)
    {
        $this->assertEquals($sum, $this->converter->EurToUsd($cash, $reverse));
    }
    public function eurToJpyNumbers()
    {
        return [
          [200, false, 25906],
          [200, true, 1.544043850845364],
        ];
    }
    /**
    * @dataProvider eurToJpyNumbers
    */
    public function testEurToJpy($cash, $reverse, $sum)
    {
        $this->assertEquals($sum, $this->converter->EurToJpy($cash, $reverse));
    }
    
    public function WrongCurrencyValues()
    {
        return [
          [1, "true"],
          [-1, true],
          ["-1", false],
        ];
    }
    /**
    * @dataProvider WrongCurrencyValues
    * @expectedException InvalidArgumentException
    */
    public function testCurrencyThrowsExceptionIfInvalidArgumentIsPassed($cash, $reverse)
    {
        $this->converter->EurToJpy($cash, $reverse);
        $this->converter->EurToUsd($cash, $reverse);
    }
}
