<?php
class SearchFilterWidget extends CWidget
{
	public $profile;
	//public $username;
	//public $radius;
	//public $photo;
	//public $age;
	//public $maxage;
	public $filter;
	
	public function init()
	{
		if(!$this->filter) $this->filter = array();
		if(!$this->profile) $this->profile = Yii::app()->user->Profile;
		//if(!$this->username) $this->username = '';
		//if(!$this->radius) $this->radius = 50;
		//if(!$this->photo) $this->photo = true;
	}
	
	public function run(){
		$this->render('searchfilter', array('location'=> CHelperProfile::showProfileInfoSimple($this->profile,  12), 
			'age' => isset($this->filter['age'])?$this->filter['age']: $this->profile->getSettingsValue('ageMin'),
			'maxage' => isset($this->filter['maxage'])?$this->filter['maxage']: $this->profile->getSettingsValue('ageMax'),
			'username' => isset($this->filter['username'])?$this->filter['username']: '',
			'radius' => isset($this->filter['radius'])?$this->filter['radius']: 50,
			'has_photo' => isset($this->filter['has_photo'])?$this->filter['has_photo']: true));
	}
}
