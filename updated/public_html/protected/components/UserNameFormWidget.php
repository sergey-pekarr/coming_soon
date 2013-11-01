<?php
class UserNameFormWidget extends CWidget
{
    public function init()
    {
        $model = new UserNameForm;
                
        $this->render('UserNameForm', array('model'=>$model));
    }
}
?>