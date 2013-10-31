<?php
class UserPrivateMessagesBoxWidget extends CWidget
{
    public $message;
    
    public function init()
    {
        $profile = new Profile($this->message['id_from']);
        $this->render('UserPrivateMessagesBox', array('m'=>$this->message, 'profile'=>$profile));
    }
}
?>
