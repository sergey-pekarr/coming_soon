<?php
class PanelDashboardMessagesWidget extends CWidget
{
    public function init()
    {
        if (Yii::app()->user->id)
        {
            $model = new Messages;
            
            $messages = $model->getPrivateMessagesTo(Yii::app()->user->id, true, 0, 3);
                   
            $this->render('PanelDashboardMessages', array('messages'=>$messages['messages'], 'newCount'=>$messages['newCount']));
        }
    }
}
?>
