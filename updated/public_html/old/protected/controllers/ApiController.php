<?php

class ApiController extends Controller
{

	public function init()
    {
        parent::init();
        $this->layout='//layouts/ajax';
    }		
	
	
	/*
	 * register new user from API
	 */
	public function actionSignup()
	{
		if (!$_POST)
			Yii::app()->end();
		
		$model = new ApiSignup();
		$model->attributes=$_POST;
		
		//log $_POST
		$affid = (isset($_POST['affid'])) ? intval($_POST['affid']) : 0;
		$ip = CHelperLocation::getIPReal();
		$sql = "INSERT INTO `api_log` (ip, log, affid) VALUES (:ip, :log, :affid)";
        Yii::app()->db->createCommand($sql)        
            ->bindValue(":ip", $ip, PDO::PARAM_STR)
            ->bindValue(":log", serialize($_POST), PDO::PARAM_STR)
            ->bindValue(":affid", $affid, PDO::PARAM_INT)
            ->execute(); 
		$recordId = Yii::app()->db->lastInsertId;
            
		
		
		$user_id = 0;
		$error_text  = "";
		$result = "";
		
		if($model->validate())
        {
        	$user_id = $model->doApiRegistration();
        	
        	if ($user_id)
        	{
        		$result = CHelperProfile::getAutoLoginUrl( $user_id );
        		
        		//update log
        		if ($recordId)
        		{
					$sql = "UPDATE `api_log` SET user_id=:user_id WHERE id=:id LIMIT 1";
			        Yii::app()->db->createCommand($sql)        
			            ->bindValue(":user_id", $user_id, PDO::PARAM_INT)
			            ->bindValue(":id", $recordId, PDO::PARAM_INT)
			            ->execute();         		
        		}
        	}		
        }
        else
        {
        	$error_text = json_encode( $model->errors );
        }
        
        if (!$result)
        	$result = "error"; //!!!
        
        echo $result."\n".$error_text;
        //die("{$result}\n{$error_text}");
	}

	
	
	/*
	 * use for creating new key
	 */
	public function actionKeyTest()
	{
		if (DEBUG_IP && isset($_GET['key']))
			echo strtoupper(substr(md5(SALT . $_GET['key']), 15, 16));
	}
}