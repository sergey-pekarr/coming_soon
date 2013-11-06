<?php
class UserRegistrationStep2FormWidget extends CWidget
{
    public function init()
    {
        $location_id = Yii::app()->location->findLocationIdByIP();        
        $location = Yii::app()->location->getLocation( $location_id );
        
        $model = new UserRegistrationStep2Form;
        
        $this->render('userregistrationstep2', array('model'=>$model, 'location_id'=>$location_id, 'location'=>$location));
    }
}
?>
