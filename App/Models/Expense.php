<?php

namespace App\Models;

use PDO;

/**
 * User model
 *
 * PHP version 7.0
 */
class Expense extends \Core\Model{

    public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	public function add($userId){
		
		$db = static::getDB();
		$expenseCategoryIdAssingedToUserQuery = $db ->query ('SELECT id FROM `expenses_category_assigned_to_users` WHERE name = "'.$this->expenseCategory.'" and user_id = '.$userId);
		$expenseCategoryIdAssingedToUser = $expenseCategoryIdAssingedToUserQuery->fetchColumn();
		$paidTypeIdAssingedToUserQuery = $db ->query ('SELECT id FROM `payment_methods_assigned_to_users` WHERE name = "'.$this->paidType.'" and user_id = '.$userId );
		$paidTypeIdAssingedToUser = $paidTypeIdAssingedToUserQuery->fetchColumn();
		$addNewExpenseQuery = $db->prepare('INSERT INTO expenses VALUES (NULL, :userId, :expenseCategoryIdAssingedToUser, :paidTypeIdAssingedToUser, :expensePrice, :expenseDate, :remarkExpense)');
		$addNewExpenseQuery->bindValue(':userId', $userId ,PDO::PARAM_INT);
		$addNewExpenseQuery->bindValue(':expenseCategoryIdAssingedToUser', $expenseCategoryIdAssingedToUser, PDO::PARAM_INT);
		$addNewExpenseQuery->bindValue(':paidTypeIdAssingedToUser', $paidTypeIdAssingedToUser, PDO::PARAM_INT);
		$addNewExpenseQuery->bindValue(':expensePrice', $this->expensePrice, PDO::PARAM_STR);
		$addNewExpenseQuery->bindValue(':expenseDate', $this->expenseDate, PDO::PARAM_STR);
		$addNewExpenseQuery->bindValue(':remarkExpense', $this->remarkExpense, PDO::PARAM_STR);
		
		return $addNewExpenseQuery->execute();
	}
	
	public static function getExpensesForBalance($startDate, $endDate , $userId){
		$db = static::getDB();
		$expensesFromPeriodQuery = $db -> query("SELECT expenses_category_assigned_to_users.name , SUM(expenses.amount) FROM expenses_category_assigned_to_users, expenses WHERE expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id AND expenses.user_id = '{$userId}' AND expenses.date_of_expense >= '{$startDate}' AND expenses.date_of_expense <= '{$endDate}' GROUP BY expenses_category_assigned_to_users.name ORDER BY SUM(expenses.amount) DESC ");
		$expensesFromPeriod= $expensesFromPeriodQuery ->fetchAll();
		return $expensesFromPeriod;
	}
}