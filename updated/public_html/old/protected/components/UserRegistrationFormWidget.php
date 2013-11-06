<?php
class UserRegistrationFormWidget extends CWidget
{
    public $home;
        
    public function init()
    {
        $model = new UserRegistrationForm;
        
        $this->render('userregistration', array('model'=>$model));
    }
}
?>
