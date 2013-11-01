<?php
class PanelProfileLikeWidget extends CWidget
{
    public $all;
    
    public function init()
    {
        $this->render('PanelProfileLike', array('all'=>$this->all));
    }
}
?>
