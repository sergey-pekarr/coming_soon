<?php

/**
 * This is the model class for table "help".
 *
 * The followings are the available columns in table 'help':
 * @property integer $id
 * @property string $created
 * @property string $email
 * @property string $text
 */
class HelpForm extends CFormModel
{

    public $email;
    public $message;
    public $verifyCode;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, message', 'required'),
			array('email', 'email'),
            array('message', 'length', 'min'=>10, 'max'=>1000),
			// verifyCode needs to be entered correctly
			array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'verifyCode'=>'Verification Code',
		);
	}

    public function save()
    {
        $command = Yii::app()->db->createCommand();
        $command->insert('help', array(
            'email'=>$this->email,
            'message'=>$this->message,
            'created'=>new CDbExpression('NOW()'),
        ));
        return Yii::app()->db->getLastInsertID();
    }
    
}