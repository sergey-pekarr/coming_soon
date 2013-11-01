<?php
class PanelDashboardUpdatesBoxWidget extends CWidget
{
    public $update;
    
    public function init()
    {
        $profile = new Profile($this->update['id']);//user_id as id
        $this->render('PanelDashboardUpdatesBox', array('up'=>$this->update, 'profile'=>$profile));
    }
}
?>
