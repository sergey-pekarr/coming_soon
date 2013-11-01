<?php
class ReportBugFormWidget extends CWidget
{
    public function init()
    {
        $model = new ReportBugSendForm;
        
        if (FACEBOOK)
        {
            $this->render('reportBugFormFB', array('model'=>$model, 'profile'=>Yii::app()->user->id));
        }
    }
}
?>
