<?php

class EmailForm extends CFormModel
{	
	public $email;	
	
	public function init()
	{
		parent::init();
	}
	
	public function rules()
	{
		return array(
			array('email', 'required'),
			array('email', 'email'),
			array('email', 'emailCheck'),
			);
	}
	
	public function emailCheck()
	{
		if (!$this->hasErrors() && Profile::emailExist($this->email))
		{
			$this->addError('email',"Email already exists.");
		}
	}	
}
