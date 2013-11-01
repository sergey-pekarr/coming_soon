<?php

class ReportBugSendForm extends CFormModel
{
    public $subject;
    public $body;
    public $action;
    public $url;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('subject, body', 'required'),
            array('subject', 'length', 'min'=>2, 'max'=>25),
            array('body', 'length', 'min'=>10, 'max'=>1000),
            array('url', 'checkUrl'),
		);
	}
    
    public function init()
    {
        parent::init();
        $this->url = SITE_URL.$_SERVER['REQUEST_URI'];
    }
        
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
		);
	}
    
	public function checkUrl()
	{
		
	}    
    
    
	public function doSend()
	{
        $id_from = Yii::app()->user->id;        
        
        if(!$this->hasErrors())
        {
            //$id = Messages::addPrivateMessage($idTo, $id_from, $this->subject, $this->body);
                
            //if ($id)
            {
                //send email
                //Yii::app()->mail->SendHtmlMail($idTo, '', 'messageNew', array('id_from'=>$id_from));
                $body = $this->subject. "\n";
                $body.= $this->body. "\n\n\n";
                $body.= 'url: '.$this->url. "\n\n\n";
                
                if (Yii::app()->user->id)
                    $body.= 'user ID: ' . Yii::app()->user->id . "\n\n\n";
                else
                    $body.= 'user: Guest'. "\n\n\n";
                
                mail('onl@tst.pp.ua', 'New Report Bug', $body);
            }
            
            return true;
        }
        return false;
	}
    
    
        
}
