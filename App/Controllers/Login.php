<?php

namespace App\Controllers;

use \App\Auth;
use \App\Flash;
use \Core\View;
use \App\Models\User;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{

    /**
     * Show the login page
     *
     * @return void
     */
    public function newAction()
    {
		if(Auth::getUser()){
			$this -> redirect ('/');
		}else{
        View::renderTemplate('Login/new.html');
		}
    }

    /**
     * Log in a user
     *
     * @return void
     */
    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
	
		$remember_me = isset($_POST['remember_me']);
		
        if ($user) {
			
			Auth::login($user, $remember_me);
			
           $this->redirect(Auth::getReturnToPage());

        } else {
			
			Flash::addMessage('Niepoprawny email lub hasło', Flash::WARNING);
			
            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'] ,
				'remember_me' => $remember_me
            ]);
        }
    }
	
	public function destroyAction(){
		// Unset all of the session variables.
		Auth::logout();
		
		$this -> redirect ('/login/show-logout-message');
	}
	
	public function showLogoutMessageAction(){
		// Unset all of the session variables.
		Flash::addMessage('Wylogowano pomyślnie');
		
		$this -> redirect('/');
	}
}
