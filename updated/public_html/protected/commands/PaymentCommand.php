<?php
class PaymentCommand extends CConsoleCommand
{
	private $timeStart; 
	private $timeMax=55;
	private $usleepDelay;
	
    public function init() 
    { 
        if (SITE_UNDER_UPDATE) 
            Yii::app()->end();
        
		$this->usleepDelay = (LIVE) ? 50 : 1000;
            
        $this->timeStart = time();
    }

	
    //  */1 * * * * root php /home/onl/domains/onl.lo/public_html/protected/console payment TodayGold
    //	*/1 * * * * /usr/bin/php /home/dating/pinkmeets.com/public_html/protected/console.php payment TodayGold
    public function actionTodayGold()
    {
        $logId = Yii::app()->helperCron->onStartCron('payment','todayGold');
        
        $res = PaymentCron::todayGoldPrepare();        
        
        Yii::app()->helperCron->onEndCron($logId, $res);
		
		
		//Each item in pm_today_gold will have one relative item in pm_today_risk. 
		//So I think can be called after TodayGold. It will not harm TodayGold because TodayGold has completed, and logged
		//Delay 20s to avoid overload at starttime
		if ( (time()-$this->timeStart)<($this->timeMax/2) )
		{
			//return true;
			sleep(1);
					
			$logId = Yii::app()->helperCron->onStartCron('risk','DetectRisk');
			$res = RiskDetection::detectRisk();
			Yii::app()->helperCron->onEndCron($logId, $res);
			
	
			sleep(1);
			$logId = Yii::app()->helperCron->onStartCron('risk','ReportRisk');
			RiskDetection::checkAndSendMail();
			Yii::app()->helperCron->onEndCron($logId, $res);
		}
    }

    
    //	3,18,33,48 * * * * /usr/bin/php /home/dating/pinkmeets.com/public_html/protected/console.php payment SalesRG_cams
    public function actionSalesRG_cams()
    {

//Yii::app()->end();//STOPPED RG (skype 2013-05-15)    	
    	if (!LIVE) return;
    	 
    	$logId = Yii::app()->helperCron->onStartCron('payment','SalesRG_cams');
    
    	$modelPayment = new Payment_rg_cams();
    	$res = $modelPayment->CamSales();
    
    	//CHelperLog::logFile('pm_rg_cams_sales.log', $res);
    
    	Yii::app()->helperCron->onEndCron($logId, $res);
    }
}
