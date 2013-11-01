<?php
class SendFormWidget extends CWidget
{
    public $profile;
    
    public function init()
    {
        $model = new MessageSendForm;
        $this->render('sendForm', array('model'=>$model, 'profile'=>$this->profile));
    }
}
?>
