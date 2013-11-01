<?php
class FacebookLoginWidget extends CWidget
{
    
    public function init()
    {
        ///$facebook = Yii::app()->facebook;
        $this->render('FacebookLogin'/*, array('facebook'=>$facebook)*/);
    }
}
?>
