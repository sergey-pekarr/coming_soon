<?php
class UserSettingsFormWidget extends CWidget
{
    public function init()
    {
        $model = new UserSettingsForm;
        $this->render('UserSettingsForm', array('model'=>$model));            
    }
}
?>
