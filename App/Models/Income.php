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
}