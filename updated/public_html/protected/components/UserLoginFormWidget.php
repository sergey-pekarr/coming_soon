<?php
class UserLoginFormWidget extends CWidget
{
    public $type=1;

    public function init()
    {
        $model = new LoginForm;
        
        if ($this->type==1)     
            $this->render('userlogin', array('model'=>$model));
        else
			$this->render('userlogin2', array('model'=>$model));
    }
}
?>
