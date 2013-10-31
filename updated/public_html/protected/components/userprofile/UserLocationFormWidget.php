<?php
class UserLocationFormWidget extends CWidget
{
    public function init()
    {
        $location = Yii::app()->user->Profile->getDataValue('location');

        $cityAndStateName = $location['city'];
        
        if ($location['stateName'])
        {
            $cityAndStateName .= ', '.$location['stateName'];
        }
        
        $model = new UserLocationForm;
                
        $this->render(
            'UserLocationForm', 
            array(
                'model'=>$model, 
                'profile'=>Yii::app()->user->Profile,
                'location'=>$location,
                'location_id' => Yii::app()->location->findNearestID( $location['latitude'], $location['longitude'] ),
                'cityAndStateName'=>$cityAndStateName,                
            )
        );
    }
}
?>