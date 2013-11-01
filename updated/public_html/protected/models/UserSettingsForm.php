<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class UserSettingsForm extends CFormModel
{
    public $passwordOld;
    public $passwordNew;
    public $emailNew;
    public $email_notifications;
    //public $email_on_profile_view;
    //public $email_3d;
    
    public function init()
    {
        $this->email_notifications = Yii::app()->user->settings('email_notifications');
    }
    
    
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('passwordOld', 'required'),
            array('passwordOld', 'passwordOldCheck'),
            array('passwordNew', 'length', 'min'=>6, 'max'=>12),
            array('emailNew', 'email'),
            array('emailNew', 'emailNewCheck'),
            array('email_notifications', 'in', 'range'=>array('0', 'Instant', 'Digest') ),
            //array('email_on_profile_view', 'email_on_profile_viewCheck'),
            //array('email_3d', 'email_3dCheck'),
		);
	}

    public function passwordOldCheck()
    {
        $sql = "SELECT password FROM users WHERE id=".Yii::app()->user->id;
        $passOld = Yii::app()->db->createCommand($sql)->queryScalar(); 
        if ( MD5(SALT.$this->passwordOld) != $passOld )
        {
            $this->addError('passwordOld','Bad current password');
        }        
    }
    public function emailNewCheck()
    {
        $id = Profile::emailExist($this->emailNew);
        if ( $id && $id!=Yii::app()->user->id )
        {
            $this->addError('emailNew',"Email already exists.");
        }
    }         
    /*public function email_notificationsCheck()
    {

    }    
    public function email_on_profile_viewCheck()
    {

    }    
    public function email_3dCheck()
    {

    }   */ 

    
	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'passwordOld'=>'Current password',
            'passwordNew'=>'New password',
            'email'=>'Email',
            'email_notifications'=>'Email notifications',
            'email_on_profile_view'=> 'Email profile views',
            'email_3d'=>'3rd party email'
		);
	}
    
    
    
    
    public function saveSettings()
    {
        if ($this->passwordNew)
            Yii::app()->user->Profile->Update('password', $this->passwordNew);
        if ($this->emailNew)
            Yii::app()->user->Profile->Update('email', $this->emailNew);
            
        Yii::app()->user->Profile->settingsUpdate('email_notifications', $this->email_notifications);
    }
        
}
