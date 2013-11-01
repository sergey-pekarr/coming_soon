<?php

class CHelperPayment
{
	static function getPaymods()
    {
    	return array('zombaio', 'zombaio2', 'rg', 'segpay', 'rg_cams', 'rg2');
    }
  
    static public function getPaymodsForSelect()
    {
    	$res = array(
    		''=>'All',
    	);
    	
    	$ps = self::getPaymods();
    	asort($ps);
    	foreach ($ps as $p)
    		$res[$p]=$p;
   	
    	return $res;
    }    
    
    public static function getAllin1BackUpBiller($form='93')
    {
    	switch($form)
    	{
    		case '93': return ZSIGNUP_BACKUP_BILLER;
    		case '932': return ZSIGNUP2_BACKUP_BILLER;
    	}
    	
    	throw new CHttpException(404, 'Wrong paymode.');
    }
    
    /**
     * next billing for zsignup
     */
    public static function getAllinNextBilling($form='93')
    {
		//if (DEBUG_IP && $form=='932') return 'wirecard_2';
		//if (DEBUG_IP && $form=='93')  return "rg";//'segpay';
    	
    	$paymod = '';
	    
    	$sql = "SELECT * FROM pm_billing_control WHERE `form`='{$form}' ORDER BY `order`";
	    $paymods = Yii::app()->db->createCommand($sql)->queryAll();
	    
	    if (!$paymods)
	    {
			return self::getAllin1BackUpBiller($form);
	    }
	    
		foreach ($paymods as $p)
	    	$sales_done[$p['paymod']] = 0;    
	        
	    $sql = "SELECT paymod FROM `user_payment` WHERE firstpay='".date("Y-m-d")."' AND form='{$form}'";
	    $dbres = Yii::app()->db->createCommand($sql)->queryAll();
	      
	    $sales_done = array();
	    if ($dbres)
	    	foreach ($dbres as $v)
	        	if (isset($sales_done[$v['paymod']]))
	            	$sales_done[$v['paymod']]++;
	            else
	            	$sales_done[$v['paymod']]=1;
	                    

		$paymod=$paymods[0]['paymod'];
	        
	    foreach ($paymods as $p)
	    {
	        if (!isset($sales_done[$p['paymod']]))
	        	$sales_done[$p['paymod']] = 0;	
	    	
			$sales_done[$p['paymod']] -= $p['sales'];
			 
	        if (
	        		$p['sales']==0 //all remaining that day
	                || 
	                $sales_done[$p['paymod']] < 0
				)
			{
	        	$paymod = $p['paymod'];
	            break;
			}
		}
	    
	    if (!$paymod) $paymod=self::getAllin1BackUpBiller($form);
	    
	    return $paymod;
    }
    
    
    
    
    
    /*
     * return gateway (not paymod) for user
     * rg
     */
    static public function getCamsWay($userId)
    {
    	return CAMS_BILLER_WAY;
    }
    static public function getCamsPaymod($userId=0)
    {
    	return new Payment_rg_cams();
    }    
    
    
    
    public static function hideCard($ccnum, $type=0)
    {
    	if (!$ccnum) return '';
    	
    	switch($type)
    	{
    		case 1: //	4111********1111
    				$res = substr($ccnum,0,4) . str_repeat('*', (strlen($ccnum) - 8)) . substr($ccnum,-4,4);
    				break;
    		case 2: //	411111******1111
    				$res = substr($ccnum,0,6) . str_repeat('*', (strlen($ccnum) - 10)) . substr($ccnum,-4,4);
    				break;
    		default://	************1111
    				$res = str_repeat('*', (strlen($ccnum) - 4)) . substr($ccnum,-4,4); 
    				break;
    	}
    	return $res;
    }
    
    
    
    
	public static function getOptionsAll()
	{
		$mkey = "Payment_getOptionsAll";
		$res = Yii::app()->cache->get($mkey);
		
		if ($res===false)
		{
			$sql = "SELECT * FROM pm_pricing";
			$res = Yii::app()->db->createCommand($sql)->queryAll();
			
			Yii::app()->cache->set($mkey, $res, 300);
		}
		
		return $res;
	}
    
	
	public static function getOptionRow($priceId)
	{
		$priceId = intval($priceId);
		
		if (!$priceId)
			return false;
		
		$options = self::getOptionsAll();//$this->getOptions();
		if ($options)
			foreach($options as $o)
				if ($o['id']==$priceId)
					return $o;

		return false;
	}   

	
	/*
	 * detect visa/mastercard
	 * 
	 * http://stackoverflow.com/questions/72768/how-do-you-detect-credit-card-type-based-on-number
	 */
	public static function detectCardType($cn)
	{
		if (preg_match("/^4[0-9]{12}(?:[0-9]{3})?$/", $cn))
			return 'visa';
		elseif (preg_match("/^5[1-5][0-9]{14}$/", $cn))
			return 'mastercard';
		elseif (preg_match("/^3[47][0-9]{13}$/", $cn))
			return 'american express';
		elseif (preg_match("/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/", $cn))
			return 'diners club';			
		elseif (preg_match("/^6(?:011|5[0-9]{2})[0-9]{12}$/", $cn))
			return 'discover';			
		elseif (preg_match("/^(?:2131|1800|35\d{3})\d{11}$/", $cn))
			return 'jcb';			
		else
			return '';//other
	}
	
	public static function getCCYears()
	{
        for ($y=date("Y"); $y<=(date("Y")+10); $y++ )
        	$ccyears[$y] = $y;
        return $ccyears;
	}
	public static function getCCMonths()
	{
		for ($m=1; $m<=12; $m++ )
			$ccmonths[sprintf("%02d", $m)] = sprintf("%02d", $m);
        return $ccmonths;
	}	
	
	
	
	
	
	
	
	
	
	
	
    public static function getModelPayment($paymod)
    {
    	switch($paymod) 
    	{
    		case 'zombaio': 
    			$modelPayment = new Payment_zombaio( 0 );
    			break;    		
    		case 'zombaio2': 
    			$modelPayment = new Payment_zombaio2( 0 );
    			break;
    		case 'rg': 
    			$modelPayment = new Payment_rg( 0 );
    			break;
    		case 'rg_cams': 
    			$modelPayment = new Payment_rg_cams( 0 );
    			break;
    		case 'rg2': 
    			$modelPayment = new Payment_rg_cams( 0 );
    			break;
    		case 'segpay': 
    			$modelPayment = new Payment_segpay( 0 );
    			break;
    			
    		default:     			
				return false;
    	};

    	return $modelPayment;
    }	
}
