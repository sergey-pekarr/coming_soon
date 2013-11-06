<?php
class VideoSendFormWidget extends CWidget
{
    public $profileTo;
    
    public function init()
    {
        if (Yii::app()->user->id)
        {
            $model = new MessageVideoSendForm;
            $this->render('videoSendForm', array('model'=>$model, 'profileTo'=>$this->profileTo));
        }
    }
}
?>
