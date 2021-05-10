<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense;
use \App\Flash;
use \App\Controllers\Balance;


/**
 * Home controller
 *
 * PHP version 7.0
 */
class Expenses extends Authenticated
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function newAction()
    {
		$expensesCategoryForUser = Expense::getExpensesCategoryForUser($this->user->id);
		$paymentMethodsForUser = Expense::getPaymentMethodsForUser($this->user->id);
        View::renderTemplate('Expenses/index.html', [
            'chosen' => 'expense' ,
			'expenseCategory' => $expensesCategoryForUser,
			'paymentMethods' => $paymentMethodsForUser
        ]);
    }
	
	public function addAction(){
		$expense = new Expense($_POST);
		if($expense->add($this->user->id)){
			
			Flash::addMessage('Nowy wydatek dodany!');
			
            $this -> redirect ('/wydatki');
			
		} else {
			
			Flash::addMessage('Wystąpił błąd. Spróbuj jeszcze raz.', Flash::WARNING);
			
            $this -> redirect ('/wydatki');
			
		}
	}
	
	public function limitAction(){
		$expenseCategory = $_POST['expenseCategory'];
		$expensePrice = $_POST['expensePrice'];
		$categoryLimit = Expense::getCategoryLimit($expenseCategory, $this->user->id);
		if($categoryLimit == 0)
			exit();
		else{
			$dates = Balance::getCurrentDateAction();
			$expenseSumForLimitCategory = Expense::getExpenseSumForLimitCategory($expenseCategory, $this->user->id, $dates['startDate'], $dates['endDate']);
			if(($expensePrice+$expenseSumForLimitCategory) <= $categoryLimit)
				echo '<span style = "color: green">Limit kategorii ' . $categoryLimit . ' zł. Możesz jeszcze wydać ' . $categoryLimit - $expensePrice - $expenseSumForLimitCategory. ' zł.</span>';
			else if(($expensePrice+$expenseSumForLimitCategory) > $categoryLimit)
				echo '<span style = "color: red">Limit kategorii ' . $categoryLimit . ' zł. Przekroczysz limit o ' . -($categoryLimit - $expensePrice - $expenseSumForLimitCategory) . ' zł.</span>';
		}
	}
}
