<?php


class UserImgUploadForm extends CFormModel
{
	public $image;

	public function rules()
	{
		return array(
            //array('image', 'required'),
            array('image', 'file', 'types'=>'jpg, jpeg, gif, png, tiff, bmp', 'maxSize' => 5242880),
		);
	}
        
}
