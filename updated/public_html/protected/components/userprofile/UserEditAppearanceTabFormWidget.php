<?php
class UserEditAppearanceTabFormWidget extends CWidget
{
    public function init()
    {
        if (Yii::app()->user->id)
        {
            $model = new UserEditAppearanceTabForm;
            $this->render( 'UserEditAppearanceTabForm', array('model'=>$model, 'profile'=>Yii::app()->user->Profile) );            
        }
    }
}
?>
