<?php
class SendFormWidget extends CWidget
{
    public $profile;
    public $bubble=false;
    public $reply = false;
    
    public function init()
    {
        if (Yii::app()->user->id)
        {
            $model = new MessageSendForm;
            if ($this->reply)
                $this->render('sendFormReply', array('model'=>$model, 'profile'=>$this->profile));
            elseif ($this->bubble)
                $this->render('sendFormBubble', array('model'=>$model, 'profile'=>$this->profile));
            else
                $this->render('sendForm', array('model'=>$model, 'profile'=>$this->profile));
        }
    }
}
?>
