<?php
class HelperReversals 
{
	public static function getMods()
    {
		return array(
			//enum in database => array(title, bgcolor, reason for user_payment, show in color hints)
		
			"chargeback"					=> array("Zombaio Chargeback", 				'#e69999', 'refunded', 	true),
			"doubleSale_FraudRefunded"		=> array("Double Sale, Fraud Refunded",		'#e1c7e1', 'refunded', 	true),
			"women_RefundedAndCcanceled"	=> array("Women Refunded And Canceled", 	'#b6d7a8', 'refunded', 	true),
			"zombaioRefund"					=> array("Zombaio Refund", 					'#ffff99', 'refunded', 	true),
		
			"rg_Refund"						=> array("rg Refund", 						'#CEF6F5', 'refunded', 	true),
			"rg_Chargeback"					=> array("rg Chargeback", 					'#A9F5F2', 'chargeback',true),
			
			"rg_cams_Refund"				=> array("rg Cams Refund", 					'#CEF6F5', 'refunded', 	true),
			"rg_cams_Chargeback"			=> array("rg Cams Chargeback", 				'#A9F5F2', 'chargeback',true),

			"rg2_Refund"					=> array("rg2 Refund", 						'#CEF6F5', 'refunded', 	true),
			"rg2_Chargeback"				=> array("rg2 Chargeback", 					'#A9F5F2', 'chargeback',true),		
		
			"segpay_refund"					=> array("Segpay Refund", 					'#62979d', 'refunded', 	true),
			"segpay_chargeback"				=> array("Segpay Chargeback", 				'#177082', 'chargeback',true),			
		);
    }   	
	
    public static function getModsKeys()
    {
    	$keys = array();
    	foreach (self::getMods() as $k=>$v)
    	{
    		$keys[] = $k;
    	}
    	return $keys;
    }
    
    public static function getModsForSelect($text='All')
    {
    	$res = array(''=>$text);
    	foreach (self::getMods() as $k=>$v)
    	{
    		$res[$k] = $v['0'];
    	}
    	return $res;
    }
    
    
	public static function getReversals($post, $page=0)
    {
   	
        switch  ($post['sort'])
        {
            case 'idASC':    $order = "r.id ASC"; break;
            case 'idDESC':   $order = "r.id DESC"; break;
            case 'affid':   $order = "r.aff_id ASC"; break;
            
            default:   $order = $post['sort']; break;
        }
        
	    $where[] = "r.date_real>='{$post['date1']}'";
	    $where[] = "r.date_real<='{$post['date2']}'";
	    if ($post['mod'])
	    	$where[] = "r.type='{$post['mod']}'";

		if ($post['form'])
			$where[] = "u.form='{$post['form']}'";
				
		$where[] = "r.user_id=u.id";
		
	    $where = implode(" AND ", $where); 
	    
		$sql = "SELECT COUNT(r.id) FROM `".DB_NAME_STATS."`.`reversals` as r, `".DB_NAME."`.`users` as u WHERE {$where}";
	    $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();
	        
	    $sql = "SELECT r.*, u.username, u.form FROM `".DB_NAME_STATS."`.`reversals` as r, `".DB_NAME."`.`users` as u WHERE {$where} ";
	    $sql.= " ORDER BY {$order}";
      
        $sql.= " LIMIT ".($page * $post['perPage']).", " . $post['perPage'];

        $res['list'] = Yii::app()->db->createCommand($sql)->queryAll();
		
		return $res;
	}
    
}