<?php
class Levelup
{
	//Simple cache to prevent re-calculate
	static private $_cache = array();
	
	private $profile;
	private $id;
	
	public $level;
	public $incomplete = array();
	public $percent;
	
	public $photo;
	public $personal;
	public $verified;
	public $desc;
	public $paid;
	public $messages;
	public $winks;
	public $favourite;
	public $like;	
	
	public function __construct($profile = null){
		if(!$profile){
			$this->id = Yii::app()->user->id;
			$this->profile = Yii::app()->user->Profile;
		}
		else {
			$this->profile = $profile;
			$this->id = $profile->getId();
		}		
	}
	
	private function saveCache(){
		self::$_cache[$this->id] = array(
			'level'=>$this->level, 
			'incomplete'=>$this->incomplete,
			'verified' => $this->verified,
			'desc' => $this->desc,
			'percent' => $this->percent,
			'photo' => $this->photo,
			'personal' => $this->personal,
			'paid' => $this->paid,
			'messages' => $this->messages,
			'winks' => $this->winks,
			'favourite' => $this->favourite,
			'like' => $this->like,
			);
	}
	
	private function checkPersonalOption(){
		$personal = $this->profile->getDataValue('personal');
		
		$exclude = array('default', 'user_id', 'badgets', 'status', 'firstName', 'lastName', 'profession', 'description');
		if(!$personal) return 0;
		$count = 0;
		foreach($personal as $key => $value){
			if(!in_array($key, $exclude)
				&& $value != null && $value != '' && $value !== 0 && $value != '0' && $value != ',' && $value != ',,'
			){
				$count++;
			}
		}
		return $count;
	}

	private function getPromosLevelup(){
		$qry = "select * from profile_levelup where user_id = {$this->id}";			
		$counts = Yii::app()->db
			->createCommand($qry)
			->queryRow();
		if(!$counts){
			$counts = array('messages' => 0,'winks' => 0, 'favourite' => 0, 'like' => 0);
		}
		return $counts;
	}
	
	/*
		//35
		Create: 10
		Verified: 10
		Paid: 10
		Word: 5
		
		//30
		personal: 2*5
		Photo: 5*4 = 20
		
		//35
		Messages: 1*10
		Winks: 1*5
		Favourite: 1*10
		Like: 1*10		
	*/
	private function calculatePercent(){
		$this->percent = 10; //After create account
		if($this->verified) {
			$this->percent += 10;
		}
		if($this->paid) {
			$this->percent += 10;
		}
		if(strlen($this->desc) >= 20) {
			$this->percent += 5;
		}
		
		$this->percent += min($this->personal,5)*2;
		$this->percent += min($this->photo,4)*5;
		
		$this->percent += min($this->messages,10);
		$this->percent += min($this->winks,5);
		$this->percent += min($this->favourite,10);
		$this->percent += min($this->like,10);
	}
	
	private function checkLevel1(){	
		$this->level = 1;		
		if($this->photo==0){
			$this->incomplete[] = '1.1';
		}
		if(strlen($this->desc) < 20) {
			$this->incomplete[] = '1.2';
		}
		if(!$this->verified) {
			$this->incomplete[] = '1.3';
		}
	}
	
	private function checkLevel2(){	
		$this->level = 2;
		
		if($this->photo<2) {
			$this->incomplete[] = '2.1';
		}
		if($this->personal<5) {
			$this->incomplete[] = '2.2';
		}
		if(!$this->paid) {
			$this->incomplete[] = '2.3';
		}
		if($this->messages<5) {
			$this->incomplete[] = '2.4';
		}
		if($this->winks<5) {
			$this->incomplete[] = '2.5';
		}
		if($this->favourite<5) {
			$this->incomplete[] = '2.6';
		}
		if($this->like<5) {
			$this->incomplete[] = '2.7';
		}
	}
	
	private function checkLevel3(){	
		$this->level = 3;
		
		if($this->photo<4) {
			$this->incomplete[] = '3.1';
		}
		if($this->messages<10) {
			$this->incomplete[] = '3.2';
		}
		if($this->favourite<10) {
			$this->incomplete[] = '3.3';
		}
		if($this->like<10) {
			$this->incomplete[] = '3.4';
		}
	}
	
	public function checkLevel(){
		if(isset(self::$_cache[$this->id])){
			return self::$_cache[$this->id];
		}
		
		$this->photo = $this->profile->getDataValue('pics');
		$this->desc = $this->profile->getPersonalValue('character');
		$this->verified = $this->profile->getSettingsValue('email_activated_at') != '0000-00-00 00:00:00';
		
		$this->checkLevel1();
		
		if(count($this->incomplete)>0){
			$this->calculatePercent();
			$this->saveCache();
			return self::$_cache[$this->id];
		}
		
		$this->personal = $this->checkPersonalOption();
		$this->paid = ($this->profile->getDataValue('role') == 'gold');
		
		//For fake users, should 
		if($this->profile->getDataValue('promo') == '1'){
			$counts = $this->getPromosLevelup();
		}
		else {
			$act = Activity::createActivity($this->id);
			$counts = $act->getLevelUpCounts();			
		}
		
		$this->messages = $counts['messages'];	
		$this->winks = $counts['winks'];
		$this->favourite = $counts['favourite'];
		$this->like = $counts['like'];
		
		$this->checkLevel2();
		
		if(count($this->incomplete)>0){
			$this->calculatePercent();
			$this->saveCache();
			return self::$_cache[$this->id];
		}
		
		$this->checkLevel3();
		
		if(count($this->incomplete)>0){
			$this->calculatePercent();
			$this->saveCache();
			return self::$_cache[$this->id];
		}
		
		$this->level = 4;
		//$this->calculatePercent();
		$this->percent = 100;
		$this->saveCache();
		return self::$_cache[$this->id];			
	}
}
