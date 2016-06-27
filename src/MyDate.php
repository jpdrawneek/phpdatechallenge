<?php

  class MyDate {
    
    protected $daysInMonth = [1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31];

    public static function diff($start, $end) {

      $func = new MyDate();
      
      $startDate = $func->parseDate($start);
      $endDate = $func->parseDate($end);
      
      $totalMonths = 12;
      $years = $endDate->year - $startDate->year;
      $invert = !($years >= 0) || !($endDate->month - $startDate->month >= 0);

      $days = ($endDate->day - $startDate->day);
      
      $totalDays = 365;
      if ($years !== 0) {
        $totalDays += $years * 365;
        $totalDays += $func->daysInMonth($startDate->month);
        $totalMonths += 12 - 1;
      }
      $startMonths = range(1, $startDate->month);
      array_pop($startMonths);
      $totalDays -= $func->getDaysInMonthRange($startMonths);
      $totalMonths -= $startDate->month;
      $endMonths = range($endDate->month, 12);
      if ($startDate->month === $endDate->month) {
        array_shift($endMonths);
      }
      $totalDays -= $func->getDaysInMonthRange($endMonths);
      $totalMonths -= (12 - $endDate->month);
      
        if ($days > 0) {
          $totalDays -= $func->daysInMonth($startDate->month);
          $totalDays += $endDate->day - $startDate->day;
        } elseif ($days < 0) {
          $totalDays -= $func->daysInMonth($startDate->month);
          $totalDays += $startDate->day - $endDate->day;
          $invert = TRUE;
        }
      
      $totalYears = (int)floor($totalDays / 365);

      $totalDays += $func->addLeapDay($startDate, $endDate);
      
      return (object)array(
        'years' => $totalYears,
        'months' => $totalMonths,
        'days' => $days,
        'total_days' => $totalDays,
        'invert' => $invert
      );

    }
    
    protected function getDaysInMonthRange(array $monthList) {
      $totalDays = 0;
      foreach ($monthList AS $month) {
        $totalDays += $this->daysInMonth($month);
      }
      return $totalDays;
    }
    
    public function addLeapDay(stdClass $start, stdClass $end) {
      $extraDays = 0;
      if ($start->year <= $end->year) {
        $years = range($start->year, $end->year);
        $startMonth = $start->month;
        $endMonth = $end->month;
        $endDay = $end->day;
      } else {
        $years = range($end->year, $start->year);
        $startMonth = $end->month;
        $endMonth = $start->month;
        $endDay = $start->day;
      }
      for ($i=1; $i < count($years) -1; $i++) {
        if ($this->isLeapYear($years[$i])) {
          $extraDays++;
          var_dump($years[$i]);
        }
      }
      if ($this->isLeapYear($years[0]) && ($startMonth < 3 || ($endDay < 29 && $endMonth === 2))) {
          $extraDays++;
      }
      if ($this->isLeapYear($years[count($years) - 1]) && ($endMonth > 2 || ($endDay === 29 && $endMonth === 2))) {
          $extraDays++;
      }
      
      return $extraDays;
    }

    public function isLeapYear($year) {
      return ( ((int)$year % 100 === 0) && ((int)$year % 400 === 0) && (int)$year%4 === 0 ) || ( ((int)$year % 100 !== 0) && (int)$year % 4 === 0 );
    }

    public function daysInMonth($month) {
      return $this->daysInMonth[(int)$month];
    }
    
    public function parseDate($date) {
      list($year, $month, $day) = split('/', $date);
      return (object)array(
        'year' => $year,
        'month' => $month,
        'day' => $day
      );
    }
  }
