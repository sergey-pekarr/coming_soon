<?php
class UserEditMainTabFormWidget extends CWidget
{
    public $id;
    
    public function init()
    {
        if (intval($this->id))
        {
            $model = new UserEditMainTabForm($this->id);
            $this->render( 'UserEditMainTabForm', array('model'=>$model) );            
        }
    }
}
?>
