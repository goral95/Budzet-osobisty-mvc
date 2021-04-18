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
class Balance extends Authenticated
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function newAction()
    {
		
        View::renderTemplate('Balance/index.html', [
            'chosen' => 'balance'
        ]);
    }
}
