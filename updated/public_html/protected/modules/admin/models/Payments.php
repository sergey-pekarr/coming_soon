<?php

class Payments
{
        
    public static function getTodayGold($post, $page=0)
    {
        switch  ($post['sort'])
        {
            case 'idASC':    $order = "id ASC"; break;
            case 'idDESC':   $order = "id DESC"; break;
            
            default:   $order = $post['sort']; break;
        }
        
        
	    $sale = (isset($_GET['sale'])) ? $_GET['sale'] : "";//$sale = intval($_GET['sale']);
		if ($sale)
		{
		    $sale = addslashes($sale);
			$sql = "SELECT * FROM `pm_today_gold` WHERE id IN ({$sale}) ORDER BY FIELD(id,{$sale})";

			$res['count'] = count(explode(",", $sale));
			$res['countWaiting'] = 0;
		}
		else
		{
	        $where[] = "`date`>='{$post['date1']}'";
	        $where[] = "`date`<='{$post['date2']}'";
	        if ($post['aff'])
	            $where[] = "(affid={$post['aff']} OR manager_id={$post['aff']})";
	        if ($post['paymod'])
	            $where[] = "paymod='{$post['paymod']}'";
	        if ($post['status'])
	        {
	            if ($post['status']=='not_active')
	            	$where[] = "status<>'active'";
	            else
	        		$where[] = "status='{$post['status']}'";
	        }	            
	        $where = implode(" AND ", $where); 
	            
		    $sql = "SELECT COUNT(id) FROM `pm_today_gold` WHERE {$where} LIMIT 1";
	        $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();
	
		    $sql = "SELECT COUNT(id) FROM `pm_today_gold` WHERE `date`='0000-00-00' LIMIT 1";
	        $res['countWaiting'] = Yii::app()->db->createCommand($sql)->queryScalar();        
	        
	        $sql = "SELECT * FROM `pm_today_gold` WHERE {$where} ";
	        $sql.= " ORDER BY {$order}";		
		}
      
        $sql.= " LIMIT ".($page * $post['perPage']).", " . $post['perPage'];
      
        $users = Yii::app()->db->createCommand($sql)->queryAll();

        
	    if ($users)
		    foreach ($users as $k=>$v)
		    {
		        $users[$k]['sale_id'] = $v['id'];
		        $users[$k]['id'] = $v['user_id'];
	            
		        if ($v['ipFresh'])
		        {
		            $users[$k]['ipFresh'] = @unserialize($v['ipFresh']);
		            $users[$k]['ipFresh_str'] = $v['id'] .",". @implode(",",$users[$k]['ipFresh']);
		        }
	            
		            
		        if ($v['dublicate_sales_by_IP'])
		        {
		            $users[$k]['dublicate_sales_by_IP'] = @unserialize($v['dublicate_sales_by_IP']);
		            $users[$k]['dublicate_sales_by_IP_str'] = $v['id'] .",". @implode(",",$users[$k]['dublicate_sales_by_IP']);
		        }

		
		        if ($v['dublicate_sales_by_IP_B'])
		        {
		            $users[$k]['dublicate_sales_by_IP_B'] = @unserialize($v['dublicate_sales_by_IP_B']);
		            $users[$k]['dublicate_sales_by_IP_B_str'] = $v['id'] .",". @implode(",",$users[$k]['dublicate_sales_by_IP_B']);
		        }
           
		
		        if ($v['dublicate_sales_by_OS'])
		        {
		            $users[$k]['dublicate_sales_by_OS'] = @unserialize($v['dublicate_sales_by_OS']);
		            $users[$k]['dublicate_sales_by_OS_str'] = $v['id'] .",". @implode(",",$users[$k]['dublicate_sales_by_OS']);
		        }
		        if ($v['dublicate_sales_by_OS_2'])
		        {
		            $users[$k]['dublicate_sales_by_OS_2'] = @unserialize($v['dublicate_sales_by_OS_2']);
		            $users[$k]['dublicate_sales_by_OS_2_str'] = $v['id'] .",". @implode(",",$users[$k]['dublicate_sales_by_OS_2']);
		        }
		
		
		        $users[$k]['agent_last_login_text'] = "";
		        if ($v['agent_last_login']!="0000-00-00 00:00:00")
		        {
		            $users[$k]['agent_last_login_text'] = date("l jS \of F Y h:i:s A", strtotime($v['agent_last_login'])) ;  
		            $users[$k]['agent_last_login_text'].= "<br/>(".CHelperDate::date_distanceOfTimeInWords(strtotime($v['agent_last_login']), time()).' ago)';          
		        }
	        	
		
		        $users[$k]['last_update_lmw_text'] = CHelperDate::date_distanceOfTimeInWords(strtotime($v['last_update_lmw']), time()).' ago';
		        
	        
		        if ($v['billing_info'])
		        {
		            $users[$k]['billing_info'] = unserialize($v['billing_info']);  
		        }
		        
		        /*if ($v['fliptop_socialinfo'])
		        {
		            $users[$k]['fliptop_socialinfo'] = unserialize($v['fliptop_socialinfo']);
		        }*/        
		        
		        
		        
		    }        
        $res['list'] = $users;
FB::error($res);        
        return $res;    
    }
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public static function getTransactions($post, $page=0)
    {
        switch  ($post['sort'])
        {
            case 'idASC':    $order = "t.id ASC"; break;
            case 'idDESC':   $order = "t.id DESC"; break;
            
            default:   $order = $post['sort']; break;
        }
        
	    $where[] = "t.date>='{$post['date1']}'";
	    $where[] = "t.date<='{$post['date2']}'";
	    if ($post['paymod'])
	    	$where[] = "t.paymod='{$post['paymod']}'";
		if ($post['status'])
	    	$where[] = "t.status='{$post['status']}'";

		if ($post['form'])
			$where[] = "u.form='{$post['form']}'";		
		$where[] = "t.user_id=u.id";
		
	    $where = implode(" AND ", $where); 
	    
		$sql = "SELECT COUNT(t.id) FROM `pm_transactions` as t, users as u WHERE {$where} LIMIT 1";
	    $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();
	        
	    $sql = "SELECT t.*, u.form FROM `pm_transactions` as t, users as u WHERE {$where} ";
	    $sql.= " ORDER BY {$order}";		
      
        $sql.= " LIMIT ".($page * $post['perPage']).", " . $post['perPage'];
      
        $trns = Yii::app()->db->createCommand($sql)->queryAll();
		
        
        $res['nebilling_2']['3d_passed']=0;
        $res['nebilling_2']['3d_not_passed']=0;
        $res['nebilling_2']['3d_not_card']=0;
        
        //some specifics for paymods
	    if ($trns && $post['paymod'])
	    {
		    //per transaction
	    	foreach ($trns as $k=>$v)
		    {
		        switch ($v['paymod'])
		        {
		        	case 'netbilling_2':
		        		
		        		$sql = "SELECT * FROM `pm_nb_2_trn` WHERE trn_id={$v['id']} LIMIT 1";
		        		$trnPaymod = Yii::app()->db->createCommand($sql)->queryRow();
		        		
		        		$trns[$k]['paymodTrn'] = $trnPaymod;
	        		
		        		break; 
		        	
		        	
		        	default: break;
		        }
		    }
		    
		    
		    //totals
			switch ($post['paymod'])
		    {
		    	case 'netbilling_2':
		    		$where=array();
				    $where[] = "`date`>='{$post['date1']}'";
				    $where[] = "`date`<='{$post['date2']}'";
					$where[] = "`type`='initial'";
				    switch ($post['status'])
					{
						case 'completed': $where[] = "status='approved'"; break;
						default: break;
					}
				    $where = implode(" AND ", $where); 
					    		
		        	$sql = "SELECT * FROM `pm_nb_2_trn` WHERE {$where}";		        	
		        	$totals = Yii::app()->db->createCommand($sql)->queryAll();
/*if (DEBUG_IP)
	CHelperSite::vd($totals);*/
		        	if ($totals)
		        	{
		        		foreach ($totals as $t)
		        		{
				        		/*if ($t['responce_3D_PayerAuthenticationID']!=0)
				        		{
					        		if ($t['responce_3D_callback_code']==0)
					        			$res['nebilling_2']['3d_passed']++;
					        		else
					        			$res['nebilling_2']['3d_not_passed']++;
				        		}
				        		else
				        			$res['nebilling_2']['3d_not_card']++;*/
		        			
		        				switch ($t['passed_3D'])
		        				{
		        					//case '0':
		        					case 'no':
		        						$res['nebilling_2']['3d_not_passed']++;
		        						break;
		        					case 'yes': $res['nebilling_2']['3d_passed']++; break;
		        					case 'non3D': $res['nebilling_2']['3d_not_card']++; break;
		        				}
		        		}
		        	}
		        	break; 
		        	
		        	
		        	default: break;
			}
		    
	    }
	    

	    //some additional fields
	    if ($trns)
	    {
	    	//per transaction
	    	foreach ($trns as $k=>$v)
	    	{
	    		//extra info for rebills
	    		if ($v['status']=='renewal')
	    		{
	    			$sql = "SELECT added FROM `pm_transactions` WHERE user_id={$v['user_id']} AND `status`='completed' AND paymod='{$v['paymod']}' ORDER BY id DESC LIMIT 1";
	    			$trnInitialInfo = Yii::app()->db->createCommand($sql)->queryScalar();
	    			$trns[$k]['renewalInitialInfo'] = $trnInitialInfo;
	    			
	    		}
	    		
	    		
	    		//count of rebills
	    		$sql = "SELECT COUNT(id) FROM `pm_transactions` WHERE user_id={$v['user_id']} AND `status`='renewal' AND paymod='{$v['paymod']}'";
	    		$trns[$k]['renewalCount'] = Yii::app()->db->createCommand($sql)->queryScalar();
	    	}	    
	    }	    
	    
	    
        $res['list'] = $trns;
		
        return $res;    
    }
    
    
	public static function getRfdNChbkItems($date1, $date2, $page = 0){
		
		$fromDate = date('Y-m-d', strtotime($date1));
		$toDate = date('Y-m-d 23:59:59', strtotime($date2));
		
		$statdb = DB_NAME_STATS;
		
		$qry1 = "select manager_id as id, manager_name as login, 
			sum(case when status not in ('','') then 1 else 0 end) as paid,
			sum(case when status = 'active' then 1 else 0 end) as Active,
			0 as Refund,
			0 as Chargeback,
			0 as `Unknown`
		from pm_today_gold
			where '$fromDate' <= date and date <= '$toDate'
		group by manager_id, manager_name
		order by manager_name";
		
		$paidItems = Yii::app()->db->createCommand($qry1)->queryAll();
		if(!$paidItems) $paidItems = array();
		
		$qry2 = "select m.id, m.login, 0 as paid, 0 as Active
				, sum( case when locate('refund', r.`type`) > 0 then 1 else 0 end) as Refund
				, sum( case when locate('chargeback', r.`type`) > 0 then 1 else 0 end) as Chargeback
				, sum( case when locate('refund', r.`type`) = 0 and locate('chargeback', r.`type`) = 0 then 1 else 0 end) as `Unknown`
				from $statdb.reversals as r
				left join $statdb.cs_users as u on r.aff_id = u.id
				left join $statdb.cs_users as m on m.id = u.manager_id
				where '$fromDate' <= r.date and r.date <= '$toDate'
				group by m.id, m.login;
				order by m.login";		
		$refundItems = Yii::app()->db->createCommand($qry2)->queryAll();
		if(!$refundItems) $refundItems = array();
		
		$adminId = 0;
		$adminLogin = '';
		
		foreach($refundItems as $refundItem){
			if(!isset($refundItem['id']) || $refundItem['id'] == '') $refundItem['id'] = $adminId;
			if(!isset($refundItem['login']) || $refundItem['login'] == '') $refundItem['login'] = $adminLogin;
			self::findAndMergeRefundToPaid($paidItems, $refundItem);
		}
		
		$names = array();
		foreach($paidItems as $item){
			$names[] = $item['login'];
		}
		array_multisort($names, SORT_ASC, $paidItems);
		
		//Merge records and update value
		
		/*foreach($paidItems as &$paidItem){
			if(!$paidItem['paid']){
				$paidItem['percent'] = '';
			}
			else{
				$paidItem['percent'] = round(($paidItem['Refund'] + $paidItem['Chargeback'])/$paidItem['paid'] * 100).'%';
			}
		}*/
		foreach($paidItems as &$paidItem){
			//rebills 2013-03-21
			$sql = "SELECT COUNT(t.id) FROM `pm_transactions` as t, users as u WHERE t.`date`>='{$date1}' AND t.`date`<='{$date2}' AND t.`status`='renewal' AND u.id=t.user_id AND u.affid IN (SELECT id FROM ".DB_NAME_STATS.".cs_users WHERE manager_id=:manager_id)";
//if (DEBUG_IP) CHelperSite::vd($sql);			
			$paidItem['rebills'] = Yii::app()->db->createCommand($sql)->bindValue(":manager_id", $paidItem['id'], PDO::PARAM_INT)->queryScalar();
			
			
			
			if(!$paidItem['paid']){
				$paidItem['percent'] = '';
				$paidItem['CB_percent'] = 0;
				$paidItem['rebills_percent'] = 0 ; 
			}
			else{
				$paidItem['percent'] = round(($paidItem['Refund'] + $paidItem['Chargeback'])/$paidItem['paid'] * 100).'%';
				$paidItem['CB_percent'] = round(100*$paidItem['Chargeback']/$paidItem['paid']);
				$paidItem['rebills_percent'] = round(100*$paidItem['rebills']/$paidItem['paid']);
			}
			
			
		}		
		
		
		
		return $paidItems;
	}
	
	public static function findAndMergeRefundToPaid(&$paidItems, $refundItem){
		$id = $refundItem['id'];
		foreach($paidItems as &$paidItem){
			if($paidItem['id'] == $id){
				$paidItem['Refund'] += $refundItem['Refund'];
				$paidItem['Chargeback'] += $refundItem['Chargeback'];
				return;
			}
		}
		$paidItems[] = $refundItem;
	}
        
}



