<?php

/*
+ Improve search: cache adjacent items, itemCount, realItemCount (when user request the same search condition)
	- Every time we need to search in database, we will request 200 items which include the requeted range. Store them in cache
	- When use request next or back pages, we will prefer to lookup from cached range. Return if requested range is suitable
	- itemCount, realItemCount are query just one time
	- Real users will appear as first items
		- Dont use 'order by promo' because it cause slow process
		- Use two separate query (for real user and promo user), by store realItemCount -> program can select which one need to run
Later:
	- then list gold user
	- list online user first
*/
class SearchForm
{
	public $data;
	public $count;
	public $perpage;
	public $totalpage;
	public $page;
	
	private $profile;
	private $form;
	public $radius;
	public $username;
	private $gender_search;
	public $age;
	public $maxage;
	private $height;
	private $maxheight;
	private $income;
	private $maxincome;
	private $interests;
	private $looking_for;
	public $has_photo;	
	private $language;
	private $options;
	
	public function __construct($post, $perpage = 16, $profile = null){
		$this->form = $post;
		$this->perpage = $perpage;
		if(!$this->form){
			//$this->form = $_POST;
			$this->form = $_GET;
		}
		$this->profile = !$profile? (new Profile(Yii::app()->user->id)):$profile;
		$this->options = array();
		
		$this->initCondition();
	}
	
	private function getValue($arr, $name, $default = ''){
		if(gettype($arr) == 'object') $arr = (array)$arr;
		if(isset($arr[$name])) return $arr[$name];
		else return $default;
	}
	
	private function parseInput($arr){
		$this->radius = $this->getValue($arr,'radius',50);
		
		//false mean: we dont care this condition => will not be included in search query
		
		$this->username = addslashes($this->getValue($arr, 'username', false));
		
		$this->gender_search = $this->getValue($arr, 'gender_search', $this->profile->getDataValue('looking_for_gender'));
		if(in_array($this->gender_search,array('MF','MFC'))) $this->gender_search = false;
		
		$this->age = $this->getValue($arr, 'age','18',  $this->profile->getSettingsValue('ageMin'));
		if(in_array($this->age,array('','18'))) $this->age = false;
		
		$this->maxage = $this->getValue($arr, 'maxage','99',  $this->profile->getSettingsValue('ageMax'));
		if(in_array($this->maxage,array('','99'))) $this->maxage = false;
		
		$this->height = $this->getValue($arr, 'height','1');
		if(in_array($this->height,array('','1'))) $this->height = false;
		
		$this->maxheight = $this->getValue($arr, 'maxheight','27');
		if(in_array($this->maxheight,array('','27'))) $this->maxheight = false;
		
		$this->income = $this->getValue($arr, 'income', '1');
		if(in_array($this->income,array('', '0','1'))) $this->income = false;
		
		$this->maxincome = $this->getValue($arr, 'maxincome', '7');
		if(in_array($this->maxincome,array('', '7'))) $this->maxincome = false;
		
		$this->interests = $this->getValue($arr, 'interests', false);
		$this->looking_for = $this->getValue($arr, 'looking_for', false);
		
		$this->has_photo = $this->getValue($arr, 'has_photo', false);
		
		$this->language = $this->getValue($arr, 'language', false);
		
		$fields = array("body_type", "drinking", "eye_color", "hair_color",
			"personality", "relationship_status", "smoking", "anal", "experience", "live", 
			"kinky", "occupation", "oral", "religion", "appearance", "ethnicity", 
			"hair_length", "best_feature", "piercings_tattoos", "nationality", "style",);
		
		foreach($fields as $key){
			$v = $this->getValue($arr, $key, false);
			if($v && $v != '0' && $v != ''){
				$this->options[$key] = $v;
			}
		}
	}
	
	private function initCondition(){
		$this->page = $this->getValue($_GET,'page',1);
		
		//$this->parseInput($_POST);
		$this->parseInput($this->form);
		
		$groupcondition = $this->getValue($this->form, 'searchcondition', null);
		if($groupcondition){
			try{
				$group = json_decode($groupcondition);
				$this->parseInput($group);
			} catch(exception $ex){
			}
		}
		
	}
	
	private function internalBuildQuery(&$qry = '', &$qryCount = '', &$joinPersonal = '', &$personal = '', &$where = ''){
		$qry = "select u.* 
				from users as u
				inner join users_location as l on l.user_id = u.id and u.role <> 'deleted'";
		
		$qryCount = "select count(*)
				from users as u
				inner join users_location as l on l.user_id = u.id and u.role <> 'deleted'";
		
		//Note: At this time, promo users have no personal information => u.promo = 0 cause omit most of the record
		//Without u.promo = 0, query might run in 0.5 second (300K users on dev)
		
		//Improve later: if user does not save any information on users_personal -> do not create record. This will keep database lighter -> faster
		$joinPersonal = " inner join users_personal as p on p.user_id = u.id  "; //and u.promo = '0'
		$personal = false;
		$where = ' where true ';
		
		if($this->username){
			$where .= " and u.username like '%{$this->username}%'";
		}
		if($this->gender_search) $where .= " and ".CHelperProfile::whereLookGender();
		if($this->age){
			$where .= " and ".CHelperProfile::whereAgeMin($this->age);
		}
		
		if($this->maxage){
			$where .= " and ".CHelperProfile::whereAgeMax($this->maxage);
		}
		
		if($this->has_photo){
			$where .= ' and u.pics > 0';
		}
		
		if($this->radius){
			$lat = $this->profile->getLocationValue('latitude');
			$lon = $this->profile->getLocationValue('longitude');
			$where .= " and ".CHelperProfile::whereLocation($lat, $lon, $this->radius, 'l');
		}
		
		
		if($this->height){
			$personal = true;
			$where .= " and p.height >= {$this->height}";
		}
		if($this->maxheight){
			$personal = true;
			$where .= " and p.height <= {$this->maxheight}";
		}
		
		if($this->income){
			$personal = true;
			$where .= " and p.income >= {$this->income}";
		}
		if($this->maxincome){
			$personal = true;
			$where .= " and p.maxincome <= {$this->maxincome}";
		}
		
		if($this->language){
			$personal = true;
			$where .= " and (instr('{$this->language}', p.1st_language) or instr('{$this->language}', p.2nd_language)))";
		}
		
		foreach($this->options as $key => $value){
			$personal = true;
			$liststr = explode(',', $value);
			$listarrstr = '';
			foreach($liststr as $sglvalue){
				if($sglvalue && $sglvalue != '' && $sglvalue != '0'){
					$listarrstr .= ($listarrstr==''?'':',')."'$sglvalue'";
				}
			}
			//$where .= " and INSTR(p.$key, ',$value,') > 0 ";	
			if($listarrstr != ''){		
				$where .= " and p.$key in ($listarrstr) ";	
			}	
		}
		
		if($this->interests){
			$arr = explode(',',$this->interests);
			
			$q = '';
			foreach($arr as $v){
				if(trim($v) != ''){
					$q .= ($q == ''?'':' or ')." INSTR(p.interests, ',$v,') > 0";
				}
			}
			if($q != ''){
				$q = " and ($q)";
				$personal = true;				
				$where .= $q;
			}		
		}
		
		if($this->looking_for){
			$arr = explode(',',$this->looking_for);
			
			$q = '';
			foreach($arr as $v){
				if(trim($v) != ''){
					$q .= ($q == ''?'':' or ')." INSTR(p.looking_for, ',$v,') > 0";
				}
			}
			if($q != ''){
				$q = " and ($q)";
				$personal = true;
				$where .= $q;
			}
		}
	}
	
	public function queryData(){
		
		$this->internalBuildQuery($qry, $qryCount, $joinPersonal, $personal, $where);
		
		//Checking cache here
		$mkey = 'seach_cache_'.Yii::app()->user->id;
		$cacheData = Yii::app()->cache->get( $mkey );
		
		$start = $this->perpage * ($this->page - 1);
		if($start<0) $start = 0;
		$cacheMaxItem = 200;	
		$cacheStart = floor($start/$cacheMaxItem)*$cacheMaxItem;
		$searchLimit = $cacheMaxItem + $this->perpage; 
		//Reserve $this->perpage for the last page
		//For e.g. get(99, 20) -> return items [99,119] in cache (assume perpage = 20)
		//		   get(100, 20) -> requery new data [100, 220)
		
		if(isset($cacheData['where']) && isset($cacheData['items'])
			&& $cacheData['where'] == $where
			&& $cacheData['start'] <= $start 
			//&& ($start + $this->perpage) < ($cacheData['start'] + count($cacheData['items'])) //
			&& ($start + $this->perpage) < ($cacheData['start'] + $searchLimit) 
		//Do not need re-query when it reach the searching last items
		){
			$this->count = $cacheData['count'];
			$this->data = array_slice($cacheData['items'], $start - $cacheData['start'], $this->perpage);
			return;
		}
		if(!$cacheData || $cacheData['where'] != $where){
			$cacheData = array('where' => $where, 'start' => $cacheStart, 'realcount' => null);
		}
		else {
			$cacheData['start'] = $cacheStart;
		}
		
		//$qry .= ($personal? $joinPersonal : '').$where." limit $cacheStart, $searchLimit";
		//		
		//$cacheData['items'] = Yii::app()->db
		//	->createCommand($qry)
		//	->queryAll();
		//
		//if(!$cacheData['items']) $cacheData['items'] = array();
		
		if(!isset($cacheData['count'])){ //Do not need to re-query count when we only need to update item range
			
			$qryAllCount = $qryCount.($personal? $joinPersonal : '').$where;
			
			$cacheData['count'] = Yii::app()->db
				->createCommand($qryAllCount)
				->queryScalar();
			
			if(!$cacheData['count']) $cacheData['count'] = 0;
		}
		
		if(!isset($cacheData['realcount'])){ //Do not need to re-query count when we only need to update item range
			
			$qryRealCount = $qryCount." and u.promo = '0' ". ($personal? $joinPersonal : '').$where;
			
			$cacheData['realcount'] = Yii::app()->db
				->createCommand($qryRealCount)
				->queryScalar();
			
			if(!$cacheData['realcount']) $cacheData['realcount'] = 0;
		}
		
		$cacheData['items'] = array();
		
		//Search real users first
		if($cacheStart < $cacheData['realcount']){
			$realqry = $qry." and u.promo = '0'
						inner join users_activity as a on a.user_id = u.id 
						". ($personal? $joinPersonal : '').$where." 
						order by activityLast desc
						limit $cacheStart, $searchLimit";
			
			$cacheData['items'] = Yii::app()->db
				->createCommand($realqry)
				->queryAll();
			
			if(!$cacheData['items']) $cacheData['items'] = array();
		}
		
		//If has not found enough real user
		if($cacheData['realcount'] < $cacheStart + $searchLimit){
			
			if($cacheData['realcount'] < $cacheStart){
				$promostart = $cacheStart - $cacheData['realcount'];
				$promolimit = $searchLimit;
			}
			else {
				$promostart = 0;
				$promolimit = $cacheStart + $searchLimit - $cacheData['realcount'];
			}
			
			$promoqry = $qry." and u.promo = '1'  
						inner join users_activity as a on a.user_id = u.id 
						". ($personal? $joinPersonal : '').$where." 
						 order by activityLast desc
						 limit $promostart, $promolimit";
			
			$promoData = Yii::app()->db
				->createCommand($promoqry)
				->queryAll();
			
			if(!$promoData) $promoData = array();	
			
			$cacheData['items'] = array_merge($cacheData['items'], $promoData);
		}
		
		$this->count = $cacheData['count'];
		$this->data = array_slice($cacheData['items'], $start - $cacheData['start'], $this->perpage);
		
		//Save cache here
		Yii::app()->cache->set($mkey, $cacheData, Yii::app()->params->cache['profile']);
	}
	
	
	
	
	public function queryData2(){
		
		$this->internalBuildQuery($qry, $qryCount, $joinPersonal, $personal, $where);
		
		//Checking cache here
		$mkey = 'seach_cache_'.Yii::app()->user->id;
		$cacheData = Yii::app()->cache->get( $mkey );
		
		$start = $this->perpage * ($this->page - 1);
		if($start<0) $start = 0;
		$cacheMaxItem = 200;	
		$cacheStart = floor($start/$cacheMaxItem)*$cacheMaxItem;
		$searchLimit = $cacheMaxItem + $this->perpage; 
		//Reserve $this->perpage for the last page
		//For e.g. get(99, 20) -> return items [99,119] in cache (assume perpage = 20)
		//		   get(100, 20) -> requery new data [100, 220)
		
		if(isset($cacheData['where']) && isset($cacheData['items'])
			&& $cacheData['where'] == $where
			&& $cacheData['start'] <= $start 
			//&& ($start + $this->perpage) < ($cacheData['start'] + count($cacheData['items'])) //
			&& ($start + $this->perpage) < ($cacheData['start'] + $searchLimit) 
		//Do not need re-query when it reach the searching last items
		){
			$this->count = $cacheData['count'];
			$this->data = array_slice($cacheData['items'], $start - $cacheData['start'], $this->perpage);
			return;
		}
		if(!$cacheData || $cacheData['where'] != $where){
			$cacheData = array('where' => $where, 'start' => $cacheStart);
		}
		else {
			$cacheData['start'] = $cacheStart;
		}
		
		if(!isset($cacheData['count'])){ //Do not need to re-query count when we only need to update item range
			
			$qryAllCount = $qryCount.($personal? $joinPersonal : '').$where;
			
			$cacheData['count'] = Yii::app()->db
				->createCommand($qryAllCount)
				->queryScalar();
			
			if(!$cacheData['count']) $cacheData['count'] = 0;
		}
		

		$realqry = $qry."
						inner join users_activity as a on a.user_id = u.id 
						". ($personal? $joinPersonal : '').$where." 
						order by activityLast desc, u.promo
						limit $cacheStart, $searchLimit";
		
		$cacheData['items'] = Yii::app()->db
			->createCommand($realqry)
			->queryAll();
		
		if(!$cacheData['items']) $cacheData['items'] = array();
		
		
		$this->count = $cacheData['count'];
		$this->data = array_slice($cacheData['items'], $start - $cacheData['start'], $this->perpage);
		
		//Save cache here
		Yii::app()->cache->set($mkey, $cacheData, Yii::app()->params->cache['profile']);
	}
	
	
	public function combineOptions(){
		$options = $this->options;
		if($this->radius) $options['radius'] = $this->radius;
		if($this->username) $options['username'] = $this->username;
		if($this->gender_search) $options['gender_search'] = $this->gender_search;
		if($this->age) $options['age'] = $this->age;
		if($this->maxage) $options['maxage'] = $this->maxage;
		if($this->height) $options['height'] = $this->height;
		if($this->maxheight) $options['maxheight'] = $this->maxheight;
		if($this->income) $options['income'] = $this->income;
		if($this->maxincome) $options['maxincome'] = $this->maxincome;
		if($this->interests) $options['interests'] = $this->interests;
		if($this->looking_for) $options['looking_for'] = $this->looking_for;
		if($this->has_photo) $options['has_photo'] = $this->has_photo;
		if($this->language) $options['language'] = $this->language;
		return $options;
	}
	
	/*public function init()
	{
	    //$this->gender = array(Yii::app()->user->data('gender'));
	    $this->looking_for_gender = "";//CHelperProfile::getLookGender();
	    
	    //$this->onlineNow = Yii::app()->user->settings('onlineNow');
	    //$this->withPhoto = Yii::app()->user->settings('withPhoto');
	    
	    $this->miles = 250;
	    $this->sortby = 'L';
	    
	    if (isset($_REQUEST['SearchForm']))
	    {
	        if ( in_array($_REQUEST['SearchForm']['looking_for_gender'], array('','M','F')) )
	            $this->looking_for_gender = $_REQUEST['SearchForm']['looking_for_gender'];            
	        
	        if ( in_array($_REQUEST['SearchForm']['ageMin'], Yii::app()->helperProfile->getAges()) )
	            if ($_REQUEST['SearchForm']['ageMin'] != Yii::app()->user->settings('ageMin'))
	                Yii::app()->user->Profile->settingsUpdate('ageMin', $_REQUEST['SearchForm']['ageMin']);
	    
	        if ( in_array($_REQUEST['SearchForm']['ageMax'], Yii::app()->helperProfile->getAges()) )
	            if ($_REQUEST['SearchForm']['ageMax'] != Yii::app()->user->settings('ageMax'))
	                Yii::app()->user->Profile->settingsUpdate('ageMax', $_REQUEST['SearchForm']['ageMax']);
	        
	        if ( $_REQUEST['SearchForm']['miles']>=10 && $_REQUEST['SearchForm']['miles']<=500 )
	            $this->miles = $_REQUEST['SearchForm']['miles'];
	            
	            
	        if ( in_array($_REQUEST['SearchForm']['sortby'], array('L','N')) )
	            $this->sortby = $_REQUEST['SearchForm']['sortby'];
	    }
	    
	    $this->ageMin = Yii::app()->user->settings('ageMin');
	    $this->ageMax = Yii::app()->user->settings('ageMax');
	}    */

	
	/**
	 * Declares the validation rules.
	 */
	/*public function rules()
	{
		return array(
	           array('looking_for_gender', 'in', 'range'=>array('','M','F') ),
	           array('ageMin', 'in', 'range'=>Yii::app()->helperProfile->getAges() ),
	           array('ageMax', 'in', 'range'=>Yii::app()->helperProfile->getAges() ),
	           array('sortby', 'in', 'range'=>array('L','N') ),
	           array('miles', 'milesCheck'),
	           //array('location_id', 'locationCheck'),
		);
	}*/
	
	
	/**
	 * Declares attribute labels.
	 */
	/*public function attributeLabels()
	{
		return array(
	           //'onlineNow'=>'Online now',
	           //'withPhoto'=>'With photos',
		);
	}*/
	
	/*public function milesCheck()
	{
	    if ($this->miles<10 || $this->miles>500)
	    {
	        $this->addError('miles','Bad miles');
	    }
	}*/
	
}
