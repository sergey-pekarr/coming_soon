<?php
class UserViewLocationTabWidget extends CWidget
{
    public $id;
    
    public function init()
    {
        $profile = new Profile($this->id);
        $location  = $profile->getDataValue('location');
    	
    	if ($location)
        {
            
        	$model = new AdminUserLocationForm();
        	$location_id = 0;//Yii::app()->location->findNearestID( $location['latitude'], $location['longitude'] );
        	
            $this->render(
            	'UserViewLocationTab', 
            	array(
            		'model'=>$model, 
            		'location'=>$location, 
            		'location_id'=>$location_id
            	) 
            );            
        }
    }
}
?>
