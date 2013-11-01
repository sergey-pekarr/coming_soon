<?php
class GoogleAnalyticsWidget extends CWidget
{
    public function init()
    {
        if (LIVE)
    		$this->render('googleAnalytics');
    }
}
?>
