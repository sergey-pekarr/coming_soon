<?php

//Paymentapi, not PaymentApi

class PaymentapiController extends Controller
{
	/*
	 * ZOMBAIO POSTBACK
	 * http://pinkmeets.com/paymentapi/zombaio
	 */
	/*public function actionZombaio()
	{
		header("Content-Type: text/plain");	
		
		if (!isset($_GET) || !$_GET)
		{
			echo "";
			Yii::app()->end();		
		}

		
		//form 93 or 932
		$username = (isset($_GET['username'])) ? trim($_GET['username']) : "";
		$userId = Profile::usernameExist($username);
		$profile = new Profile($userId);
		
		if ( $profile->getDataValue('form')=='932' )
			$modelPayment = new Payment_zombaio2();
		else
			$modelPayment = new Payment_zombaio();
		
		$res = $modelPayment->postback($_GET);
		
		echo $res;
		
		Yii::app()->end();	
	}*/

	
	

	/*
	 * ZOMBAIO POSTBACK
	 * http://pinkmeets.com/paymentapi/zombaio
	 */
	public function actionZombaio()
	{
		header("Content-Type: text/plain");	
		
		if (!isset($_GET) || !$_GET)
		{
			echo "";
			Yii::app()->end();		
		}

		
		//form 93 or 932
		$subID = (isset($_GET['SUBSCRIPTION_ID'])) ? trim($_GET['SUBSCRIPTION_ID']) : "";
		
		$userId=0;
		if ($subID) // rebills
		{
			$sql = "SELECT user_id FROM pm_zombaio_trn WHERE `sub_id`={$subID} AND `action`='user.add' LIMIT 1";
			$userId = Yii::app()->db->createCommand($sql)->queryScalar();		
		}
		else//user.add
		{
			$username = (isset($_GET['username'])) ? trim($_GET['username']) : "";
			
			if ($username)
			{
				$userId = Profile::usernameExist($username);
			}
		}
		
		
		
//CHelperSite::vd($userId);		
		if ($userId)
		{
			$profile = new Profile($userId);
//CHelperSite::vd($profile->getDataValue('form'));			
			if ( $profile->getDataValue('form')=='932' )
				$modelPayment = new Payment_zombaio2();
			else
				$modelPayment = new Payment_zombaio();		
		}
		else 
			$modelPayment = new Payment_zombaio();
		
		$res = $modelPayment->postback($_GET);
		
		echo $res;
		
		Yii::app()->end();	
	}	
	
	
	
	/*
	 * RocketGate
	 * redirection after success first payment
	 */
	public function actionRg_approved()
	{
		//RG approved (postback):
		if (isset($_REQUEST['flag']) && isset($_REQUEST['hash']))
		{
			$modelPayment = new Payment_rg();
			$success = $modelPayment->success();
			$url = ($success) ? '/payment/approved' : '/payment/declined';
			if (!Yii::app()->user->id)
				$this->redirect('/site/login');
			else
				$this->redirect($url);
		}	
	}
	
	
	/*
	 * RocketGate POSTBACK
	 * http://pinkmeets.com/paymentapi/rg
	 */
	public function actionRg()
	{
		header('Content-type: application/xml');	

/*if (LOCAL)
{
	$xml='<?xml version="1.0" encoding="UTF-8"?><RecurringBilling><authNo>199963</authNo><merchantAccount>1</merchantAccount><approvedAmount>39.0</approvedAmount><customerID>47</customerID><cardLastFour>1881</cardLastFour><transactionDate>2013-06-12</transactionDate><version>1.1</version><settledCurrency>USD</settledCurrency><transactID>100013F3954736F</transactID><transactionType>PURCHASE</transactionType><requestedAmount>39.0</requestedAmount><requestedCurrency>USD</requestedCurrency><settledAmount>39.0</settledAmount><cardHash>3RPN+a+d07r0bOlq7NcWPKvzgcyyHmPxXw+hCxxmP6k=</cardHash><invoiceID>47</invoiceID><paymentType>CREDIT</paymentType><merchantID>1363309678</merchantID><cardType>VISA</cardType><transactionTimestamp>2013-06-12 13:00:03</transactionTimestamp><approvedCurrency>USD</approvedCurrency><billingType>C</billingType></RecurringBilling>';
}	
else*/
{	
	$xml = file_get_contents('php://input');
	
	$log = date("r") . " remoteIP: " .$_SERVER['REMOTE_ADDR']."\n";//.$_SERVER['REQUEST_URI']."\n";
	CHelperLog::logFile('pm_rg_postback_xml.log', $log . var_export($xml, true));
}		
		$res=false;
		// Payment_rg OR Payment_rg_cams
		$resRG = HelperXML::xml2ary($xml);
		if (isset($resRG["RecurringBilling"])) 
		{
			//initial rg trn id
			$trn_id = (isset($resRG["RecurringBilling"]["_c"]["customerID"]['_v'])) ? intval($resRG["RecurringBilling"]["_c"]["customerID"]['_v']) : 0;
			if ($trn_id) 
			{
				$iTRN = Payment::getTransactionInfo($trn_id);
				
				if (isset($iTRN['paymod']))
				{
					switch ($iTRN['paymod'])
					{
						//case 'rg':
						default: 
							$modelPayment = new Payment_rg();
							$res = $modelPayment->Rebill($xml);								
							break;

						case 'rg_cams': 
							$modelPayment = new Payment_rg_cams();
							$res = $modelPayment->Rebill($xml);								
							break;							

						case 'rg2': 
							$modelPayment = new Payment_rg2();
							$res = $modelPayment->Rebill($xml);								
							break;							
							
					}
				}
			}
			

		}
			

		

/*$answer = '<?xml version="1.0" encoding="UTF-8"?>
<Response>
<results>result-code</results>
[<message>result-message</message>]
</Response>';	*/	

$resCode="0";//always return success ($res) ? "0" : "1";
$answer = '<?xml version="1.0" encoding="UTF-8"?>
<Response>
<results>'.$resCode.'</results>
</Response>';	

		echo $answer;		
	}	
	
	
/*
	public function actionRgTestRebill()
	{

		
if (LIVE)
$xml = '<?xml version="1.0" encoding="UTF-8"?><RecurringBilling><authNo>139450</authNo><merchantAccount>1</merchantAccount><approvedAmount>39.0</approvedAmount><customerID>11168</customerID><cardLastFour>1881</cardLastFour><transactionDate>2013-05-17</transactionDate><version>1.1</version><username>a031801</username><settledCurrency>USD</settledCurrency><transactID>100013EB3A6151D</transactID><transactionType>PURCHASE</transactionType><requestedAmount>39.0</requestedAmount><requestedCurrency>USD</requestedCurrency><settledAmount>39.0</settledAmount><merchantProductID>1</merchantProductID><cardHash>3RPN+a+d07r0bOlq7NcWPKvzgcyyHmPxXw+hCxxmP6k=</cardHash><invoiceID>11168</invoiceID><paymentType>CREDIT</paymentType><merchantID>1363309678</merchantID><cardType>VISA</cardType><transactionTimestamp>2013-05-17 14:00:06</transactionTimestamp><approvedCurrency>USD</approvedCurrency><billingType>R</billingType></RecurringBilling>';//id 11168 
else
$xml = '<?xml version="1.0" encoding="UTF-8"?><RecurringBilling><authNo>771713</authNo><merchantAccount>1</merchantAccount><approvedAmount>39.0</approvedAmount><customerID>18</customerID><cardLastFour>1111</cardLastFour><transactionDate>2013-05-17</transactionDate><version>1.1</version><username>a0318</username><settledCurrency>USD</settledCurrency><transactID>100013EB3A615CF</transactID><transactionType>PURCHASE</transactionType><requestedAmount>39.0</requestedAmount><requestedCurrency>USD</requestedCurrency><settledAmount>39.0</settledAmount><merchantProductID>1</merchantProductID><cardHash>m77xlHZiPKVsF9p1/VdzTb+CUwaGBDpuSRxtcb7+j24=</cardHash><invoiceID>18</invoiceID><paymentType>CREDIT</paymentType><merchantID>1363309678</merchantID><cardType>VISA</cardType><transactionTimestamp>2013-05-17 14:00:06</transactionTimestamp><approvedCurrency>USD</approvedCurrency><billingType>R</billingType></RecurringBilling>';//id 18

		$modelPayment = new Payment_rg();
		$res = $modelPayment->Rebill($xml);

		header('Content-type: application/xml');

$resCode="0";//always return success ($res) ? "0" : "1";
$answer = '<?xml version="1.0" encoding="UTF-8"?>
<Response>
<results>'.$resCode.'</results>
</Response>';	

		echo $answer;		
	}	
*/
	
	
	
	/*
	 * SEGPAY POSTBACK
	 * http://pinkmeets.com/paymentapi/segpay?action=<action>&stage=<stage>&approved=<approved>&trantype=<trantype>&purchaseid=<purchaseid>&tranid=<tranid>&price=<price>&currencycode=<currencycode>&eticketid=<eticketid>&ip=<ipaddress>&initialvalue=<ival>&initialperiod=<iint>&recurringvalue=<rval>&recurringperiod=<rint>&desc=<desc>&username=<extra username>&password=<extra password>&name=<billname>&firstname=<billnamefirst>&lastname=<billnamelast>&email=<billemail>&phone=<billphone>&address=<billaddr>&city=<billcity>&state=<billstate>&zipcode=<billzip>&country=<billcntry>&merchantpartnerid=<extra merchantpartnerid>&transGUID=<transguid>&standin=<standin>&xsellnum=<xsellnum>&billertranstime=<transtime>&REF1=<extra ref1>&REF2=<extra ref2>
	 */
	public function actionSegpay()
	{
		header("Content-Type: text/plain");	
		
		if (DEBUG_IP)
			$p = $_REQUEST;//for testing using $_GET...
		else
			$p = $_POST;

		if (!$p)
		{
			echo "";
			Yii::app()->end();	
		}
		
		$modelPayment = new Payment_segpay();
		
		$res = $modelPayment->postback($p);
		
		echo $res;
		
		
	}	
}