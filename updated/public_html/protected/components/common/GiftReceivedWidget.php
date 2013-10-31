<?php
class GiftReceivedWidget extends CWidget
{
	public $profile;
	
	public function init()
	{		
	}
	
	public function run(){
		$id = $this->profile->getId();
		//if($this->profile->getDataValue('promo') == '1'){
		//	$qry = "select gifts profile_gift_promos where user_id = $id";
		//	$gifts = Yii::app()->db
		//		->createCommand($qry)
		//		->queryRow();
		//	if(!$gifts){
		//		$items = array();
		//	}
		//	else {
		//		$items = array();
		//		$giftarr = explode(',', $gifts);
		//		foreach($giftarr as $item){
		//			if($item != null && trim($item) != ''){
		//				$items[] = $item;
		//			}
		//		}
		//	}			
		//}
		//else {
		//	$act = Activity::createActivity($id);		
		//	$items = $act->getGifts();
		//}
		//if(!$items) $items = array();
		
		//Fake user might have both real and fake gift		
		$act = Activity::createActivity($id);		
		$items = $act->getGifts($this->profile->getDataValue('promo') == '1'?$this->profile:null);
		
		if($items){
			$this->render('gifts', array('items' => $items));
		}
		else if($this->profile->getId() == Yii::app()->user->id){
			$this->render('gifts', array('items' => array()));
		}
	}
}
?>