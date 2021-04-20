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
}