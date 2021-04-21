<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense;
use \App\Models\Income;
/**
 * Home controller
 *
 * PHP version 7.0
 */
class Balance extends Authenticated{

    /**
     * Show the index page
     *
     * @return void
     */
    public function newAction(){
		$dates = static::getCurrentDateAction();
        $this->showTables($dates['startDate'], $dates['endDate']);
    }
	
	public function showAction(){
		$this->showTables($_POST['startDate'], $_POST['endDate']);
    }
		
	public function showTables($startDate, $endDate){
		$incomesFromPeriod = Income::getIncomesForBalance($startDate, $endDate , $this->user->id);
		$expensesFromPeriod = Expense::getExpensesForBalance($startDate, $endDate , $this->user->id);
		
		$incomeSum = static::calculateSum($incomesFromPeriod);
		$expenseSum = static::calculateSum($expensesFromPeriod);
		$balance = $incomeSum - $expenseSum;
		
		View::renderTemplate('Balance/index.html', [
			'chosen' => 'balance' ,
            'incomesFromPeriod' => $incomesFromPeriod,
			'expensesFromPeriod' => $expensesFromPeriod,
			'startDate' => $startDate,
			'endDate' => $endDate,
			'incomeSum' => $incomeSum,
			'expenseSum' => $expenseSum,
			'balance' => $balance
        ]);
		
	}
	
	public static function calculateSum($activitis = []){
		$sum = 0;
		foreach ($activitis as $activity) {
			$sum = $sum + $activity[1];
		}
		return $sum;
	}
	
	public static function getCurrentDateAction(){
		$month = date("m");
		$year = date("Y");
		$dates['startDate'] = $year . "-" . $month . "-01";
		if($month == 2){
		if($year % 4 == 0 &  $year % 100 != 0 || $year % 400 == 0)
			$dates['endDate'] = $year . "-" . $month . "-29";
		else
			$dates['endDate'] = $year . "-" . $month . "-28";
		}
		else if ($month == 4 || $month == 6 || $month == 9 || $month == 11)
			$dates['endDate'] = $year . "-" . $month . "-30";
		else
			$dates['endDate'] = $year . "-" . $month . "-31";
		
		return $dates;
	}
}

