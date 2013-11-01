<?php
//
//class RemindPasswordForm extends CFormModel
//{
//	public $f_email;
//		
//	public function rules()
//	{
//		return array(
//			// username and password are required
//			array('f_email', 'required'),///array('email, password, agree', 'required'),
//			array('f_email', 'email'),///array('email, email2', 'email'),
//			array('f_email', 'emailCheck'),
//			);
//	}
//	
//	public function emailCheck()
//	{
//		if (!Profile::emailExist($this->f_email))
//		{
//			$this->addError('f_email',"Email does not exists.");
//		}
//	}			
//	
//}