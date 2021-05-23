<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\User;
use \App\Flash;
use \App\Auth;
use \App\Login;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Settings extends Authenticated
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function newAction(){
		$incomesCategoryForUser = Income::getIncomesCategoryForUser($this->user->id);
		$expensesCategoryForUser = Expense::getExpensesCategoryForUser($this->user->id);
		$paymentMethodsForUser = Expense::getPaymentMethodsForUser($this->user->id);
        View::renderTemplate('Settings/index.html', [
		'chosen' => 'settings' ,
		'user' => $this->user ,
		'incomeCategory' => $incomesCategoryForUser ,
		'expenseCategory' => $expensesCategoryForUser ,
		'paymentMethods' => $paymentMethodsForUser
		]);
    }
	
	public function refreshIncomeCategoriesAction(){
		$incomesCategoryForUser = Income::getIncomesCategoryForUser($this->user->id);
        View::renderTemplate('Settings/income-categories.html', [
		'incomeCategory' => $incomesCategoryForUser
		]);
    }
	
	public function refreshExpenseCategoriesAction(){
		$expensesCategoryForUser = Expense::getExpensesCategoryForUser($this->user->id);
        View::renderTemplate('Settings/expense-categories.html', [
		'expenseCategory' => $expensesCategoryForUser
		]);
    }
	
	public function refreshPaymentMethodsAction(){
		$paymentMethodsForUser = Expense::getPaymentMethodsForUser($this->user->id);
        View::renderTemplate('Settings/payment-methods.html', [
		'paymentMethods' => $paymentMethodsForUser
		]);
    }
	
	public function deleteIncomeCategoryAction(){
		if(Income::deleteIncomeCategory( $_POST['categoryId'] ))
			echo '<span style = "color:green;">Kategoria została usunięta </span>' ;
		else
			echo '<span style = "color:red;">Wystąpił błąd. Spróbuj ponownie </span>';
	}
	
	public function deleteExpenseCategoryAction(){
		if(Expense::deleteExpenseCategory( $_POST['categoryId'] ))
			echo '<span style = "color:green;">Kategoria została usunięta </span>' ;
		else
			echo '<span style = "color:red;">Wystąpił błąd. Spróbuj ponownie </span>';
	}
	
	public function deletePaymentMethodAction(){
		if(Expense::deletePaymentMethod( $_POST['categoryId'] ))
			echo '<span style = "color:green;">Metoda została usunięta </span>' ;
		else
			echo '<span style = "color:red;">Wystąpił błąd. Spróbuj ponownie </span>';
	}
	
	public function makeCategoryFormat( $categoryName){
		$rightFormat = strtolower($categoryName);
		$rightFormat = ucfirst($rightFormat);
		return $rightFormat;
	}
	
	public function editIncomeCategoryAction(){
		$categoryName = $this->makeCategoryFormat($_POST['categoryName']);
		
		if(Income::editIncomeCategory( $_POST['categoryId'], $categoryName ,  $this->user->id))
			echo '<span style = "color:green;">Kategoria została zapisana </span>' ;
		else
			echo '<span style = "color:red;">Taka kategoria już istnieje </span>';
	}
	
	public function editPaymentMethodAction(){
		$categoryName = $this->makeCategoryFormat($_POST['categoryName']);
		
		if(Expense::editPaymentMethod( $_POST['categoryId'], $categoryName ,  $this->user->id))
			echo '<span style = "color:green;">Metoda została zapisana </span>' ;
		else
			echo '<span style = "color:red;">Taka metoda już istnieje </span>';
	}
	
	public function editExpenseCategoryAction(){
		$categoryName = $this->makeCategoryFormat($_POST['categoryName']);
		if($_POST['categoryLimit'] >= 0){
		if(Expense::editExpenseCategory( $_POST['categoryId'], $_POST['categoryLimit'], $categoryName ,  $this->user->id))
			echo '<span style = "color:green;">Kategoria została zapisana </span>' ;
		else
			echo '<span style = "color:red;">Taka kategoria już istnieje </span>';
		}else{
			echo '<span style = "color:red;">Limit musi być liczbą większą lub równą zero</span>';
		}
	}
	
	public function addIncomeCategoryAction(){
		$categoryName = $this->makeCategoryFormat($_POST['categoryName']);
		
		if(Income::addIncomeCategory( $categoryName ,  $this->user->id)){
			Flash::addMessage('Kategoria została dodana');
			
            $this -> redirect ('/ustawienia');
		}else{
			Flash::addMessage('Taka kategoria już istnieje', Flash::WARNING);
			
            $this -> redirect ('/ustawienia');
		}
	}
	
	public function addPaymentMethodAction(){
		$categoryName = $this->makeCategoryFormat($_POST['categoryName']);
		
		if(Expense::addPaymentMethod( $categoryName ,  $this->user->id)){
			Flash::addMessage('Metoda płatności została dodana');
			
            $this -> redirect ('/ustawienia');
		}else{
			Flash::addMessage('Taka metoda już istnieje', Flash::WARNING);
			
            $this -> redirect ('/ustawienia');
		}
	}
	
	public function addExpenseCategoryAction(){
		$categoryName = $this->makeCategoryFormat($_POST['categoryName']);
		if(Expense::addExpenseCategory( $categoryName ,  $this->user->id)){
			Flash::addMessage('Kategoria została dodana');
			
            $this -> redirect ('/ustawienia');
		}else{
			Flash::addMessage('Taka kategoria już istnieje', Flash::WARNING);
			
            $this -> redirect ('/ustawienia');
		}
		
	}
	
	public function editUserNameAction(){
		
		if(User::editUserName( $_POST['userName'], $this->user->id))
			echo '<span style = "color:green;">Nazwa została zapisana </span>' ;
		else
			echo '<span style = "color:red;">Wystąpił błąd, spróbuj ponownie </span>';
	}
	
	public function resetAccountAction(){
		Income::deleteUserIncomeThings($this->user->id);
		Expense::deleteUserExpenseThings($this->user->id);
		User::setDefaultCategories($this->user->id);
		Flash::addMessage('Konto zostało zresetowane');
        $this -> redirect ('/ustawienia');
	}
	
	public function deleteAccountAction(){
		Income::deleteUserIncomeThings($this->user->id);
		Expense::deleteUserExpenseThings($this->user->id);
		User::deleteUser($this->user->id);
		Auth::logout();
		$this -> redirect('/login/show-delete-message');
	}
	
	public function changePasswordAction(){
		if($this->user -> resetPassword($_POST['password'])){
			echo '<span style = "color:green;">Hasło zmienione pomyślnie </span>' ; 
		}else{
			echo '<span style = "color:red;">Zły format hasła, spróbuj ponownie </span>';
		}
	}
}
