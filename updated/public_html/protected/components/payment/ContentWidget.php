<?php
class ContentWidget extends CWidget
{
    public $paymod;
	
	public function init()
    {
		if ($this->paymod == 'rg2')
        {
        	$model = new PaymentForm();
        	
        	$this->render('contentPaymentForm', array('model'=>$model, 'price'=>9.00, 'paymod'=>$this->paymod));
        }
        else
        {    	
	        $redirectUrl = SITE_URL.'/payment/redirection';
	        $this->render('content', array('redirectUrl'=>$redirectUrl));        
        }
    }
}
?>
