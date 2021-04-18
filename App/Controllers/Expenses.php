<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense;
use \App\Flash;


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
		
        View::renderTemplate('Expenses/index.html', [
            'chosen' => 'expense'
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
}
