<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class UserVideoUploadForm extends CFormModel
{
	public $Filedata;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            array('Filedata', 'required'),
            array('Filedata', 'file', 'types'=>VIDEO_FORMATS, 'maxSize' => 56000000),
		);
	}
        
}
