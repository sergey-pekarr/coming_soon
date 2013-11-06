<?php
class UserEditAppearanceTabFormWidget extends CWidget
{
    public $id;
    
    public function init()
    {
        if (intval($this->id))
        {
            $model = new UserEditAppearanceTabForm;
            $model->id = $this->id;
            $model->init();            
            
            $profile = new Profile($this->id);
            $this->render( 'UserEditAppearanceTabForm', array('model'=>$model, 'profile'=>$profile) );            
        }
    }
}
?>
