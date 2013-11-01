<?php
class EmailCommand extends CConsoleCommand
{
    
    private $timeStart; 
    private $timeMax=55;
    private $usleepDelay;
    
    public function init() 
    { 
        if (SITE_UNDER_UPDATE) 
            Yii::app()->end();
        
		$this->usleepDelay = (LIVE) ? 50 : 1000;
            
        $this->timeStart = time();    
    }

    
    
    
    //  /home/dating/pinkmeets.com/public_html/protected/console email Send
    //  /home/onl/domains/onl.lo/public_html/protected/console email Send   
    
    public function actionSend() 
    {

        $logId = Yii::app()->helperCron->onStartCron('email','Send');        

        $sql = "SELECT * FROM log_mail WHERE sent='0' ORDER BY RAND() LIMIT 1000";
        $toSend = Yii::app()->db->createCommand($sql)->queryAll();
        
        $res = array();
        
        if ($toSend)
        {
			$res = array();
				
            foreach ($toSend as $row)
            {
                        if ( (time()-$this->timeStart)>=$this->timeMax )
                            break;
                        
                        /*switch($row['template'])
                        {
                        	case 'welcome':
                        	case 'passwordReset':
                        		Yii::app()->mail->SendHtmlMail($row);
                        		break;
                        	
                        	default:
                        		Yii::app()->mail->SendText($row);
                        }*/
                            
						$row['data'] = unserialize($row['data']);
						Yii::app()->mail->SendHtmlMail($row);
						
                        
                        $res[] = $row['id'];
                        
                        usleep($this->usleepDelay);///
            }
        }
        
        Yii::app()->helperCron->onEndCron($logId, serialize($res));
    }
    
    
    
    
    
    
    
    /*
     * NOT CRON
     */
    
    //  /home/pinkmeets/pinkmeets.com/public_html/protected/console email BounceProccess --email="EMAIL_BODY"
    //	NOT USING ON DEV
    //  /home/onl/domains/onl.lo/public_html/protected/console email BounceProccess --email="EMAIL_BODY"
    public function actionBounceProccess(/*array*/$email="")
    {
    	$logId = Yii::app()->helperCron->onStartCron('email','BounceProccess');
    	
    	//if ($email)
    		CHelperLog::logFile('cron_email_BounceProccess.log', $email);
    	
//$str = @explode("\n", $email);
//CHelperLog::logFile('cron_email_2_BounceProccess.log', $str);

    	$res = $email;//"";
    	
    	
    	//http://support.serverstack.com/index.php?/Tickets/Ticket/View/56766
    	//$pattern = "/to=\<([a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+)\>/";
		//preg_match($pattern, $email, $m);		
		//if (isset($m[1]))
        
		
		//check email
        $emailValidator = new CEmailValidator;
        $email = ($email) ? trim($email) : "";
        if ($emailValidator->validateValue($email))		
		{
			$userEmail = $email;//$m[1];
			$userId = Profile::emailExist($userEmail);
			
			if ($userId)
			{
				$profile = new Profile($userId);
				$profile->settingsUpdate('email_bounced', '1');
				$profile->settingsUpdate('hided_notify', '1');
				
				$sql = "UPDATE `pm_today_gold` SET email_bounced='1' WHERE user_id={$userId} LIMIT 1";
				Yii::app()->db->createCommand($sql)->execute();
				
				
				$sql = "INSERT INTO `log_mail_bounce` (`message`, `email`, `date`) VALUES (:message, :email, CURDATE())";
				Yii::app()->db->createCommand($sql)
					->bindValue(":message", "", PDO::PARAM_STR)//->bindValue(":message", $email, PDO::PARAM_STR)
					->bindValue(":email", $userEmail, PDO::PARAM_STR)
					->execute();				
			}
		}   	
    	
    	Yii::app()->helperCron->onEndCron($logId, $res);
    }
    
}
