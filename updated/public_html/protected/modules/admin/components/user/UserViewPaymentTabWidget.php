<?php
class UserViewPaymentTabWidget extends CWidget
{
    public $id;
    
    public function init()
    {
        if (intval($this->id))
        {
            //$model = new UserEditMainTabForm($this->id);
            $profile = new Profile($this->id);
        	$dataPayment = $profile->getPayment();
        	
        	$sql = "SELECT * FROM `pm_transactions` WHERE user_id={$this->id} ORDER BY id DESC";
        	$trans = Yii::app()->db->createCommand($sql)->queryAll();
        	
        	$dataAdditional = "";
        	if (isset($dataPayment['paymod']))
        	{
	        	if ($dataPayment['paymod']=='wirecard' /*|| $dataPayment['paymod']=='wirecard_2'*/ || $dataPayment['paymod']=='wirecard_no3D')
	        	{
	        		$sql = "SELECT `GuWID` FROM `pm_wirecard_trn` WHERE user_id={$this->id} AND `type`='initial' AND status='approved' ORDER BY id DESC LIMIT 1";
		        	$dataAdditional = Yii::app()->db->createCommand($sql)->queryScalar();        		
	        		$dataAdditional = "GuWID: ".$dataAdditional;
	        	}

	        	if ($dataPayment['paymod']=='wirecard_2')
	        	{
	        		$sql = "SELECT `GuWID` FROM `pm_wirecard_trn` WHERE user_id={$this->id} AND `type`='initial' AND status='approved' ORDER BY id DESC LIMIT 1";
	        		$dataAdditional = "GuWID [initial, trial]: ".Yii::app()->db->createCommand($sql)->queryScalar();
	        		
	        		$sql = "SELECT `GuWID` FROM `pm_wirecard_trn` WHERE user_id={$this->id} AND `type`='repeated' AND status='approved' ORDER BY id ASC LIMIT 1";
	        		$dataAdditional.= " <br /> GuWID [first rebill]: ".Yii::app()->db->createCommand($sql)->queryScalar();	        		
	        	}
	        	
	        	
				if ($dataPayment['paymod']=='epg' || $dataPayment['paymod']=='epg_2')
	        	{
	        		$sql = "SELECT trn_id, TransactionId FROM `pm_epg_trn` WHERE user_id={$this->id} AND `step`='trial' AND ResultStatus='OK' ORDER BY id DESC LIMIT 1";
		        	$resTmp = Yii::app()->db->createCommand($sql)->queryRow();        		
	        		if ($resTmp)
	        		{
		        		$dataAdditional = "Your ReferenceNumber: ".$resTmp['trn_id'];
		        		$dataAdditional.= "<br />";
		        		$dataAdditional.= "EPG ReferenceTransactionID: ".$resTmp['TransactionId'];
	        		}
	        	}	        	
        	}
        	
        	
            $this->render( 'UserViewPaymentTab', array('profile'=>$profile, 'dataPayment'=>$dataPayment, 'trans'=>$trans, 'dataAdditional'=>$dataAdditional) );            
        }
    }
}
?>
