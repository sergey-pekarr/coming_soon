<?php

class Users
{
	public static function getUsers($post, $page=0)
	{
		
		switch  ($post['sort'])
		{
			case 'idASC':    $order = "u.id ASC"; break;
			case 'idDESC':   $order = "u.id DESC"; break;
			
			default:   $order = $post['sort']; break;
		}
		

		$where[] = "a.joined>='{$post['date1']} 00:00:00'";
		$where[] = "a.joined<='{$post['date2']} 23:59:59'";
		$where[] = "u.id = a.user_id";
		if ($post['userRole'])
			$where[] = "u.role='".$post['userRole']."'";
		if ($post['form'])
			$where[] = "u.form='".$post['form']."'";
		
		$affWhere = $where;
		
		if($post['affid']){
			$where[] = "u.affid='".$post['affid']."'";
		}
		
		$sql = "SELECT COUNT(u.id) FROM users as u, users_activity as a";
		if (isset($where) && !empty($where))
			$sql.= " WHERE " . implode(' AND ', $where);
		$sql.= " LIMIT 1";
		
		$res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();

		
		
		
		$sql = "SELECT u.id FROM users as u, users_activity as a";
		if (isset($where) && !empty($where))
			$sql.= " WHERE " . implode(' AND ', $where);
		$sql.= " ORDER BY {$order}";
		$sql.= " LIMIT ".($page * $post['perPage']).", " . $post['perPage'];
		
		$res['list'] = Yii::app()->db->createCommand($sql)->queryColumn();
		
		//n
		$dbstats = DB_NAME_STATS;
		$sql = "SELECT u.affid, t.login, count(*) as count FROM users as u, users_activity as a, $dbstats.cs_users as t";
		$affWhere[] = "t.id = u.affid";
		if (isset($affWhere) && !empty($affWhere))
			$sql.= " WHERE " . implode(' AND ', $affWhere)." group by u.affid, t.login order by t.login";
		$affItems = Yii::app()->db->createCommand($sql)->queryAll();
		
		$affs = array('' => 'All');
		if($affItems){
			foreach($affItems as $affItem){
				$affs[$affItem['affid']] = "{$affItem['login']} [{$affItem['affid']}] ({$affItem['count']})";
			}
		}
		$res['affs'] = $affs;		
		
		return $res;
	}

    
    public static function getImagesNotApproved($post, $page=0)
    {
        $sql = "SELECT COUNT(user_id) FROM user_image WHERE approved='0'";        
        $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();    	
    	
    	$sql = "SELECT * FROM user_image WHERE approved='0' ORDER BY user_id DESC, n DESC";
    	$sql.= " LIMIT ".($page * $post['perPage']).", " . $post['perPage'];    	
    	$res['list'] = Yii::app()->db->createCommand($sql)->queryAll();
    	
    	return $res;
    }
    
    
    public function getImagesXrate($post, $page=0)
    {
        if ($post['type']=='') $where = "xrated='0' OR xrated=''";
    	if ($post['type']=='clothed') $where = "xrated='clothed'";
        if ($post['type']=='naked') $where = "xrated='naked'";
    	
    	$sql = "SELECT COUNT(user_id) FROM user_image WHERE {$where}";        
        $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();    	
    	
    	$sql = "SELECT * FROM user_image WHERE {$where} ORDER BY user_id DESC, n DESC";
    	$sql.= " LIMIT ".($page * $post['perPage']).", " . $post['perPage'];    	
    	$res['list'] = Yii::app()->db->createCommand($sql)->queryAll();
    	
    	return $res;
    }    
    
    /*
    public function gerReportedAbuse()
    {
        return Yii::app()->db->createCommand("SELECT id, id_to FROM admin_report_abuse WHERE hided='0' ORDER BY id DESC LIMIT 100")->queryAll();                
    }
    public function hideReportAbuse($reportId)
    {
        return Yii::app()->db->createCommand("UPDATE admin_report_abuse SET hided='1' WHERE id=:id")
            ->bindParam('id', $reportId, PDO::PARAM_INT)
            ->execute();                
    }  */  
	
    
    
    public function findUsers($post)
    {
        $res = array();
    	
    	
    	if ($post['userId'])
    	{
	    	$sql = "SELECT id FROM users WHERE id=:userId LIMIT 1";
	    	$id = Yii::app()->db->createCommand($sql)
	    		->bindValue(":userId", 	trim($post['userId']), PDO::PARAM_INT)
	    		->queryScalar();

	    	if ($id) $res[] = $id;
    	}
		
        if ($post['username'])
    	{
	    	$id = Profile::usernameExist(trim($post['username']));
	    	if ($id) $res[] = $id;
    	}
    		
        if ($post['email'])
    	{
	    	//from users table
	    	$email = strtolower(trim($post['email']));//!!!
	    	
	    	$id = Profile::emailExist($email);
	    	if ($id) $res[] = $id;
	    	
	    	
	    	//from transactions
	    	$sql = "SELECT DISTINCT(user_id) FROM `pm_transactions` WHERE LOWER(email)=:email";//LOWER!!!
	    	$ids = Yii::app()->db->createCommand($sql)
	    		->bindValue(":email", $email, PDO::PARAM_STR)
	    		->queryColumn();
	    	
	    	if ($ids) 
	    		foreach ($ids as $id)
	    			$res[] = $id;
    	}    	
    		
        if ($post['profileIdEncr'])
    	{
	    	$id = CSecur::decryptID( trim($post['profileIdEncr']) );
	    	if ($id) $res[] = $id;
    	} 
    	
    	
    	if ($post['ref_domain'])
    	{
			$sql = "SELECT user_id FROM `users_info` WHERE ref_domain like :ref_domain ORDER BY user_id DESC LIMIT 1000";
			$ids = Yii::app()->db->createCommand($sql)
						->bindValue(':ref_domain', '%'.$post['ref_domain'].'%', PDO::PARAM_STR)
						->queryColumn();
			
	    	if ($ids) 
	    		foreach ($ids as $id)
	    			$res[] = $id;
    	}    	
    	
    	
    	//from transactions:
        if ($post['netbilling_2_member_id'])
	    {
			$sql = "SELECT user_id FROM `pm_nb_2_trn` WHERE member_id='".intval($post['netbilling_2_member_id'])."' LIMIT 1";
	    	$id = Yii::app()->db->createCommand($sql)->queryScalar();
	        if ($id) $res[] = $id;	    	
	    }
	    if ( $post['zombaio_sub_id'])
	    {
			$sql = "SELECT user_id FROM `pm_zombaio_postback` WHERE sub_id='".intval($post['zombaio_sub_id'])."' AND action='user.add' LIMIT 1";
	    	$id = Yii::app()->db->createCommand($sql)->queryScalar();
	        if ($id) $res[] = $id;    	
	    }
	
	    if ( $post['vendo_trans_id'])
	    {
	    	$sql = "SELECT user_id FROM `pm_vendo_postback` WHERE `trans_transid` =".intval($post['vendo_trans_id'])." LIMIT 1";
	    	$id = Yii::app()->db->createCommand($sql)->queryScalar();
	        if ($id) $res[] = $id;	    	
	    }    	
    	
    	if ( $post['wd_GUWID'])
	    {
	    	$sql = "SELECT user_id FROM `pm_wirecard_trn` WHERE GuWID=:GuWID LIMIT 1";
	    	$id = Yii::app()->db->createCommand($sql)
	    		->bindValue(":GuWID", trim($post['wd_GUWID']), PDO::PARAM_STR)
	    		->queryScalar();
	        if ($id) $res[] = $id;		    	
	    }    	
    	
            
        if ($post['ccname1'] || $post['ccname2'])
    	{
	    	$ccname1 = strtolower(trim($post['ccname1']));
	    	$ccname2 = strtolower(trim($post['ccname2']));
	    	$ccname = $ccname1." ".$ccname2;
	    	
    		if ($post['ccname1'] && $post['ccname2'])
	    	{
		    	$sql = "SELECT DISTINCT(user_id) FROM `pm_transactions` WHERE (LOWER(firstname)=:firstname AND LOWER(lastname)=:lastname) OR LOWER(ccname)=:ccname";//LOWER!!!
		    	$ids = Yii::app()->db->createCommand($sql)
		    		->bindValue(":firstname", $ccname1, PDO::PARAM_STR)
		    		->bindValue(":lastname", $ccname2, PDO::PARAM_STR)
		    		->bindValue(":ccname", $ccname, PDO::PARAM_STR)
		    		->queryColumn();
		    	
		    	if ($ids) 
		    		foreach ($ids as $id)
		    			$res[] = $id;	    		
	    	}
	    	
	    	//later... $ccname1 or $ccname2 ...
    	}	    
	    
	    $res = array_unique($res);
	    
        return $res;
    }    
    
    
    
    

    
    
}



