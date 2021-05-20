<?php

namespace App\Models;

use PDO;

/**
 * User model
 *
 * PHP version 7.0
 */
class Income extends \Core\Model{

    public function __construct($data = []){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	public function add($userId){
		
		$db = static::getDB();
		$incomeCategoryIdAssingedToUserQuery = $db ->query ('SELECT id FROM `incomes_category_assigned_to_users` WHERE name = "'.$this->incomeCategory.'" and user_id = '.$userId );
		$incomeCategoryIdAssingedToUser = $incomeCategoryIdAssingedToUserQuery->fetchColumn();
		$addNewIncomeQuery = $db->prepare('INSERT INTO incomes VALUES (NULL, :userId, :incomeCategoryIdAssingedToUser, :incomePrice, :incomeDate, :remarkIncome)');
		$addNewIncomeQuery->bindValue(':userId', $userId ,PDO::PARAM_INT);
		$addNewIncomeQuery->bindValue(':incomeCategoryIdAssingedToUser', $incomeCategoryIdAssingedToUser, PDO::PARAM_INT);
		$addNewIncomeQuery->bindValue(':incomePrice', $this->incomePrice, PDO::PARAM_STR);
		$addNewIncomeQuery->bindValue(':incomeDate', $this->incomeDate, PDO::PARAM_STR);
		$addNewIncomeQuery->bindValue(':remarkIncome', $this->remarkIncome, PDO::PARAM_STR);
		
		return $addNewIncomeQuery->execute();
	}
	
	public static function getIncomesForBalance($startDate, $endDate , $userId){
		$db = static::getDB();
		$incomesFromPeriodQuery = $db -> query("SELECT incomes_category_assigned_to_users.name , SUM(incomes.amount) FROM incomes_category_assigned_to_users, incomes WHERE incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id AND incomes.user_id = '{$userId}' AND incomes.date_of_income >= '{$startDate}' AND incomes.date_of_income <= '{$endDate}' GROUP BY incomes_category_assigned_to_users.name ORDER BY SUM(incomes.amount) DESC ");
		$incomesFromPeriod= $incomesFromPeriodQuery ->fetchAll();
		return $incomesFromPeriod;
	}
	
	public static function getIncomesCategoryForUser($userId){
		$db = static::getDB();
		$query = $db -> query("SELECT id, name FROM `incomes_category_assigned_to_users`  WHERE user_id = '{$userId}' AND deleted = 0");
		$incomesCategoryForUser= $query ->fetchAll();
		return $incomesCategoryForUser;
	}
	
	public static function deleteIncomeCategory( $categoryId ){
		$db = static::getDB();
		
		$sql = 'UPDATE incomes_category_assigned_to_users SET deleted = 1 WHERE id = :categoryId';
				
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);

        return $stmt->execute();
	}
	
	public static function editIncomeCategory( $categoryId , $categoryName ,  $userId){
		$db = static::getDB();
		$query = $db -> query ("SELECT * FROM incomes_category_assigned_to_users WHERE user_id = '{$userId}' AND name = '{$categoryName}'  AND id != '{$categoryId}' ");
		$repeatedCategory = $query -> fetchAll();
		if($repeatedCategory)
			return false;
		else{
			$sql = 'UPDATE incomes_category_assigned_to_users SET name = :categoryName WHERE id = :categoryId';
				
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

			return $stmt->execute();
		}
	}
	
	public static function addIncomeCategory( $categoryName ,  $userId){
		$db = static::getDB();
		$query = $db -> query ("SELECT * FROM incomes_category_assigned_to_users WHERE user_id = '{$userId}' AND name = '{$categoryName}'  AND deleted = 0");
		$repeatedCategory = $query -> fetchAll();
		if($repeatedCategory)
			return false;
		else{
			$sql = 'INSERT INTO  incomes_category_assigned_to_users VALUES ( NULL, :userId, :categoryName, false ) ';
				
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
			$stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

			return $stmt->execute();
		}
	}
}