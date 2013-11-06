<?php
class UserReportAbuseFormWidget extends CWidget
{
    public $id;

    public function init()
    {
        if ($this->id && Yii::app()->user->id)
        {
            $reported = ReportAbuse::getReportFromTo(Yii::app()->user->id, $this->id);
            
            if (!$reported)
            {
                $this->render( 'UserReportAbuseForm', array('id'=>$this->id) );                  
            }
          
        }
    }
}
?>
