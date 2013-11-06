<?php
class UserApiOutInfoWidget extends CWidget
{
    public $userId;
    
    public function init()
    {
		if ($this->userId)
		{
	    	$profile = new Profile($this->userId);
	    	$apiOut = $profile->getApiOut();
	    	$this->render( 'userApiOutInfo', array('apiOut'=>$apiOut) );
		}
    }
}
?>
