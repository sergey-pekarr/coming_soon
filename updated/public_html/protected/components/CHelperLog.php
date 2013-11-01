<?php

class CHelperLog extends CApplicationComponent
{
    

    static function logFile($fn, $values)
    {
    	if (is_array($values))
    		$values = var_export($values, true);
    	
    	$values .="\n";
    		
    	$filename = DIR_ROOT.'/../logs/'.$fn;
		$f = @fopen($filename, "a");
		if ($f)
		{
			fputs($f, $values);
			fclose($f);
			@chmod($filename, 0777);    	
		}
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
	static function adminActionsLog($admin_id, $controller, $action)
    {
    	if ($controller=='ajax' && $action=='update')
    		return;
    	
    	if (isset($_GET)) $data['GET'] = $_GET;
    	if (isset($_POST)) $data['POST'] = $_POST; 
    	if (isset($_SERVER['REQUEST_URI'])) $data['REQUEST_URI'] = $_SERVER['REQUEST_URI'];

    	//hiding some values
    	if (isset($data['POST']['AdminLoginForm']['password'])) $data['POST']['AdminLoginForm']['password'] = "******";
    	
    	if (isset($data))
    		$data = serialize($data);
    	else 	
    		$data = "";
    		
    	$ip = Yii::app()->location->getIPReal();
    	
    	if (!isset($admin_id)) $admin_id=0;
    	
    	
    	$sql = "INSERT DELAYED INTO log_admin (admin_id, controller, action, data, ip) VALUES (:admin_id, :controller, :action, :data, :ip)";
        Yii::app()->db->createCommand($sql)
        	->bindValue(":admin_id", $admin_id, PDO::PARAM_INT)
        	->bindValue(":controller", $controller, PDO::PARAM_STR)
        	->bindValue(":action", $action, PDO::PARAM_STR)
        	->bindValue(":data", $data, PDO::PARAM_STR)
        	->bindValue(":ip", $ip, PDO::PARAM_STR)
        	->execute();
    }
       
    
    
	static function adminGetLogEmails($post, $page=0)
    {
        switch  ($post['sort'])
        {
            case 'idASC':    $order = "id ASC"; break;
            case 'idDESC':   $order = "id DESC"; break;
            
            default:   $order = $post['sort']; break;
        }

        //new albums
        $where = array();
        $where[] = "added>='{$post['date1']} 00:00:00'";
        $where[] = "added<='{$post['date2']} 23:59:59'";
        if ($post['template'] && $post['template']!='all')
        	$where[] = "template='".$post['template']."'";
        $where = implode(' AND ', $where);
        
        $sql = "SELECT COUNT(id) FROM log_mail WHERE {$where}";
        $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();
//FB::warn($sql);        
        
        $sql = "SELECT * FROM log_mail
                WHERE {$where}
                ORDER BY {$order}
                LIMIT ".($page * $post['perPage']).", " . $post['perPage'];   

        $res['list'] = Yii::app()->db->createCommand($sql)->queryAll();

        
        return $res; 

    }
    



    static function adminGetLogApiOut($post, $page=0)
    {
    	/*switch  ($post['sort'])
    	{
    		case 'idASC':    $order = "id ASC"; break;
    		case 'idDESC':   $order = "id DESC"; break;
    
    		default:   $order = $post['sort']; break;
    	}*/
    
    	
    	$modelApi = new ApiOut();
    	$apis = $modelApi->apis;    	
//CHelperSite::vd($apis);    	
    	$log = array();
    	//$ts = $date1;
    	$date = $post['date2'];
    	do//for ($i = 0; $i < 31; $i++)
    	{
	    	//$date = date("Y-m-d", $ts);
	    	$l = array("date" => $date);
    		foreach ($apis as $api)
    		{
    			// oks
    			$sql = "SELECT COUNT(id) FROM `log_api_out` WHERE `date`='{$date}' AND api='{$api}' AND status=1";
    			$oks = Yii::app()->db->createCommand($sql)->queryScalar();
    	
    			// errors
    			$sql = "SELECT COUNT(id) FROM `log_api_out` WHERE `date`='{$date}' AND api='{$api}' AND status=-1";
    			$errors = Yii::app()->db->createCommand($sql)->queryScalar();
    	
    			$l[$api]['oks'] = $oks;
    			$l[$api]['errors'] = $errors;
    		}
    		$log[] = $l;
    		$date = date("Y-m-d", strtotime("-1 day", strtotime($date)));// strtotime("-1 day", $ts);
//CHelperSite::vd($date,0);    		
    	} 
    	while (strtotime($date)>=strtotime($post['date1'])/* && $post['date1']!=$post['date2'] && strtotime($post['date1'])<=strtotime($post['date2'])*/);
//CHelperSite::vd($log);
    	
    	/*$where = array();
    	$where[] = "`date`>='{$post['date1']}'";
    	$where[] = "`date`<='{$post['date2']}'";
    	$where = implode(' AND ', $where);
    
    	$sql = "SELECT COUNT(id) FROM log_api_out WHERE {$where}";
    	$res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();
    	//FB::warn($sql);
    
    	$sql = "SELECT * FROM log_api_out WHERE {$where}";
    
    	$res['list'] = Yii::app()->db->createCommand($sql)->queryAll();*/
    
    
    	return $log;
    
    }    
}
