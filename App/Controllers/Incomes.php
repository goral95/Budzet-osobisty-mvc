<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income;
use \App\Flash;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Incomes extends Authenticated
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function newAction()
    {
		
        View::renderTemplate('Incomes/index.html', [
            'chosen' => 'income'
        ]);
    }
	
	public function addAction(){
		$income = new Income($_POST);
		if($income->add($this->user->id)){
			
			Flash::addMessage('Nowy przychód dodany!');
			
            $this -> redirect ('/przychody');
			
		} else {
			
			Flash::addMessage('Wystąpił błąd. Spróbuj jeszcze raz.', Flash::WARNING);
			
            $this -> redirect ('/przychody');
			
		}
	}
}
