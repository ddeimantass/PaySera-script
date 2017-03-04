<?php
namespace Task\Classes;

class User
{
    private $id;
    private $userType;
    private $cashOutWeek;
    private $cashOutCount;
    private $limit;
    private $week;
    private $lastDate;
    
    public function __construct($id, $userType)
    {
        $this->setLastDate("1000-01-01");
        $this->setWeek(0);
        $this->setId($id);
        $this->setUserType($userType);
        $this->setCashOutWeek(0);
        $this->setCashOutCount(0);
        $this->setLimit(Converter::getEurLimit());
    }
    
    public function isLegal()
    {
        if ($this->userType === "legal") {
            return true;
        } else {
            return false;
        }
    }
    public function isOverCashLimit()
    {
        if ($this->cashOutWeek > Converter::getEurLimit()) {
            return true;
        } else {
            return false;
        }
    }
    public function isOverCountLimit()
    {
        $countLimit = 3;
        if ($this->cashOutCount > $countLimit) {
            return true;
        } else {
            return false;
        }
    }
    public function isNextWeek($date)
    {
        if ($date < $this->lastDate) {
            throw new \InvalidArgumentException;
        }
        if ($this->week != $this->getWeekByDate($date)) {
            $this->setWeek($this->getWeekByDate($date));
            $this->setLastDate($date);
            return true;
        } else {
            return false;
        }
    }
    public function setId($id)
    {
        if (!is_numeric($id) || $id < 0) {
            throw new \InvalidArgumentException;
        }
        $this->id = $id;
    }
    public function setUserType($userType)
    {
        $userTypes = array("legal", "natural");
        if (!in_array($userType, $userTypes)) {
            throw new \InvalidArgumentException;
        }
        $this->userType = $userType;
    }
    public function setLastDate($lastDate)
    {
        $this->lastDate = $lastDate;
    }
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    public function setWeek($week)
    {
        $this->week = $week;
    }
    public function setCashOutWeek($cashOutWeek)
    {
        $this->cashOutCount = $cashOutWeek;
    }
    public function setCashOutCount($cashOutCount)
    {
        $this->cashOutCount = $cashOutCount;
    }
    public function getLastDate()
    {
        return $this->lastDate;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function getUserType()
    {
        return $this->userType;
    }
    public function getWeek()
    {
        return $this->week;
    }
    public function getCashOutWeek()
    {
        return $this->cashOutCount;
    }
    public function getCashOutCount()
    {
        return $this->cashOutCount;
    }
    
    public function getWeekByDate($ddate)
    {
        $duedt = explode("-", $ddate);
        foreach ($duedt as $element) {
            if (!is_numeric($element)) {
                throw new \InvalidArgumentException;
            }
        }
        date_default_timezone_set('Europe/Vilnius');
        $year = (int)$duedt[0];
        $month = (int)$duedt[1];
        $day = (int)$duedt[2];
        
        if (!checkdate($month, $day, $year)) {
            throw new \InvalidArgumentException;
        }
        
        $date_now = new \DateTime();
        $date = new \DateTime($ddate);
        
        if ($date_now < $date) {
            throw new \InvalidArgumentException;
        }

        $week = $date->format("W");
        return $week;
    }
    public function operation($operation)
    {
        switch ($operation[0]) {
            case "cash_in":
                return $this->addCash($operation[1], $operation[2]);
            case "cash_out":
                return $this->takeCash($operation[1], $operation[2], $operation[3]);
            default:
                throw new \InvalidArgumentException;
        }
    }
    public function addCash($cash, $currency)
    {
        switch ($currency) {
            case "EUR":
                $commissionMax = Converter::getEurMax();
                break;
            case "USD":
                $commissionMax = Converter::getUsdMax();
                break;
            case "JPY":
                $commissionMax = Converter::getJpyMax();
                break;
            default:
                throw new \InvalidArgumentException;
        }
        return ceil(($this->commissionIn($cash, $commissionMax)) * 100) / 100;
    }
    public function takeCash($cash, $currency, $date)
    {
        if (!is_numeric($cash) || $cash < 0 || strpos($date, '-') === false) {
            throw new \InvalidArgumentException;
        }
        if ($this->isNextWeek($date)) {
            $this->cashOutWeek = 0;
            $this->cashOutCount = 0;
        }
        switch ($currency) {
            case "EUR":
                $cashEur = $cash;
                $commissionMin = Converter::getEurMin();
                $limit = Converter::getEurMin();
                break;
            case "USD":
                $cashEur = Converter::EurToUsd($cash, true);
                $commissionMin = Converter::getUsdMin();
                break;
            case "JPY":
                $cashEur = Converter::EurToJpy($cash, true);
                $commissionMin = Converter::getJpyMin();
                break;
            default:
                throw new \InvalidArgumentException;
        }
        $this->cashOutWeek += $cashEur;
        ++$this->cashOutCount;
        return ceil(($this->commissionOut($cash, $commissionMin, $currency)) * 100) / 100;
    }

    public function commissionIn($cash, $commissionMax)
    {
        if (!is_numeric($cash) || $cash < 0) {
            throw new \InvalidArgumentException;
        }
        
        $commission = 0.0003;
        $commissionSum = $cash * $commission;
        $commissionMin = 0.01;
        
        if ($commissionSum >= $commissionMax) {
            return $commissionMax;
        } elseif ($commissionSum <= $commissionMin) {
            return $commissionMin;
        } else {
            return $commissionSum;
        }
    }
    
    public function commissionOut($cash, $commissionMin, $currency)
    {
        $commission = 0.003;
        $commissionSum = $cash * $commission;
        if ($this->isLegal()) {
            if ($commissionSum < $commissionMin) {
                return $commissionMin;
            } else {
                return $commissionSum;
            }
        } else {
            if ($this->isOverCountLimit()) {
                return $commissionSum;
            } elseif ($this->isOverCashLimit()) {
                $cashOver = $this->cashOutWeek - $this->limit;
                $this->cashOutWeek = Converter::getEurLimit();
                switch ($currency) {
                    case "EUR":
                        return $cashOver * $commission;
                    case "USD":
                        return Converter::EurToUsd($cashOver) * $commission;
                    case "JPY":
                        return Converter::EurToJpy($cashOver) * $commission;
                    default:
                        throw new \InvalidArgumentException;
                }
            } else {
                return 0;
            }
        }
    }
}
