<?php
/**
  * Encode / decode text
  */

class CMails extends YiiMail
{
    public $view='indexHtml';
    public $from;


	/**
	* Calls the {@link registerScripts()} method.
	*/
	public function init() 
    {
        $this->from = array(Yii::app()->params['adminEmail'] => Yii::app()->name);
        
		parent::init();	
	}
    
    
    public function SendHtmlMail($toUserId, $from='', $template, $data=array())
    {
        if (!$toUserId)
            return;
        
        $profile = new Profile($toUserId);
        $data['profile'] = $profile->getData(); 
        
        if ($data['profile']['promo'])
            return;
        
        if ( !$to = $data['profile']['email'] )
            return;

          
        if (!$from)
        {
            $from = $this->from;
        }        
        
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
        $message->setBody(array('data'=>$data) /*array('body'=>$body, 'subject2'=>$subject2)*/, 'text/html', 'utf-8');
        
        //Subject
        $viewPath = Yii::getPathOfAlias($this->viewPath.'.subject.'.$template).'.php';
        $message->subject = $controller->renderInternal($viewPath, array('data'=>$data), true);
        
        //header
        $message->ReplyTo = $from;
        
        //$message->from
        //echo $message->body;//
        $this->send($message);
    }
    
    private function _setAddData($template, $data)
    {
        switch ($template)
        {
            case 'welcome': 
                $data['subject2'] = 'Email Activation'; 
                break;
            case 'videoRejected': 
                $data['subject2'] = 'Please submit again';
                $data['subject2Color'] = '#215ace';
                $data['reason'] = str_replace("\n", "<br />", $data['reason']);
                break;
            case 'videoApproved': 
                $data['subject2'] = 'Your video approved'; 
                $data['subject2Color'] = 'green';
                break;
            case 'messageNew':
                $profileFrom = new Profile($data['id_from']); 
                $data['subject2'] = 'Private message received from '. CHelperProfile::truncName( $profileFrom->getDataValue('username'), 10);
                $data['profileFrom'] = $profileFrom;
                //$data['subject2Color'] = 'black';
                break;
            case 'messageAdmin':
                $data['subject2'] = 'Message received from Administrator';
                break;
                
        }
        
        $data['autoLoginUrl'] = Yii::app()->helperProfile->getAutoLoginUrl($data['profile']['id']);
             
//FB::error($data);
        return $data;
    }
}
