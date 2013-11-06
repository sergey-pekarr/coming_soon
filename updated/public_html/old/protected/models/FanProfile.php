<?php

define('FAN_SIGN_DIR_IMG',  dirname(__FILE__).'/../../../media/fan_sign');

class FanProfile
{	
	static private $_instances = array();
	
	static public function createFanProfile($id = 0){		
		if(!$id) $id = Yii::app()->user->id;
		if(isset(self::$_instances[$id])){
			return self::$_instances[$id];
		}
		else {
			self::$_instances[$id] = new FanProfile($id);
			return self::$_instances[$id];
		}
	}
	
	private $id;
	private $profile;
	
	private $cachedata;
	private $mkey;
	private $cache;
	//private $hasCached = true;
	
	private function __construct($id = 0){
		
		if(!$id)
		{
			$id = Yii::app()->user->id;
			$this->id = $id;
			
			$this->profile = Yii::app()->user->Profile;
		}
		else {
			$this->id = $id;			
		}
		
		$this->mkey = 'fanprofile_'.$this->id;
		
		//Live time: as long as cache profile
		$this->cache = Yii::app()->params->cache['profile'];
		
		$this->_cacheGet();	
	}
	
	private function _cacheGet(){
		
		//if ($this->id){
		//	$this->cachedata = Yii::app()->cache->get( $this->mkey );		
		//}
		//$this->hasCached = ($this->cachedata != null);
		if(!$this->cachedata) {
			$this->cachedata = array();
		}
		return $this->cachedata;
	}
	
	private function _cacheUpdate()	{
		if ($this->id) {
			Yii::app()->cache->set($this->mkey, $this->cachedata, $this->cache);
		}
	}
	
	private function _cacheDelete()
	{
		if ($this->id )	{
			Yii::app()->cache->delete($this->mkey);
		}
	}
	
	private function checkProfile(){
		if($this->profile == null){
			$this->profile = new Profile($this->id);
		}
	}
	
	private function checkSettings(){
		if(!isset($this->cachedata['settings'])){
			
			$qry = "select * from users_fan_settings where user_id = {$this->id}";
			$db = Yii::app()->db;
			
			$this->cachedata['settings'] = $db->createCommand($qry)->queryRow();
			if(!$this->cachedata['settings']){
				$this->cachedata['settings'] = array();
			}
			$this->_cacheUpdate();
		}
	}
	
	private function getSetttings($key, $default = null){
		$this->checkSettings();
		
		if(isset($this->cachedata['settings'][$key])){
			return $this->cachedata['settings'][$key];
		}
		return $default;
	}
	
	public function getStatus(){
		
		return $this->getSetttings('status', 'initial');
	}
	
	public function getPaymentMethod(){
		
		return $this->getSetttings('payment_method');
	}
	
	public function getPaymentInfor(){
		
		$infor = $this->getSetttings('payment_infor');
		if($infor != null){
			try{
				$infor = unserialize($infor);
				return $infor;
			}
			catch(exception $ex){
				$infor == null;
			}
		}
		return null;
	}
	
	public function getNote(){
		
		return $this->getSetttings('note');
	}
	
	public function getSign(){
		
		$img = $this->getSetttings('fan_sign');
		
		if($img){
			return "/img/fansign/$img";
		}
		else {
			return '/images/img/blank.gif';
		}
	}
	
	private function updateSettings($key, $value){
		
		if(in_array($key, array('status', 'payment_method', 'note', 'payment_infor' ))){
			$param = PDO::PARAM_STR;
		}
		else{
			return;
		}
		
		if($this->getSetttings($key) == $value) return;
		
		$qry = "update users_fan_settings set $key = :$key where user_id = {$this->id}";
		$db = Yii::app()->db;
		
		$ok = $db->createCommand($qry)
			->bindValue(":$key", $value, $param)
			->execute();
		
		unset($this->cachedata['settings']);
	}
	
	public function setStatus($status){
		
		$this->updateSettings('status', $status);
	}
	
	public function setPaymentMethod($method, $infor){
		
		$this->updateSettings('payment_method', $method);
		$this->updateSettings('payment_infor', serialize($infor));
	}
	
	public function setReadNote(){
		
		$this->updateSettings('note', null);
	}
	
	public function updateSign($img){
		
		$now = date('Y-m-d H:i:s'); //Should not use now() because web server and database server might be different in clock
		
		$qry = "insert into users_fan_settings (user_id, status, joined, fan_sign)
				values({$this->id}, 'pending', '$now' , :img)
				on duplicate key update `status` = 'pending', fan_sign = :img";
		$db = Yii::app()->db;
		
		$ok = $db->createCommand($qry)
			->bindValue(':img', $img, PDO::PARAM_STR)
			->execute();
		
		//n Oct 21: remove auto message
		$qry = "delete from users_fakeplan where user_id = {$this->id} and `type` = 'messages'";
		$ok = $db->createCommand($qry)
			->execute();
		
		unset($this->cachedata['settings']);		
	}
	
	public function getNextPayouts(){
		$db = Yii::app()->db;
		$qry = "select 
					sum(case when pay_status = 'paid' then amt else 0 end) as paid,
					sum(case when pay_status = 'pending' then amt else 0 end) as pending,
					sum(case when pay_status = 'going' then amt else 0 end) as going				
				from users_fan_payout where user_id = {$this->id}";
		$paid = $db->createCommand($qry)->queryRow();
		
		$currentTime = time();
		$weekday = date('N', $currentTime) -1;
		$thisweek = date('Y-m-d', $currentTime + (7 - $weekday) * 24*3600);
		
		$result = array(
			'Total paid amount' => ($paid && $paid['paid'])?$paid['paid']:0,
			'Going to be paid(*)' => ($paid && $paid['pending'])?$paid['pending']:0,
			'Next payout amount' => ($paid && $paid['going'])?$paid['going']:0,
			'Next payout date' => $thisweek
			);
		if($paid && $paid['pending']){
			unset($result['Going to be paid(*)']);
		}
		
		return $result;
	}
	
	private function normalizeStatistic(&$arr){
		if(!isset($arr['valid_count'])) $arr['valid_count'] = 0;
		if(!isset($arr['pending_count'])) $arr['pending_count'] = 0;
		if(!isset($arr['invalid_count'])) $arr['invalid_count'] = 0;
		if(!isset($arr['amt'])) $arr['amt'] = 0;
	}
	
	private function statisticBeforeToday($currentTime){
		$db = Yii::app()->db;
		
		$today = date('Y-m-d 00:00:00', $currentTime);	
		$yesterday = date('Y-m-d 00:00:00', $currentTime - 24*3600);	
		$weekday = date('N', $currentTime) -1;
		$thisweek = date('Y-m-d 00:00:00', $currentTime - $weekday * 24*3600);
		$thismonth = date('Y-m-1 00:00:00', $currentTime);
		
		if(isset($this->cachedata['beforetoday']) && $this->cachedata['beforetoday']['date'] == $today){
			return $this->cachedata['beforetoday'];
		}
		
		$weekQry = "select sum(case when m.valid = 'valid' then 1 else 0 end) as valid_count,
						sum(case when m.valid = 'justadded' then 1 else 0 end) as pending_count,
						sum(case when m.valid = 'invalid' then 1 else 0 end) as invalid_count,
						sum(m.amt) as amt    
					from users_fan_messages as m
					where m.id_to = {$this->id} and '$thisweek' <= m.added and m.added < '$today'";
		$weekData = $db->createCommand($weekQry)->queryRow();
		if(!$weekData) $weekData == array('valid_count' => 0, 'pending_count' => 0, 'invalid_count' => 0, 'amt' => 0);
		
		$this->normalizeStatistic($weekData);
		
		$monthQry = "select sum(case when m.valid = 'valid' then 1 else 0 end) as valid_count,
						sum(case when m.valid = 'justadded' then 1 else 0 end) as pending_count,
						sum(case when m.valid = 'invalid' then 1 else 0 end) as invalid_count,
						sum(m.amt) as amt    
					from users_fan_messages as m
					where m.id_to = {$this->id} and '$thismonth' <= m.added and m.added < '$today'";
		$monthData = $db->createCommand($monthQry)->queryRow();
		if(!$monthData) $monthData == array('valid_count' => 0, 'pending_count' => 0, 'invalid_count' => 0, 'amt' => 0);
		
		$this->normalizeStatistic($monthData);
		
		$this->cachedata['beforetoday'] = array('date'=>$today, 'week' => $weekData, 'month' => $monthData);
		$this->_cacheUpdate();
		return $this->cachedata['beforetoday'];
	}
	
	private $statistic = null;
	
	public function statistic(){
		
		$db = Yii::app()->db;
		
		$currentTime = time();
		$today = date('Y-m-d 00:00:00', $currentTime);	
		$enddate = date('Y-m-d 23:59:59', $currentTime);		
		$weekday = date('N', $currentTime) -1;		
		$thisweek = date('Y-m-d 00:00:00', $currentTime - $weekday * 24*3600);
		
		if($this->statistic && $this->statistic['date'] == $today) return $this->statistic;
		
		$before = $this->statisticBeforeToday($currentTime);
		$befWeek = $before['week'];
		$befMonth = $before['month'];
		
		$todayQry = "select sum(case when m.valid = 'valid' then 1 else 0 end) as valid_count,
						sum(case when m.valid = 'justadded' then 1 else 0 end) as pending_count,
						sum(case when m.valid = 'invalid' then 1 else 0 end) as invalid_count,
						sum(m.amt) as amt    
					from users_fan_messages as m
					where m.id_to = {$this->id} and '$today' <= m.added and m.added <= '$enddate'";
		$todayData = $db->createCommand($todayQry)->queryRow();
		
		$this->normalizeStatistic($todayData);
		
		if(!$todayData) $todayData == array('valid_count' => 0, 'pending_count' => 0, 'invalid_count' => 0, 'amt' => 0);
		
		$this->statistic=array('date'=>$today,
			'week' => array('valid_count' => $befWeek['valid_count'] + $todayData['valid_count'], 
					'pending_count' => $befWeek['pending_count'] + $todayData['pending_count'], 
					'invalid_count' => $befWeek['invalid_count'] + $todayData['invalid_count'], 
					'amt' => $befWeek['amt'] + $todayData['amt'] ),
				'month' => array('valid_count' => $befMonth['valid_count'] + $todayData['valid_count'], 
					'pending_count' => $befMonth['pending_count'] + $todayData['pending_count'], 
					'invalid_count' => $befMonth['invalid_count'] + $todayData['invalid_count'], 
					'amt' => $befMonth['amt'] + $todayData['amt'] ),
				'today' => $todayData,
				'todayCount' => $todayData['valid_count'],
				'weekCount' => $befWeek['valid_count'] + $todayData['valid_count'],
				'monthCount' => $befMonth['valid_count'] + $todayData['valid_count']);
		
		return $this->statistic;
	}
	
	
	static public function getImgPath($userId){
		$subdir1 = $userId + 999999 - (($userId-1)%1000000);
		$subdir2 = $userId + 999 - (($userId-1)%1000);
		//return FAN_SIGN_DIR_IMG.'/'.$subdir1.'/'.$subdir2.'/'.$userId;
		return FAN_SIGN_DIR_IMG.'/'.$subdir1.'/'.$subdir2;
	}
	
	static public function getImg($id)
	{
		if (!$userId = Yii::app()->secur->decryptID($id)){
			return false;
		}
		$file = self::getImgPath($userId);
		
		if(!file_exists($file)){
			$file = dirname(__FILE__).'/../../../images/img/blank.gif';
		}
		return array('imgPath'=>$file, 'imgInfo'=>getimagesize($file), 'filemtime'=>filemtime($file));
	}
}