<?php
class UserInfoRefShowWidget extends CWidget
{
    public $userId;
    
    public function init()
    {
		if ($this->userId)
		{
	    	$profile = new Profile($this->userId);
	    	$this->render( 'userInfoRefShow', array('profile'=>$profile) );
		}
		else 
			$this->render( 'userInfoRefShow' );
    }
}
?>
