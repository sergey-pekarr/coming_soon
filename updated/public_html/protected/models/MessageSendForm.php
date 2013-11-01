<?php

class MessageSendForm extends CFormModel
{
    public $id_to;
    public $subject;
    public $body;
    public $action;

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
            array('id_to', 'receiverCheck')
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
    
	public function doSend()
	{
		$idTo = Yii::app()->secur->decryptID($this->id_to);
        $id_from = Yii::app()->user->id;        
        
        if(!$this->hasErrors() && $id_from && $idTo)
        {
            $id = Messages::addPrivateMessage($idTo, $id_from, $this->subject, $this->body);
                
            if ($id)
            {
                //send email
                Yii::app()->mail->SendHtmlMail($idTo, '', 'messageNew', array('id_from'=>$id_from));
            }
            
            return true;
        }
        return false;
	}
    
    
        
}
