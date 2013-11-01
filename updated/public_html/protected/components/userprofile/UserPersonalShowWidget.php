<?php
class UserPersonalShowWidget extends CWidget
{
    public $profile;
    
    public function init()
    {
        $this->render('UserPersonalShow', array('profile'=>$this->profile));
    }
}
?>