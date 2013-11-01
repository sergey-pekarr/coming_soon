<?php
class UserInfoShotWidget extends CWidget
{
    public $userId;
    
    public function init()
    {
		if ($this->userId)
		{
	    	$profile = new Profile($this->userId);
	    	$this->render( 'userInfoShot', array('profile'=>$profile) );
		}
		else 
			$this->render( 'userInfoShot' );
    }
}
?>
