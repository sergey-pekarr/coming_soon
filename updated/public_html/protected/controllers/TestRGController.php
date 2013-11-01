<?php

class TestRGController extends Controller
{

	public function init()
    {
		if (!DEBUG_IP)
		    Yii::app()->end();    	
    	
        parent::init();
        $this->layout='//layouts/guest1';
    }
    
    
    
    public function actionTest6()
	{
		$user_id = (LOCAL_OK) ? 594837 : 602192;
		
		$paymentModel = new Payment_rg();
		$url = $paymentModel->startTransaction_TEST6($user_id, 100);
		echo $url;//$this->redirect($url);
    }
}