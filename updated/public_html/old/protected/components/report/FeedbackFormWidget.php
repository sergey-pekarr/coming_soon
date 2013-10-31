<?php
class FeedbackFormWidget extends CWidget
{
    public function init()
    {
        $model = new FeedbackSendForm;
        
        $template = (!FACEBOOK) ? 'feedbackForm' : 'feedbackFormFB';
        
        $this->render($template, array('model'=>$model, 'profile'=>Yii::app()->user->id));
    }
}
?>
