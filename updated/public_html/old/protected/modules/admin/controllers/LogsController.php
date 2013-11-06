<?php

class LogsController extends Controller
{
	public function actionIndex()
	{
		//$this->render('index');
	}

    public function actionEmails()
	{
		$model = new LogsEmailsForm;
        $model->attributes = (isset($_REQUEST['LogsEmailsForm'])) ? $_REQUEST['LogsEmailsForm'] : '';        
        if ($model->validate())
        {
            $page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
            $page = ($page) ? $page : 1;
            $page--;
FB::error($model->attributes);            
            $emails = CHelperLog::adminGetLogEmails($model->attributes, $page);
//FB::info($metrics);
            $pages=new CPagination($emails['count']);
            $pages->pageSize = $model->perPage;
            $pages->setCurrentPage($page);
        }
            
        $this->render('emails', array('model'=>$model, 'emails'=>$emails, 'pages'=>$pages));
	}
	
	

    public function actionEmailsBodyShow()
	{
        $id = $_POST['id'];

        $sql = "SELECT subject, body, html FROM log_mail WHERE id=:id LIMIT 1";
        $res = Yii::app()->db->createCommand($sql)
        	->bindValue(":id", $id, PDO::PARAM_INT)
        	->queryRow();
		
        $body = $res['body'];
		if ($res['html']=='0')
        	$body = preg_replace("/([\n\r]{1})/", '<br />', $body);
        	
        echo json_encode(array('subject'=>$res['subject'], 'body'=>$body));
        Yii::app()->end();
	}	
	
	
	
	
	public function actionApiOutLog()
	{
		$modelApi = new ApiOut();
		$apis = $modelApi->apis;		
		
		$model = new LogsApiOutForm;
		$model->attributes = (isset($_REQUEST['LogsApiOutForm'])) ? $_REQUEST['LogsApiOutForm'] : '';
		if ($model->validate())
		{
			/*$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
			$page = ($page) ? $page : 1;
			$page--;*/
			
			$log = CHelperLog::adminGetLogApiOut($model->attributes/*, $page*/);
			
			//FB::info($metrics);
			/*$pages=new CPagination($log['count']);
			$pages->pageSize = $model->perPage;
			$pages->setCurrentPage($page);*/
		}
		
		$this->render('apiout', array('model'=>$model, 'log'=>$log, 'apis'=>$apis/*, 'pages'=>$pages*/));
		/*return;
		
		
		
		
		
		$modelApi = new ApiOut();
		
		$apis = $modelApi->apis;

		if (isset($_GET['api']))
		{
		    $api = $_GET['api'];
		    $date = $_GET['date'];
		    $status = $_GET['log'];
			
		    $sql = "SELECT * FROM `log_api_out` WHERE `date`=:date AND api=:api AND status=:status";
		    $apiLog = Yii::app()->db->createCommand($sql)
		    			->bindValue(":date", 	$date, 		PDO::PARAM_STR)
		    			->bindValue(":api", 	$api, 		PDO::PARAM_STR)
		    			->bindValue(":status", 	$status, 	PDO::PARAM_INT)
		    			->queryAll();
		    
		    $this->render('apiout', array('apiLog'=>$apiLog, 'api'=>$api, 'date'=>$date));
		}
		else
		{
			$ts = time();
		    $log = array();
		    for ($i = 0; $i < 31; $i++)
		    {
		        $date = date("Y-m-d", $ts);
		        $l = array("date" => $date, 'cupid'=>'');
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
		        $ts = strtotime("-1 day", $ts);
		    }
		    
		    $this->render('apiout', array('log'=>$log, 'apis'=>$apis));
		}*/
	}
	
	
	public function actionApiOutLog_api()
	{
		if (isset($_GET['api']))
		{
			$api = $_GET['api'];
			$date = $_GET['date'];
			$status = $_GET['log'];
				
			$sql = "SELECT * FROM `log_api_out` WHERE `date`=:date AND api=:api AND status=:status";
			$apiLog = Yii::app()->db->createCommand($sql)
				->bindValue(":date", 	$date, 		PDO::PARAM_STR)
				->bindValue(":api", 	$api, 		PDO::PARAM_STR)
				->bindValue(":status", 	$status, 	PDO::PARAM_INT)
				->queryAll();
	
			$this->render('apiout', array('apiLog'=>$apiLog, 'api'=>$api, 'date'=>$date));
		}
	}
	
}