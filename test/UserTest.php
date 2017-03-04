<?php

use Task\Classes\User as User;

class UserTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->userLegal = new User(1, "legal");
        $this->userNatural = new User(2, "natural");
    }
    
    public function WrongOperationValues()
    {
        return [
            [["cash", 100, "EUR", "2017-01-01"]],
            [["cash_in", "a100", "EUR", "2017-01-01"]],
            [["cash_in", 100, "EURas", "2017-01-01"]],
            [["cash_out", 100, "EUR", "201701-01"]],
            [["cash_out", 100, "EUR", "2018-02-01"]],
            [["cash_out", 100, "EUR", "2017-02-29"]],
        ];
    }
    /**
    * @dataProvider WrongOperationValues
    * @expectedException InvalidArgumentException
    */
    public function testOperationThrowsExceptionIfInvalidArgumentIsPassed($operation)
    {
        $this->userLegal->operation($operation);
    }

    public function addCashValues()
    {
        return [
            [200, "EUR", 0.06],
            [1000000, "EUR", 5],
            [200, "USD", 0.06],
            [100000, "USD", 5.75],
            [1000000, "JPY", 300],
            [10000000, "JPY", 647.65],
            [0.03, "EUR", 0.01],
        ];
    }
    /**
    * @dataProvider addCashValues
    */
    public function testCashIn($cash, $currency, $commission)
    {
        $this->assertEquals($commission, $this->userLegal->addCash($cash, $currency));
    }
    
    public function WrongAddCashValues()
    {
        return [
          ["a", "EUR"],
          [-1, "JPY"],
          [-1, "USDD"],
        ];
    }
    /**
    * @dataProvider WrongAddCashValues
    * @expectedException InvalidArgumentException
    */
    public function testAddCashThrowsExceptionIfInvalidArgumentIsPassed($cash, $currency)
    {
        $this->userLegal->addCash($cash, $currency);
    }
    
    public function WrongTakeCashValues()
    {
        return [
          [-300, "EUR", "2002-09-09"],
          [600, "USDS", "2002-12-01"],
          [150, "EUR", "2002-21-19"],
          [150, "JPY", "2020-04-19"],
          ["a", "JPY", "2020-04-19"],
          [150, 1, "2020-04-19"],
          [150, "EUR", "202004-19"],
          [150, "EUR", 1],
        ];
    }
    /**
    * @dataProvider WrongTakeCashValues
    * @expectedException InvalidArgumentException
    */
    public function testTakeCashThrowsExceptionIfInvalidArgumentIsPassed($cash, $currency)
    {
        $this->userLegal->takeCash($cash, $currency, $date);
    }
    
    public function takeCashValues()
    {
        return [
          [0.9, 300, "EUR", "2002-09-09"],
          [1.8, 600, "USD", "2002-12-01"],
          [0.5, 150, "EUR", "2002-04-19"],
          [64.77, 150, "JPY", "2002-04-19"],
        ];
    }
    /**
    * @dataProvider takeCashValues
    */
    public function testTakeCash($commission, $cash, $currency, $date)
    {
        $this->assertEquals($commission, $this->userLegal->takeCash($cash, $currency, $date));
    }
    public function takeCashNumbersForNatural()
    {
        return [
          [0, 300, "EUR", "2002-09-09"],
          [0, 600, "USD", "2002-12-01"],
          [0, 150, "JPY", "2002-04-19"],
          [897, 300000, "EUR", "2002-09-09"],
          [1796.56, 600000, "USD", "2002-12-01"],
          [61.42, 150000, "JPY", "2002-04-19"],
        ];
    }
    /**
    * @dataProvider takeCashNumbersForNatural
    */
    public function testTakeCashforNatural($commission, $cash, $currency, $date)
    {
        $this->assertEquals($commission, $this->userNatural->takeCash($cash, $currency, $date));
    }
    public function dates()
    {
        return [
          ["2017-01-01", 52],
          ["2017-01-02", 1],
          ["2016-12-24", 51],
        ];
    }
    /**
    * @dataProvider dates
    */
    public function testGetWeekByDate($date, $week)
    {
        $this->assertEquals($week, $this->userNatural->getWeekByDate($date));
    }
}
