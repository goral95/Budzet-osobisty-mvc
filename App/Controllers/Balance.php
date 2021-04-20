<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense;
use \App\Models\Income;
use \App\Flash;


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
		
        View::renderTemplate('Balance/index.html', [
            'chosen' => 'balance'
        ]);
    }
	
	public function showAction(){
		$incomesFromPeriod = Income::getIncomesForBalance($_POST['startDate'], $_POST['endDate'] , $this->user->id);
		$expensesFromPeriod = Expense::getExpensesForBalance($_POST['startDate'], $_POST['endDate'] , $this->user->id);
		
		$incomeSum = static::calculateSum($incomesFromPeriod);
		$expenseSum = static::calculateSum($expensesFromPeriod);
		$balance = $incomeSum - $expenseSum;
		
		View::renderTemplate('Balance/index.html', [
			'chosen' => 'balance' ,
            'incomesFromPeriod' => $incomesFromPeriod,
			'expensesFromPeriod' => $expensesFromPeriod,
			'startDate' => $_POST['startDate'],
			'endDate' => $_POST['endDate'],
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
}
