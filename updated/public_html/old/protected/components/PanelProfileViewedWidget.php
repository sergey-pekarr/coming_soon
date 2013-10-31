<?php
class PanelProfileViewedWidget extends CWidget
{
    public $all;
    
    public function init()
    {
        $this->render('PanelProfileViewed', array('all'=>$this->all));
    }
}
?>
