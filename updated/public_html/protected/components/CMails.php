<?php

class CMails extends YiiMail
{
    public $view='indexHtml';
    public $from;


	/**
	* Calls the {@link registerScripts()} method.
	*/
	public function init() 
    {
        $this->from = array(Yii::app()->params['adminEmail'] => "PinkMeets"/*Yii::app()->name*/);
        
		parent::init();	
	}
    
	public function saveToDB($to, $from='', $template, $html, $data)
	{
		/*if (!is_array($to)) $to = array($to);
		$to = serialize($to);*/
		
		//n: 2012 Oct 12
		$subject = $data['subject'];
		$body = $data['body'];
		
		unset($data['subject']);
		unset($data['body']);
		
		$dataSerialise = serialize($data);
		
		$user_id = (isset($data['user_id'])) ? $data['user_id'] : 0;
		
		$to = trim($to);//2012-11-01
		
		$sql = "INSERT INTO log_mail (`user_id`, `to`, `from`, `template`, `html`, `data`, `subject`, `body`, `added`) VALUES (:user_id, :to, :from, :template, :html, :data, :subject, :body, NOW())";
		Yii::app()->db->createCommand($sql)
			->bindValue(":user_id", $user_id, PDO::PARAM_INT)	
			->bindValue(":to", $to, PDO::PARAM_STR)
			->bindValue(":from", $from, PDO::PARAM_STR)
			->bindValue(":template", $template, PDO::PARAM_STR)
			->bindValue(":html", $html, PDO::PARAM_STR)
			->bindValue(":data", $dataSerialise, PDO::PARAM_STR)
			->bindValue(":subject", $subject, PDO::PARAM_STR)
			->bindValue(":body", $body, PDO::PARAM_STR)
			->execute();
	}
    
	public function updateDB($id, $sent)
	{
		$sql = "UPDATE LOW_PRIORITY log_mail SET sent=:sent WHERE id=:id LIMIT 1";
		Yii::app()->db->createCommand($sql)
			->bindValue(":id", $id, PDO::PARAM_INT)
			->bindValue(":sent", $sent, PDO::PARAM_STR)
			->execute();  
	}
	
	/*
	 * return true if unsubscribed or bounced email
	 */
    public function skipSend($userId, $template)
    {
    	$userId = intval($userId);
    	if (!$userId)
    		return true;//unsubscribed as error...
    	
    	if ($template=='verifyemail' || $template=='welcome' || $template=='passwordReset')
    		return false;
    		
    	$profile = new Profile($userId);
    	
    	if ($profile->getSettingsValue('hided_notify')=='1')
    		return true;
    		
    	if ($profile->getSettingsValue('email_bounced')=='1')
    		return true;
    	
    	return false;
    }
    
    public function SendHtmlMail($row)
    {
    	if (!$row['to']) return;
        
    	/*if (!is_array($row['to']))
    		$row['to'] = unserialize($row['to']);*/
    	
        if (!$row['from'])
            $row['from'] = $this->from;
        
        if(isset(Yii::app()->controller))
            $controller = Yii::app()->controller;
        else
            $controller = new CController('YiiMail');

        $message = new YiiMailMessage;
        $message->view = $this->view;
        
        //To, From
        //$message->setTo($row['to']);//if array	???
        $message->addTo($row['to']);//if string	???
        $message->from = $row['from'];
        
        //Body Html
        $message->setBody(array('row'=>$row), 'text/html', 'utf-8');
        
        //Subject
        $message->subject = $row['subject'];
        
        //header
        $message->ReplyTo = $row['from'];
        
        $sent = ($this->send($message)) ? '1' : '0';

      	$this->updateDB($row['id'], $sent);
    }
    
    
	public function SendText($row)
    {
    	if (!$row['to']) return;
        
    	if (!is_array($row['to']))
    		$row['to'] = unserialize($row['to']);
    	
        if (!$row['from'])
            $row['from'] = $this->from;
        
        if(isset(Yii::app()->controller))
            $controller = Yii::app()->controller;
        else
            $controller = new CController('YiiMail');

        $message = new YiiMailMessage;
        //$message->view = $this->view;
        
        //To, From
        $message->setTo($row['to']);//if array	???
        //$message->addTo($to);//if string	???
        $message->from = $row['from'];        
        
        //Body Html
        $message->setBody($row['body'], null, 'utf-8');
        
        //Subject
        $message->subject = $row['subject'];
        
        //header
        $message->ReplyTo = $row['from'];
        
        $sent = ($this->send($message)) ? '1' : '0';

      	$this->updateDB($row['id'], $sent);
    }    
    
    
    
    
    
    
    private function _setAddData($template, $data)
    {
        if (isset($data['user_id']) && $data['user_id'])
        	$profile = new Profile($data['user_id']);
    	
    	switch ($template)
        {
            case 'welcome': 
                $data['subject'] = 'Activate Your Account';
                //$data['autoLoginUrl'] = CHelperProfile::getAutoLoginUrl($data['user_id']);
                $data['confirmEmailUrl'] = CHelperProfile::getConfirmEmailUrl( $data['user_id'], $profile->getDataValue('email') );
                $data['username'] = $profile->getDataValue('username');
                $data['password'] = Yii::app()->secur->decryptByLikeNC($profile->getDataValue('passwd'));
                break;
				
			case 'verifyemail':
				$data['subject'] = 'Verify email';
				$data['confirmEmailUrl'] = CHelperProfile::getConfirmEmailUrl( $data['user_id'], $profile->getDataValue('email') );
				$data['username'] = $profile->getDataValue('username');
				break;
            case 'passwordReset': 
                $data['subject'] = 'Password Reset Notification';
                $data['passwordResetUrl'] = CHelperProfile::getAutoLoginUrl($data['user_id']).'?redirect='. urlencode( '/account' );
                $data['username'] = $profile->getDataValue('username');
                break;
                
            case 'photorequest':
            	$data['subject'] = 'Photo Request';
            	
            	$profileFrom = new Profile($data['from_user_id']);
            	
            	$data['from_profileUrl'] = CHelperProfile::getAutoLoginUrl($data['user_id']).'?redirect='. urlencode( $profileFrom->getUrlProfile() );
            	$data['from_imgUrl'] = $profileFrom->imgUrl('medium');
            	$data['from_username'] = $profileFrom->getDataValue('username');
            	$data['from_age'] = CHelperProfile::getAge($profileFrom->getDataValue('birthday'));
            	$data['from_gender_text'] = CHelperProfile::textGender( $profileFrom->getDataValue('gender') );
            	$data['from_looking_for_gender_text'] = CHelperProfile::textLookGender( $profileFrom->getDataValue('looking_for_gender') );
            	$data['from_location'] = CHelperProfile::showProfileInfoSimple($profileFrom, 3, 20);
            	$data['from_minage'] = $profileFrom->getDataValue('settings', 'ageMin');
            	$data['from_maxage'] = $profileFrom->getDataValue('settings', 'ageMax'); 
            	
            	$profile = new Profile($data['user_id']);
            	
            	$data['username'] = $profile->getDataValue('username');
            	$data['photoUploadUrl'] = CHelperProfile::getAutoLoginUrl($data['user_id']).'?redirect='. urlencode( '/account' );
            	
            	break;
			case 'email':
				
				$profileFrom = new Profile($data['from_user_id']);
				$data['subject'] = $profileFrom->getDataValue('username').' just sent you a message';
				
				$data['from_profileUrl'] = CHelperProfile::getAutoLoginUrl($data['user_id']).'?redirect='. urlencode( $profileFrom->getUrlProfile() );
				$data['from_imgUrl'] = $profileFrom->imgUrl('medium');
				$data['from_username'] = $profileFrom->getDataValue('username');
				$data['from_age'] = CHelperProfile::getAge($profileFrom->getDataValue('birthday'));
				$data['from_gender_text'] = CHelperProfile::textGender( $profileFrom->getDataValue('gender') );
				$data['from_looking_for_gender_text'] = CHelperProfile::textLookGender( $profileFrom->getDataValue('looking_for_gender') );
				$data['from_location'] = CHelperProfile::showProfileInfoSimple($profileFrom, 3, 20);
				$data['from_minage'] = $profileFrom->getDataValue('settings', 'ageMin');
				$data['from_maxage'] = $profileFrom->getDataValue('settings', 'ageMax'); 
				
				$profile = new Profile($data['user_id']);
				if(!isset($data['messages_Url'])){
				}
				
				$data['username'] = $profile->getDataValue('username');
				break;
			case "autophotorequest":
			case "autoemail":
				break;
        }
        
        
        $data['autoLoginUrl'] = (isset($data['user_id'])) ? CHelperProfile::getAutoLoginUrl($data['user_id']) : SITE_URL;
        $data['unsubscribeUrl'] = (isset($data['user_id'])) ? CHelperProfile::getAutoLoginUrl($data['user_id']).'?redirect=/account' : SITE_URL.'/account';
        
//FB::info($data, 'EMAIL TEMPLATE DATA');

        return $data;
    }
    
    
	
	
    public function prepareMailHtml($to, $from='', $template, $data=array())
    {
//FB::info($data);        
        if (!$to && isset($data['user_id']))
        {
        	$profile = new Profile($data['user_id']);
        	$to = $profile->getDataValue('email');
        };
        
        if (!$to) return;

        
		if ( isset($data['user_id']) && $this->skipSend($data['user_id'], $template))
    		return;
    	
        
        
        //if (!$from) $from = $this->from;
        
        $data = $this->_setAddData($template, $data);

        if(isset(Yii::app()->controller))
            $controller = Yii::app()->controller;
        else
            $controller = new CController('YiiMail');        
        
        //Body Html
        $viewPath = Yii::getPathOfAlias($this->viewPath.'.html.'.$template).'.php';        
        $data['body'] = $controller->renderInternal($viewPath, array('data'=>$data), true);
        
        //Subject
        $viewPath = Yii::getPathOfAlias($this->viewPath.'.subject.'.$template).'.php';
        $data['subject'] = $controller->renderInternal($viewPath, array('data'=>$data), true);
        
        $this->saveToDB($to, $from, $template, '1', $data);
        
        return true;
    }    
    
    /**
     * store TEXT email into DB
     */
    public function prepareMailText($to, $from='', $template, $data=array())
    {
        if (!$to) return;

    	if ( isset($data['user_id']) && $this->skipSend($data['user_id'], $template))
    		return;        
        
        /*if (!$from)
            $from = $this->from;*/

        $this->saveToDB($to, $from, $template, '0', $data);
        
        return true;
    }    
    
    
	public function generateInsertMail($template, $data){
		
		if (!isset($data['to']) || !isset($data['to'])){
			return '';
		}

		if(isset(Yii::app()->controller)){
			$controller = Yii::app()->controller;
		}
		else{
			$controller = new CController('YiiMail');        
		}
		
		//Body Html
		$viewPath = Yii::getPathOfAlias($this->viewPath.'.html.'.$template).'.php';        
		$data['body'] = $controller->renderInternal($viewPath, array('data'=>$data), true);
		
		//Subject
		$viewPath = Yii::getPathOfAlias($this->viewPath.'.subject.'.$template).'.php';
		$data['subject'] = $controller->renderInternal($viewPath, array('data'=>$data), true);
		
		return '';
	}
    
    
    
    
    
    /*
    public function SendMail($to, $from='', $data=array())
    {
//FB::info($data);        
        if (!$to) return;
          
        if (!$from)
            $from = $this->from;
        
        if(isset(Yii::app()->controller))
            $controller = Yii::app()->controller;
        else
            $controller = new CController('YiiMail');

        $message = new YiiMailMessage;
        $message->view = $this->view;
        
        //To, From
        $message->addTo($to);
        $message->from = $from;        
        
        $data = $this->_setAddData($template, $data);
        
        //Body Html
        $viewPath = Yii::getPathOfAlias($this->viewPath.'.html.'.$template).'.php';        
        $data['body'] = $controller->renderInternal($viewPath, array('data'=>$data), true);
        $message->setBody(array('data'=>$data), 'text/html', 'utf-8');
        
        //Subject
        $viewPath = Yii::getPathOfAlias($this->viewPath.'.subject.'.$template).'.php';
        $message->subject = $controller->renderInternal($viewPath, array('data'=>$data), true);
        
        //header
        $message->ReplyTo = $from;
        
        //$message->from
        //echo $message->body;//
        return $this->send($message);
    }    
    
    /*public function SendText($to, $from='', $data=array())
    {
        if (!$to) return;
          
        if (!$from)
            $from = $this->from;
        
        if(isset(Yii::app()->controller))
            $controller = Yii::app()->controller;
        else
            $controller = new CController('YiiMail');

        $message = new YiiMailMessage;
        //$message->view = $this->view;
        
        //To, From
        $message->setTo($to);//if array
        //$message->addTo($to);//if string
        $message->from = $from;        
        
        //Body Html
        $message->setBody($data['body'], null, 'utf-8');
        
        //Subject
        $message->subject = $data['subject'];
        
        //header
        $message->ReplyTo = $from;
        
        //$message->from
        //echo $message->body;//
        return $this->send($message);
    }  */    
    
    
    
}
