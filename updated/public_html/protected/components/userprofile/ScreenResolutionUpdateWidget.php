<?php
class ScreenResolutionUpdateWidget extends CWidget
{
    public function init()
    {
        if (Yii::app()->user->id)
        {
            $srnupd = Yii::app()->user->Profile->getDataValue('info', 'screen_resolution');
            
            if (!$srnupd)
            	$this->render('screenResolutionUpdate');            
        }
    }
}
?>
