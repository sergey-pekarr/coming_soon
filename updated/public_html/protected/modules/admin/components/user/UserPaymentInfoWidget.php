<?php
class UserPaymentInfoWidget extends CWidget
{
    public $userId;
    
    public function init()
    {
		if ($this->userId)
		{
	    	$profile = new Profile($this->userId);
	    	$this->render( 'userPaymentInfo', array('profile'=>$profile) );
		}
		else 
			$this->render( 'userPaymentInfo' );
    }
}
?>
