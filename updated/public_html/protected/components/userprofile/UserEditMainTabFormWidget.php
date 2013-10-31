<?php
class UserEditMainTabFormWidget extends CWidget
{
    public function init()
    {
        if (Yii::app()->user->id)
        {
            $model = new UserEditMainTabForm;
            //$profile = new Profile(Yii::app()->user->id);
            $this->render( 'UserEditMainTabForm', array('model'=>$model, 'profile'=>Yii::app()->user->Profile) );            
        }
    }
}
?>
