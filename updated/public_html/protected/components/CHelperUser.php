<?php

class CHelperUser extends CApplicationComponent
{

	/**
	 * return id
	 */
	static function adminsFindAdmin($username, $password)
    {
    	$sql = "SELECT id FROM admins WHERE username=:username AND password=:password LIMIT 1";
        return Yii::app()->db->createCommand($sql)
		        	->bindValue(":username", $username, PDO::PARAM_STR)
		        	->bindValue(":password", $password, PDO::PARAM_STR)
		        	->queryScalar();
    }

	/**
	 * return admin data
	 */
	static function adminsGetAdminData($id)
    {
    	if ($id)
    	{
	    	$sql = "SELECT * FROM admins WHERE id=:id LIMIT 1";
	        return Yii::app()->db->createCommand($sql)
			        	->bindValue(":id", $id, PDO::PARAM_INT)
			        	->queryRow();
    	}
    }    
    
    
    //FORMS
    public static function adminsGetForms()
    {
    	return array(
    		'1'=>'1 - main form',
    		'88'=>'88 - api',
    		'93'=>'93 - allin1',
    		'cams'=>'cams',
    	);
    }
    public static function adminsGetFormsKeys()
    {
    	$keys = array();
    	foreach (self::adminsGetForms() as $k=>$v)
    	{
    		$keys[] = $k;
    	}
    	return $keys;
    }
    public static function adminsGetFormsForSelect()
    {
    	$res = array(''=>'All');
    	foreach (self::adminsGetForms() as $k=>$v)
    	{
    		$res[$k] = $v;
    	}
    	return $res;
    }
    
    
    
    
    
    
    
    
/*    
    static function isUserCreatedInDateRange($userId, $date1, $date2)
    {
		if (!$userId)
			return false;
    	
		$profile = new Profile($userId);
    	if ($profile->getDataValue('id'))
		{
	    	$activity = $profile->getDataValue('activity');
			$joined = strtotime($activity['joined']);
			$d1 = date("Y-m-d", strtotime($date1));
			$d2 = date("Y-m-d", strtotime($date2));
			$d1 = strtotime($d1." 00:00:00");
			$d2 = strtotime($d2." 23:59:59");
			
			return ($joined>=$d1 && $joined<=$d2);		
		}
		else
			return false;
    }
*/    
    
    
}
