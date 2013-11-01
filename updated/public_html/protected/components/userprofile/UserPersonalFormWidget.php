<?php
class UserPersonalFormWidget extends CWidget
{
    public function init()
    {
        $model = new UserPersonalForm;
        $this->render('UserPersonalForm', array('model'=>$model));
    }
}
?>