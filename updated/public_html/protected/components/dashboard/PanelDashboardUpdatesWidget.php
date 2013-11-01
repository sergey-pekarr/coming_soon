<?php
class PanelDashboardUpdatesWidget extends CWidget
{
    public function init()
    {
        /*$model = new Profiles;
            
        $updates = $model->getUpdates();*/
		$updates = Updates::getUpdates();
               
        $this->render('PanelDashboardUpdates', array('updates'=>$updates));
    }
}
?>
