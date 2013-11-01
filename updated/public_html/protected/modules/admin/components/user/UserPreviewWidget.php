<?php
class UserPreviewWidget extends CWidget
{
    public $userId;
    public $n=0; //field `n` in database 
    public $size='small';
    
    public function init()
    {
		if ($this->userId)
		{
	    	$profile = new Profile($this->userId);
	    	
	    	$i = $profile->imgGetIndx($this->n);
	    	
	    	$this->render( 'userPreview', array('profile'=>$profile, 'i'=>$i) );
		}
		else 
			$this->render( 'userPreview' );
    }
}
?>
