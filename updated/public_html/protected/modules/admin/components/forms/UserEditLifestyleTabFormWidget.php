<?php
class UserEditLifestyleTabFormWidget extends CWidget
{
    public $id;
    
    public function init()
    {
        if (intval($this->id))
        {
            $model = new UserEditLifestyleTabForm;
            $model->id = $this->id;
            $model->init();
            
            $profile = new Profile($this->id);
            $this->render( 'UserEditLifestyleTabForm', array('model'=>$model, 'profile'=>$profile) );            
        }
    }
}
?>
