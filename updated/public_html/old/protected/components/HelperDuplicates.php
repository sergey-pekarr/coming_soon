<?php

class HelperDuplicates
{
   
    
    static public function checkExistedPaidIP($ip="", $days=30, $byApi=false)
    {
		if (DEBUG_IP) return false;
		
		if (!$ip && !$byApi) $ip=CHelperLocation::getIPReal();
    	
    	if (!$ip || !ip2long($ip)) return false;
    	
		
    	if ($ip=='71.71.246.96') return false;//email 2013-10-14   Please remove shelleys ip from the block list 
    	
    	
    	$sql = "
				SELECT t.user_id 
				FROM `users_info` as i, pm_transactions as t  
				WHERE 
					i.ip_signup_long=:ip_signup_long 
					AND 
					i.user_id=t.user_id
					AND
					t.`status` IN ('completed','completed_cams','authed','renewal')
					AND
					t.`date`>=(NOW() - INTERVAL {$days} DAY) 
				ORDER BY t.id DESC 
				LIMIT 1";
		$user_id = Yii::app()->db->createCommand($sql)
			->bindValue(":ip_signup_long", ip2long($ip), PDO::PARAM_INT)
			->queryScalar();

		$existed = (is_numeric($user_id) && $user_id>0);
//if (DEBUG_IP) $existed=true;
		if ($existed && !$byApi)
		{
			$profile = new Profile($user_id);
			
			//$mes = "Sorry you already signed up, you can't signup again";
			$mes = "Declined. Either you or someone else signed up recently from your ip. Please try again at a later date or login to your account now then upgrade.";
			//$mes = "Declined. Please try again at a later date.";			
			
			if (Yii::app()->user->id==0)
			{
				$mes.="<br /><br />";
				if (
					CAMS 
					&& 
					$profile->getDataValue('form')=='cams' 
					&& 
					$profile->getDataValue('settings','cams_joined')=='1'
				)
					$url = CAM_SITE_URL."/login.php";
				else
					$url = SITE_MAIN_URL."/site/login";
				$mes.="Login <a href='".$url."'>here</a>";
			}
				
			Yii::app()->user->setFlash('regAlreadyRegistered', 'You are already registered member. Login please.');
			Yii::app()->user->setFlash('errorCustom', $mes);
		}
		
		return $existed;    
    }
}
