<?php

class MessageSendForm extends CFormModel
{
    public $id_to;
    public $subject;
    public $body;
    public $action;
    
    public $type;

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
            array('id_to', 'receiverCheck'),
            
            array('type', 'typesCheck'),
            //array('type', 'in', 'range'=>array('private','email'))
		);
	}
   
        
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
		);
	}
    
    public function receiverCheck()
    {
        
    }
    
    public function typesCheck()
    {
        if (!$this->type)
            $this->addError('type', 'Please select');
    }    
    
	public function doSend()
	{
		$idTo = Yii::app()->secur->decryptID($this->id_to);
       
        if(!$this->hasErrors() && $idTo)
        {
            //private message
            if ( in_array('private', $this->type) )
                Messages::addPrivateMessage($idTo, 0, $this->subject, $this->body);
             
            //send email
            if ( in_array('email', $this->type) )
                Yii::app()->mail->SendHtmlMail($idTo, '', 'messageAdmin', array('admin'=>array('messageSubject'=>$this->subject, 'messageBody'=>$this->body)));
            
            return true;
        }
        return false;
	}
    
    
        
}
