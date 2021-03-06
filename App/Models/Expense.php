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
	
	public static function getExpensesCategoryForUser($userId){
		$db = static::getDB();
		$query = $db -> query("SELECT id, name, category_limit FROM `expenses_category_assigned_to_users`  WHERE user_id = '{$userId}' AND deleted = 0");
		$expensesCategoryForUser= $query ->fetchAll();
		return $expensesCategoryForUser;
	}
	
	public static function getPaymentMethodsForUser($userId){
		$db = static::getDB();
		$query = $db -> query("SELECT id, name FROM `payment_methods_assigned_to_users` WHERE user_id = '{$userId}' AND deleted = 0");
		$paymentMethodsForUser= $query ->fetchAll();
		return $paymentMethodsForUser;
	}
	
	public static function getCategoryLimit($expenseCategory, $userId){
		$db = static::getDB();
		$query = $db -> query("SELECT category_limit FROM `expenses_category_assigned_to_users` WHERE user_id = '{$userId}' AND name = '{$expenseCategory}'");
		$categoryLimit= $query ->fetchColumn();
		return $categoryLimit;
	}
	
	public static function getExpenseSumForLimitCategory($expenseCategory, $userId, $startDate, $endDate){
		$db = static::getDB();
		$query = $db -> query("SELECT SUM(expenses.amount) FROM expenses_category_assigned_to_users, expenses WHERE expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id AND expenses.user_id = '{$userId}' AND expenses_category_assigned_to_users.name = '{$expenseCategory}' AND expenses.date_of_expense >= '{$startDate}' AND expenses.date_of_expense <= '{$endDate}' ");
		$expenseSumForLimitCategory= $query ->fetchColumn();
		return $expenseSumForLimitCategory;
	}
	
	public static function deletePaymentMethod( $categoryId ){
		$db = static::getDB();
		
		$sql = 'UPDATE payment_methods_assigned_to_users SET deleted = 1 WHERE id = :categoryId';
				
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);

        return $stmt->execute();
	}
	
	public static function editPaymentMethod( $categoryId , $categoryName ,  $userId){
		$db = static::getDB();
		$query = $db -> query ("SELECT * FROM payment_methods_assigned_to_users WHERE user_id = '{$userId}' AND name = '{$categoryName}'  AND id != '{$categoryId}' ");
		$repeatedCategory = $query -> fetchAll();
		if($repeatedCategory)
			return false;
		else{
			$sql = 'UPDATE payment_methods_assigned_to_users SET name = :categoryName WHERE id = :categoryId';
				
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

			return $stmt->execute();
		}
	}
	
	public static function addPaymentMethod( $categoryName ,  $userId){
		$db = static::getDB();
		$query = $db -> query ("SELECT * FROM payment_methods_assigned_to_users WHERE user_id = '{$userId}' AND name = '{$categoryName}'  AND deleted = 0");
		$repeatedCategory = $query -> fetchAll();
		if($repeatedCategory)
			return false;
		else{
			$sql = 'INSERT INTO  payment_methods_assigned_to_users VALUES ( NULL, :userId, :categoryName, false ) ';
				
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

			return $stmt->execute();
		}
	}
	
	public static function deleteExpenseCategory( $categoryId ){
		$db = static::getDB();
		
		$sql = 'UPDATE expenses_category_assigned_to_users SET deleted = 1 WHERE id = :categoryId';
				
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);

        return $stmt->execute();
	}
	
	public static function editExpenseCategory( $categoryId , $categoryLimit, $categoryName ,  $userId){
		$db = static::getDB();
		$query = $db -> query ("SELECT * FROM expenses_category_assigned_to_users  WHERE user_id = '{$userId}' AND name = '{$categoryName}'  AND id != '{$categoryId}' ");
		$repeatedCategory = $query -> fetchAll();
		if($repeatedCategory)
			return false;
		else{
			$sql = 'UPDATE expenses_category_assigned_to_users  SET name = :categoryName , category_limit = :categoryLimit WHERE id = :categoryId';
				
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
			$stmt->bindValue(':categoryLimit', $categoryLimit, PDO::PARAM_INT);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

			return $stmt->execute();
		}
	}
	
	public static function addExpenseCategory( $categoryName ,  $userId){
		$db = static::getDB();
		$query = $db -> query ("SELECT * FROM expenses_category_assigned_to_users WHERE user_id = '{$userId}' AND name = '{$categoryName}'  AND deleted = 0");
		$repeatedCategory = $query -> fetchAll();
		if($repeatedCategory)
			return false;
		else{
			$sql = 'INSERT INTO  expenses_category_assigned_to_users VALUES ( NULL, :userId, :categoryName, false , 0) ';
				
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

			return $stmt->execute();
		}
	}
	
	public static function deleteUserExpenseThings($userId){
		$db = static::getDB();
		$query = $db -> query ("DELETE FROM expenses WHERE user_id= '{$userId}' ");
		$query = $db -> query ("DELETE FROM expenses_category_assigned_to_users WHERE user_id= '{$userId}' ");
		$query = $db -> query ("DELETE FROM payment_methods_assigned_to_users WHERE user_id= '{$userId}' ");
	}
	
}