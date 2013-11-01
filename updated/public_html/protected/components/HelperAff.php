<?php

class HelperAff
{
    
    /**
     * register affid
     */
    public static function registerAffId()
    {
    	$affid = 0;
    	
    	if (isset(Yii::app()->session['affid']) && Yii::app()->session['affid'] > 1)
	        $affid = Yii::app()->session['affid'];

		if ( !$affid && isset(Yii::app()->request->cookies['affid']) )
		{
			$cookie = Yii::app()->request->cookies['affid'];
			$affid = $cookie->value;
			$affid = intval($affid);
		}

	    if ( (!$affid || $affid==1) && isset($_GET['affid']) && $_GET['affid'])
	    {
	    	if (
	    		(CAMS || CAM_DUMMY)
	    		&& 
	    		preg_match("/^1-/i", $_GET['affid']))
	    	{
	    		$affid = preg_replace("/^(1-)/i", "", $_GET['affid']);
		    	$affid = intval($affid);
	    	}
	    	else
	    		$affid = intval($_GET['affid']);
	    }
	    
		if (!$affid) $affid=1;
	    
		//check aff banned
		if ($affid>1 && !isset(Yii::app()->session['affid_checked']))
		{
			Yii::app()->session['affid_checked'] = true;
			
			$sql = "SELECT status FROM cs_users WHERE id=:id LIMIT 1";
            $affStatus = Yii::app()->dbSTATS->createCommand($sql)
            	->bindValue(":id", $affid, PDO::PARAM_INT)
            	->queryScalar();  			
			
            if (!$affStatus || $affStatus=='banned')
			{
				Yii::app()->user->setFlash('errorCustom', "THIS AFFILIATE HAS BEEN TERMINATED");
				
				Yii::app()->session['affid'] = 1;
				$affid=1;
				$cookie = new CHttpCookie('affid', $affid);
		        $cookie->expire = time()-60*60*24;
		        $cookie->domain = ".".DOMAIN_COOKIE;
		        Yii::app()->request->cookies['affid'] = $cookie;
				
				Yii::app()->controller->redirect( '/site/errors');
			}
		}

		
		
	    Yii::app()->session['affid'] = $affid;
	    
	    if ($affid>1)
	    {
			$cookie = new CHttpCookie('affid', $affid);
	        $cookie->expire = time()+60*60*24*30;
	        $cookie->domain = ".".DOMAIN_COOKIE;
	        Yii::app()->request->cookies['affid'] = $cookie; 	        
	    }
	    
	    
	    
	    
	    //sbc
	    if (!isset(Yii::app()->session['sbc']))
	    	Yii::app()->session['sbc'] = 0;
	    	
	    if ( isset($_GET['sbc']) && intval($_GET['sbc']) )
	    	Yii::app()->session['sbc'] = intval($_GET['sbc']);	    
	    
	    	
	    //register unique hits
	    if ($affid>1)
	    	self::registerUniqueHit();
	    
	    
	    return $affid;
    }
    
    
    /*
     * register unique hits
     */
    public static function registerUniqueHit()
    {
    	// register unique hit
		if (!isset(Yii::app()->session['unique_hit']))
		{
			Yii::app()->session['unique_hit'] = 1;
			
			$ip = CHelperLocation::getIPReal();			
		    
			$affid = Yii::app()->session['affid'];
		    $sbc = Yii::app()->session['sbc'];		    
		    $country = (isset($_SERVER['GEOIP_COUNTRY_CODE'])) ? $_SERVER['GEOIP_COUNTRY_CODE'] : "";
		    
			$sql = "INSERT IGNORE INTO uniq_hits (`date`, `ip`, `affid`, `sbc`, `country`) VALUES (CURDATE(), :ip, :affid, :sbc, :country)";
            Yii::app()->dbSTATS->createCommand($sql)
            	->bindValue(":ip", ip2long($ip), PDO::PARAM_STR)
            	->bindValue(":affid", $affid, PDO::PARAM_INT)
            	->bindValue(":sbc", $sbc, PDO::PARAM_INT)
            	->bindValue(":country", $country, PDO::PARAM_STR)	
            	->execute();    		        
		        
		    $hit_id = Yii::app()->dbSTATS->lastInsertId;
		    
		    $ref_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : "";
		    if ($ref_url)
		    {
		    	$sql = "INSERT IGNORE INTO uniq_refs (id, referer) VALUES (:id, :referer)";
		        Yii::app()->dbSTATS->createCommand($sql)
		        	->bindValue(":id", $hit_id, PDO::PARAM_INT)	
		        	->bindValue(":referer", $ref_url, PDO::PARAM_STR)
		        	->execute();
		    }
		    
		}    
    }
    
    
    
    
    
    
    
    
    static public function getManagerOfAff($affid)
    {
		$sql = "SELECT * FROM cs_users WHERE id=(SELECT manager_id FROM cs_users WHERE id=:id)";
		return Yii::app()->dbSTATS->createCommand($sql)
			->bindValue(":id", $affid, PDO::PARAM_INT)	
		    ->queryRow();    
    }
}
