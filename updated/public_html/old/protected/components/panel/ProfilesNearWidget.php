<?php
class ProfilesNearWidget extends CWidget
{
    public function init()
    {
        $model = new Profiles;
        $this->render('ProfilesNear', array('profiles'=>$model->Near(9)));
    }
}
?>
