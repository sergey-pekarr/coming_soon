<?php
class UserEditBadgetsWidget extends CWidget
{
    public function init()
    {
        if (Yii::app()->user->id)
        {
            $model = new UserEditBadgets;
            $this->render( 'UserEditBadgets', array('model'=>$model, 'profile'=>Yii::app()->user->Profile) );            
        }
    }
}
?>
