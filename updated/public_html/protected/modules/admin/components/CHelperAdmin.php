<?php
class CHelperAdmin extends CApplicationComponent 
{
	
    /**
     * SUMMARY
     */    
    static function getSummary($post)
    {
        $where = array();
        $where[] = "a.joined>='{$post['date1']} 00:00:00'";
        $where[] = "a.joined<='{$post['date2']} 23:59:59'";
        $where[] = "a.user_id = u.id";
        $where[] = "u.role IN ('free', 'gold')";
        $where[] = "u.promo='0'";
        $where = implode(' AND ', $where);
		
        //new users
		$sql = "SELECT COUNT(a.user_id) FROM users_activity as a, users as u WHERE {$where}";
		$res['newUsers'] = Yii::app()->db->createCommand($sql)->queryScalar();
     
      
        
        $where = array();
        $where[] = "a.joined>='{$post['date1']} 00:00:00'";
        $where[] = "a.joined<='{$post['date2']} 23:59:59'";
        $where[] = "f.user_id = a.user_id";
        $where[] = "f.user_id = u.id";
        //$where[] = "u.role IN ('guest', 'user')";
        $where = implode(' AND ', $where);

        $sql = "SELECT COUNT(a.user_id) FROM users_activity as a, users as u, ext_FB as f WHERE {$where} LIMIT 1";
        $res['newUsersFromFB'] = Yii::app()->db->createCommand($sql)->queryScalar();         
        
        
        
		//returnedUsers  
        $where = array();
        $where[] = "a.activityLast>='{$post['date1']} 00:00:00'";
        $where[] = "a.activityLast<='{$post['date2']} 23:59:59'";

        $where[] = "a.joined<a.activityLast";//$where[] = "a.joined<'{$post['date1']} 00:00:00'";
        
        $where[] = "a.user_id = u.id";
        $where[] = "u.role IN ('free', 'gold')";
        $where[] = "u.promo='0'";
        $where = implode(' AND ', $where);
		
        //new users
		$sql = "SELECT COUNT(a.user_id) FROM users_activity as a, users as u WHERE {$where}";
		$res['returnedUsers'] = Yii::app()->db->createCommand($sql)->queryScalar();

		
		
		
        
        //TOTALs
        $sql = "SELECT COUNT(id) FROM users";
        $res['totalUsers'] = Yii::app()->db->createCommand($sql)->queryScalar();        

        $sql = "SELECT COUNT(id) FROM users WHERE promo='1'";
        $res['totalPromo'] = Yii::app()->db->createCommand($sql)->queryScalar();         
        
        $res['totalUsers'] -= $res['totalPromo'];
        
        $sql = "SELECT COUNT(user_id) FROM ext_FB";
        $res['totalUsersFromFB'] = Yii::app()->db->createCommand($sql)->queryScalar();         

        
        //images
        $sql = "SELECT COUNT(user_id) FROM user_image";
        $res['totalImages'] = Yii::app()->db->createCommand($sql)->queryScalar();         
        
        $sql = "SELECT COUNT(user_id) FROM user_image WHERE approved='0'";
        $res['totalImagesNotApproved'] = Yii::app()->db->createCommand($sql)->queryScalar();         

        $sql = "SELECT COUNT(user_id) FROM user_image WHERE xrated='0'";
        $res['totalImagesNotRated'] = Yii::app()->db->createCommand($sql)->queryScalar();      
		   
		$fanGirlQry = "select count(*) from users_fan_settings where status = 'pending'";
		$totalFan = Yii::app()->db->createCommand($fanGirlQry)->queryScalar();
		if(!$totalFan) $totalFan = 0;
		$res['totalFanFirl'] = $totalFan;
		
		$riskQry = "select count(*) 
				from pm_today_risk 
				where resolve = '0' 
				and (duplicate_by_location is not null 
					 or duplicate_by_figure_print is not null)";
		$totalRisk = Yii::app()->db->createCommand($riskQry)->queryScalar();
		if(!$totalRisk) $totalRisk = 0;
		$res['totalRisk'] = $totalRisk;
		
        return $res;
        
    }   	
	
	
	
    
    
	public static function getForms($dropdown=false)
	{
		if (!$dropdown)
			return array('1','88','93','932','cams');
		else
			return array(''=>'All','1'=>'1 - main form','88'=>'88 - api','93'=>'93 - zsignup','932'=>'932 - zsignup2', 'cams'=>'cams');
	}
}