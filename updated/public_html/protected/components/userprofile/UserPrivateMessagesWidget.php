<?php
class UserPrivateMessagesWidget extends CWidget
{
    public $profile;
    
    public function init()
    {
        if (Yii::app()->user->id)
        {
            $model = new Messages;
            
            $messages = $model->getPrivateMessages($this->profile->getDataValue('id'), Yii::app()->user->id);
                   
            $this->render('UserPrivateMessages', array('messages'=>$messages, 'profile'=>$this->profile));
        }
    }
}
?>
