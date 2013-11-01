<?php
class FingerprintWidget extends CWidget
{
    public function init()
    {
        if (PANAMUS_USE)
        {
	    	$panamus = new Panamus(Yii::app()->user->id);
	    	$id_session = $panamus->getId();
	    	
	    	$this->render('fingerprint', array('id_session'=>$id_session));
        }
    }
}
?>