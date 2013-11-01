<?php
class UserImgUploadFormWidget extends CWidget
{
    public function init()
    {
        $model = new UserImgUploadForm;

        $this->render('UserImgUploadForm', array('model'=>$model));
    }
}
?>
