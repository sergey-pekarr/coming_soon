<?php
class AllBoxWidget extends CWidget
{
    public $m;
    
    public function init()
    {
        $profile = new Profile($this->m['id_to']);
        $this->render('allBox', array('m'=>$this->m, 'profile'=>$profile));
    }
}
?>
