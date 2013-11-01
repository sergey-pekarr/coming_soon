<?php
class UserEmailInfoWidget extends CWidget
{
    public $userId;
    
    public function init()
    {
		if ($this->userId)
		{
	    	$profile = new Profile($this->userId);
	    	
	    	if ($profile->getDataValue('promo')=='0')
	    		$this->render( 'userEmailInfo', array('profile'=>$profile) );
		}
    }
}
?>
