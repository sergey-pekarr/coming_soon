<?php

class CHelperProfile extends CApplicationComponent
{
	public static function checkBirthday($birthday=array())
	{
		if ( !checkdate ( $birthday['month'] , $birthday['day'] , $birthday['year'] ) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	static function checkBirthdayAge18($birthday=array())
	{
		if ( !checkdate ( $birthday['month'] , $birthday['day'] , $birthday['year'] ) )
		{
			return false;
		}
		else
		{
			$date = strtotime($birthday['year'].'-'.$birthday['month'].'-'.$birthday['day']);
			return ( $date < strtotime('18 years ago') );
		}
	}    
	
	static public function getAge($birthday)
	{
		$age = time() - strtotime($birthday);
		return floor($age / 31556926); // 31556926 seconds in a year
	}
	
	static function getDays($select=false, $selectText='...')
	{
		if ($select)
			$days[''] = $selectText;
		
		for ($i=1; $i<=31; $i++)
		{
			$days[sprintf("%02d", $i)] = sprintf("%02d", $i);
		}
		return $days;
	}

	static function getMonth($select=false, $digital=true, $selectText='...')
	{
		if ($select)
			$month[''] = $selectText;

		for ($i=1; $i<=12; $i++)
		{
			if ($digital)
				$month[sprintf("%02d", $i)] = sprintf("%02d", $i);
			else 
				$month[sprintf("%02d", $i)] = date( "F", mktime(0, 0, 0, $i, 1) ) ;
		}
		return $month;
	}
	
	static function getYear($select=false, $selectText='Select')
	{
		if ($select)
			$year[''] = $selectText;
		
		$y = date('Y');
		for ($i=($y-18); $i>=($y-100); $i--)
		{
			$year[$i] = $i;
		}
		return $year;
	}
	
	static public function getAges()
	{
		for ($i=18; $i<=99; $i++)
		{
			$ages[$i] = $i;
		}
		return $ages;
	}    

	/*
	 * use in forms for dropdown in reg forms
	 */
	static public function getGenders($rangeOnly=false)
	{
		if ($rangeOnly)
			return array('0','1','2','3');
		else
			return array(
				'0' => 'Guy Looking For Girls',
				'1' => 'Girl looking for Guys',
				'2' => 'Guy looking for Guys',
				'3' => 'Girl looking for Girls',
				);
	} 
	
	

	static function getPersonalInteresting($type=1)
	{
		switch($type)
		{
			case 1 : return array('F'=>"Friends", 'D'=>"Flirting, Dating", 'N'=>"Networking");
			case 2 : return array('F'=>"Friends", 'D'=>"Dating", 'N'=>"Networking");
		}
		return array();
	}

	static function getPersonalValueList()
	{
		return 
		array(
			'height'=>array(    
					'Prefer not to say',
					'4 ft 7 in or smaller (140 cm)',
					'4 ft 8 in (143 cm)',
					'4 ft 9 in (145 cm)',
					'4 ft 10 in (148 cm)',
					'4 ft 11 in (150 cm)',
					'5 ft 0 in (153 cm)',
					'5 ft 1 in (155 cm)',
					'5 ft 2 in (158 cm)',
					'5 ft 3 in (161 cm)',
					'5 ft 4 in (163 cm)',
					'5 ft 5 in (166 cm)',
					'5 ft 6 in (168 cm)',
					'5 ft 7 in (171 cm)',
					'5 ft 8 in (173 cm)',
					'5 ft 9 in (176 cm)',
					'5 ft 10 in (178 cm)',
					'5 ft 11 in (181 cm)',
					'6 ft 0 in (183 cm)',
					'6 ft 1 in (186 cm)',
					'6 ft 2 in (188 cm)',
					'6 ft 3 in (191 cm)',
					'6 ft 4 in (194 cm)',
					'6 ft 5 in (196 cm)',
					'6 ft 6 in (199 cm)',
					'6 ft 7 in (201 cm)',
					'6 ft 8 in (204 cm)',
					'6 ft 9 in (206 cm)',
					'6 ft 10 in (209 cm)',
					'6 ft 11 in (211 cm)',
					'7 ft 0 in (214 cm)',
					'7 ft 1 in (216 cm)',
					'7 ft 2 in (219 cm)',
					'7 ft 3 in (221 cm)',
					'7 ft 4 in (224 cm)',
					'7 ft 5 in (227 cm)',
					'7 ft 6 in (229 cm)',
					'7 ft 7 in (232 cm)',
					'7 ft 8 in (234 cm)',
					'7 ft 9 in (237 cm)',
					'7 ft 10 in or taller (239 cm)'                    
					/*'Prefer not to say',
					'< 5 ft 0 in / < 152 cm',
					'5 ft 0 in / 153-155 cm',
					'5 ft 1 in / 156-157 cm',
					'5 ft 2 in / 158-160 cm',
					'5 ft 3 in / 161-162 cm',
					'5 ft 4 in / 163-165 cm',
					'5 ft 5 in / 166-167 cm',
					'5 ft 6 in / 168-170 cm',
					'5 ft 7 in / 171-172 cm',
					'5 ft 8 in / 173-175 cm',
					'5 ft 9 in / 176-178 cm',
					'5 ft 10 in / 179-180 cm',
					'5 ft 11 in / 181-183 cm',
					'6 ft 0 in / 184-185 cm',
					'6 ft 1 in / 186-188 cm',
					'6 ft 2 in / 189-190 cm',
					'6 ft 3 in / 191-193 cm',
					'6 ft 4 in / 194-195 cm',
					'> 6 ft 4 in / > 195 cm'*/
					),
				'race'=>array(
					'Prefer not to say',
					'White / Caucasian',
					'African american',
					'Asian',
					'Black / African',
					'East indian',
					'Hispanic / Latino',
					'Inter racial',
					'Middle eastern',
					'Native American',
					'Pacific Islander',
					'Other'
					/*'Prefer not to say',
					'American Indian',
					'Asian',
					'Black',
					'Caucasian',
					'East Indian',
					'Hispanic',
					'Middle Eastern',
					'Various',
					'Other'*/
					),
				'religion'=>array(
					'Prefer not to say',
					'Christian',
					'Christian / Catholic',
					'Christian / LDS',
					'Christian / Protestant',
					'Christian / Other',
					'Buddhist / Taoist',
					'Hindu',
					'Islam',
					'Jewish',
					'Pagan',
					'Atheist',
					'None / Agnostic',
					'Scientology',
					'Spiritual but not religious',
					'Other'
					/*'Prefer not to say',
					'Agnostic',
					'Atheist',
					'Buddhist/Taoist',
					'Catholic',
					'Hindu',
					'Islamic',
					'Jewish',
					'Protestant',
					'Spiritual',
					'Other'*/
					),
				'hairColor'=>array(
					'Prefer not to say',
					'Black',
					'Brown',
					'Blackish Brown',
					'Auburn',
					'Blonde',
					'Light Brown',
					'Dark Brown',
					'Red',
					'White/grey',
					'Bald',
					'A little grey'
					/*'Prefer not to say',
					'Black',
					'Brown',
					'Blond',
					'Auburn',
					'Red',
					'Gray'*/
					),
				'eyeColor'=>array(
					'Prefer not to say',
					'Black',
					'Blue',
					'Brown',
					'Gray',
					'Green',
					'Hazel'                    
					/*'Prefer not to say',
					'Amber',
					'Blue',
					'Brown',
					'Gray',
					'Green',
					'Hazel',
					'Red'*/
					),                
				'bodyType'=>array(
					'Prefer not to say',
					'Slim',
					'Slender',
					'Average',
					'Fit',
					'Smart',
					'Athletic',
					'Muscular',
					'A few extra pounds',
					'Thick',
					'Fatty',
					'Voluptuous',
					'Large'
					/*'Prefer not to say',
					'Average',
					'Slim',
					'Athletic',
					'Ample',
					'A little extra padding',
					'Large'*/
					),
				'profession'=>array(
					'Prefer not to say',
					'Not Working',
					'Student',
					'Engineering/Technical',
					'Business (Management)',
					'Clerical (Office/Shop)',
					'Design (Architect/Fashion)',
					'Education',
					'Government (Office/Shop)',
					'Medical (Nursing/Physician)',
					'Other'
					),
				'smoker'=>array(
					'Prefer not to say',
					'No, never',
					'No, but have in past',
					'Yes, but trying to quit',
					'Yes, occasionally',
					'Yes, regularly',
					),                
				'drink'=>array(
					'Prefer not to say',
					'No, never',
					'Just socially',
					'Frequently',
					),                 

				
				);
	}
	


	//TEXT
	/**
	 */
	static function textGender($gender='')
	{
		if (!$gender)
			$gender = Yii::app()->user->data('gender');
		
		return str_replace(array('M','F'), array('Man','Woman'), $gender);
	}
	
	static function textLookGender($lookGender='')
	{
		if (!$lookGender)
			$lookGender = Yii::app()->user->data('looking_for_gender');
		
		if (!$lookGender)
			$lookGender = 'F';
		
		//n 2012-07-06
		return str_replace(array('M','F', ' '), array('Man','Woman', ' and '), $lookGender);
	}    
	
	static function textHerHis($gender, $UpperFirst=false)
	{
		if ($UpperFirst)
			return str_replace(array('M','F'), array('Him','Her'), $gender);
		else
			return str_replace(array('M','F'), array('him','her'), $gender);
	}    
	
	static function textInteresting($str, $type=1)
	{
		if ($str)
		{
			$interesting = str_split($str);
			
			$interesting = str_replace( array('F','D','N'), self::getPersonalInteresting($type), $interesting );
			
			$interesting = implode(', ', $interesting);
		}        
		return $interesting;
	}    
	
	static function getLookGender($userId=0, $replaceName=false)
	{
		if (!$userId)
			$userId = Yii::app()->user->id;
		
		$looking_for_gender = 'F';
		
		if ($userId) 
		{
			$profile = new Profile($userId);
			/*$profileData = $profile->getData();
			            
			$gender = $profileData['gender'];
			$looking_for_gender = ($gender=='M') ? 'F' : 'M';*/
			$looking_for_gender = $profile->getDataValue('looking_for_gender');
		}
		
		if ($replaceName)
			//$lookGender = str_replace(array('M','F'), array('Male', 'Female'), $lookGender);
			//n 2012-07-06
			$looking_for_gender = str_replace(array('M','F', ' '), array('Man','Woman', ' and '), $lookGender);
		
		return $looking_for_gender;
	}
	
	//where
	/**
	 * WHERE LookGender
	 */
	static function whereLookGender($userId=0)
	{
		if (!$userId)
			$userId = Yii::app()->user->id;
		//      
		$looking_for_gender = self::getLookGender($userId, false);
		if (!is_array($looking_for_gender))
			$looking_for_gender = str_split($looking_for_gender);
		
		if (count($looking_for_gender)>1)
		{
			return "gender IN ('".implode("','",$looking_for_gender)."')";//��� ���������� �������
		}
		else
			return "gender='".$looking_for_gender[0]."'";//��� ���������� �������
	}
	
	
	/**
	 * WHERE AgeMin
	 */
	static function whereAgeMin($ageMin=0)
	{
		if (!$ageMin) $ageMin = Yii::app()->user->settings('ageMin');        
		if (!$ageMin) $ageMin = 18;        
		return "birthday<='".date("Y-m-d",strtotime( $ageMin .' years ago'))."'";
	}    
	/**
	 * WHERE AgeMax
	 */
	static function whereAgeMax($ageMax=0)
	{
		if (!$ageMax) $ageMax = Yii::app()->user->settings('ageMax');        
		if (!$ageMax) $ageMax = 99;
		return "birthday>'".date("Y-m-d",strtotime( ($ageMax+1) .' years ago'))."'";
	} 
	
	/*
	 * by lat,lon,range
	 */
	static function whereLocation($lat, $lon, $range_to=30, $prefix='z')
	{
		$lat_range = $range_to/69.172;
		$lon_range = abs($range_to/(cos($lat * pi()/180) * 69.172));
		$min_lat = number_format($lat - $lat_range, "4", ".", "");
		$max_lat = number_format($lat + $lat_range, "4", ".", "");
		$min_lon = number_format($lon - $lon_range, "4", ".", "");
		$max_lon = number_format($lon + $lon_range, "4", ".", "");
		
		$res = " {$prefix}.latitude BETWEEN '$min_lat' AND '$max_lat' 
        		 AND 
        		 {$prefix}.longitude BETWEEN '$min_lon' AND '$max_lon'";        
		return $res;
	}     
	
	//views
	static function truncStr($string, $count)
	{
		if (strlen($string) > $count) {            
			return mb_substr($string, 0, $count).'...';            
		} else {            
			return $string;            
		}
	}
	
	static function truncName($string, $count=10)
	{
		return self::truncStr($string, $count);
	}
	
	/**
	 * types:
	 * 1 - Age,G,City...,Country 
	 * 2 - City..., ...State..., Country
	 * 3 - City..., Country
	 * 4 - Age, G
	 * 5 Age: age
	 * 6 - profileBox
	 * 7 - City..., ...State..., CountryNAME...
	 * 8 - Username, Age, City...
	 * 9 - Age,City...,State/stateCode
	 * 10- City...,State.../stateCode
	* 11 - Age
	* 12 - City..., State
	* 13 - Age, GenderName
	* 14 - State, Country
	 */        
	static function showProfileInfoSimple($profile, $infoType, $len=0, $truncate=true, $location=array())
	{
		
		if (!$location)
		{
			$location = $profile->getDataValue('location');
		}
		
		switch ($infoType)
		{
			case 1://Age,G,City,Country
				$len = ($len) ? $len : 24;
				$res = $profile->getDataValue('age').', '.$profile->getDataValue('gender').', '.$location['city'].', '.$location['country'];
				if (strlen($res) > $len)
				{
					$newCityLen = strlen($location['city']) - (strlen($res)-$len) - 3;
					$res = $profile->getDataValue('age').', '.$profile->getDataValue('gender').', '.self::truncStr($location['city'], $newCityLen).', '.$location['country'];
				}                
				return $res;
			
			case 2://City..., ...State..., Country
				$len = ($len) ? $len : 14;
				$stateName = "";
				if ($state = $location['state'])
				{
					if (is_numeric($state))
					{
						$stateName = $location['stateName'];
					}
					else
					{
						$stateName = $state;
					}
					$stateName = ($stateName) ? ', '.$stateName : '';
				}                
				
				$res = $location['city'] . ( isset($stateName)?$stateName:'' ).', '.$location['country'];
				if (strlen($res) > $len)
				{
					if ($state && is_numeric($state))
					{
						//echo $profile->getLocationValue('state').' - '.$state.' -|';
						$res = self::showProfileInfoSimple($profile, 3, $len);
					}
					else
					{
						$newCityLen = strlen($location['city']) - (strlen($res)-$len) - 3;
						$res = self::truncStr($location['city'], $newCityLen) . $stateName . ', '.$location['country'];
					}
				}                
				return $res;  
			
			case 3://City..., Country
				$len = ($len) ? $len : 24;
				
				$res = $location['city'] . ', '.$location['country'];
				if (strlen($res) > $len)
				{
					$newCityLen = strlen($location['city']) - (strlen($res)-$len) - 3;
					$res = self::truncStr($location['city'], $newCityLen) . ', '.$location['country'];
				}
				return $res;                 

			case 4://Age,G
				$res = $profile->getDataValue('age').', '.$profile->getDataValue('gender');
				return $res;

			case 5://Age: age
				$res = 'Age: ' . $profile->getDataValue('age');
				return $res;

			case 7://City..., ...State..., CountryNAME
				$len = ($len) ? $len : 32;
				$stateName = "";
				if ($state = $location['state'])
				{
					if (is_numeric($state))
					{
						$stateName = $location['stateName'];
					}
					else
					{
						$stateName = $state;
					}
					$stateName = ($stateName) ? ', '.$stateName : '';
				}                
				
				$countryName = Yii::app()->location->getCountryName( $location['country'] );
				$countryName = self::truncStr($countryName,14);
				$res = $location['city'] . $stateName . ', '. $countryName;
				if (strlen($res) > $len)
				{
					if ($state && is_numeric($state))
					{
						//echo $profile->getLocationValue('state').' - '.$state.' -|';
						$res = self::showProfileInfoSimple($profile, 3, $len);
					}
					else
					{
						$newCityLen = strlen($location['city']) - (strlen($res)-$len) - 3;
						$res = self::truncStr($location['city'], $newCityLen) . $stateName . ', '.$countryName;
					}
				}                
				return $res;
			
			case 8://Username, Age, City...
				$len = ($len) ? $len : 50;
				$res = $profile->getDataValue('username').', '.$profile->getDataValue('age').', '.$location['city'];
				if (strlen($res) > $len)
				{
					$newCityLen = strlen($location['city']) - (strlen($res)-$len) - 3;
					$res = $profile->getDataValue('username').', '.$profile->getDataValue('age').', '.self::truncStr($location['city'], $newCityLen);
				}                
				return $res;                

			case 9://Age,City...,State/StateCode
				$len = ($len) ? $len : 30;
				$stateName = "";
				$state = $location['state'];//Yii::app()->location->getStateCode($location['country'], $location['state']);                
				if ($state)
				{
					if (preg_match('/[0-9]{1,2}$/', $state))//if (is_numeric($state))
					{
						$stateName = self::truncStr($location['stateName'], 6);
					}
					else
					{
						$stateName = $state;
					}
					$stateName = ($stateName) ? ', '.$stateName : '';
				} 
				
				$res = $profile->getDataValue('age').', '.$location['city'].$stateName;
				if (strlen($res) > $len)
				{
					$newCityLen = strlen($location['city']) - (strlen($res)-$len) - 3;
					$res = $profile->getDataValue('age').', '.self::truncStr($location['city'], $newCityLen).$stateName;
				}                
				return $res;
			
			case 10://City...,State/StateCode
				$len = ($len) ? $len : 40;
				$stateName = "";
				$state = $location['state'];//Yii::app()->location->getStateCode($location['country'], $location['state']);
				
				if ($state)
				{
					if (preg_match('/[0-9]{1,2}$/', $state))//if (is_numeric($state))
					{
						$stateName = $location['stateName'];
					}
					else
					{
						$stateName = $state;
					}
					$stateName = ($stateName) ? ', '.$stateName : '';
				} 
				
				$city = $location['city'];
				
				$res = $city.$stateName;
				if (strlen($res) > $len)
				{
					if (strlen($stateName)>($len/2))
						$city = self::truncStr($stateName, $len/2);
					
					$res = $city.$stateName; 
					
					$newCityLen = strlen($location['city']) - (strlen($res)-$len) - 3;
					$res = self::truncStr($location['city'], $newCityLen).$stateName;
				}                
				return $res;    
			
			// Does it duplicate 5? I see this line ater merge. Did you addd, Oleg?
			case 11://Age
				return $profile->getDataValue('age');  
			
			case 12:
				$len = ($len) ? $len : 14;
				$stateName = "";
				if ($state = $location['state'])
				{
					if (is_numeric($state))
					{
						$stateName = $location['stateName'];
					}
					else
					{
						$stateName = $state;
					}
					$stateName = ($stateName) ? ', '.$stateName : '';
				}                
				
				$res = $location['city'] . $stateName;
				if (strlen($res) > $len)
				{
					if ($state && is_numeric($state))
					{
						//echo $profile->getLocationValue('state').' - '.$state.' -|';
						$res = self::showProfileInfoSimple($profile, 3, $len);
					}
					else
					{
						$newCityLen = strlen($location['city']) - (strlen($res)-$len) - 3;
						$res = self::truncStr($location['city'], $newCityLen) . $stateName;
					}
				}                
				return $res; 
			case 13://Age,G
				$g = $profile->getDataValue('gender');
				if($g == 'F') $g = ", Female";
				else if($g == 'M') $g = ", Male";
				else $g = "";
				$res = $profile->getDataValue('age').$g;
				return $res; 
			
			case 14://State..., Country
				$len = ($len) ? $len : 14;
				$stateName = '';
				if ($state = $location['state'])
				{
					if (is_numeric($state))
					{
						$stateName = $location['stateName'];
					}
					else
					{
						$stateName = $state;
					}
				}                
				
				$res = $stateName . ($stateName == ''? '' :  ', ').$location['country'];               
				return $res;                
		}
		
		return '';        
	}
	
	
	
	static function getAutoLoginUrl($userId)
	{
		if (!$userId) return SITE_URL;
		
		$app=Yii::app();
		return SITE_URL.'/site/autologin/'.$app->secur->encryptID($userId).'/'.$app->getSecurityManager()->hashData($userId, SECURE_KEY);
	}        
	
	static function getUserIdFromAutoLoginUrl($get)
	{
		if ($userId = Yii::app()->secur->decryptID($get['idd']))
		{
			if ( $get['hash'] == Yii::app()->getSecurityManager()->hashData($userId, SECURE_KEY) ) 
				return $userId;
			else
				return 0;            
		}
		else
			return 0;
	}
	
	static function getConfirmEmailUrl($userId, $email)
	{
		if (!$userId) return SITE_URL;
		
		return self::getAutoLoginUrl($userId).'?redirect='. urlencode( '/confirmEmail?code=' . MD5( SALT . $email ) );
	}
	
	
	
	static function getUserImgDir($userId)
	{
		$profile = new Profile($userId);
		$dir_main = ($profile->getDataValue('promo')=='1') ? DIR_IMG_PROMO : DIR_IMG;//promo images for ALL sites
		
		$subdir1 = $userId + 999999 - (($userId-1)%1000000);
		$subdir2 = $userId + 999 - (($userId-1)%1000);
		return $dir_main.'/'.$subdir1.'/'.$subdir2.'/'.$userId;
	}

	
	//n Aug 15, 2012
	static function getUserQuizzImgDir($userId){
		$subdir1 = $userId + 999999 - (($userId-1)%1000000);
		$subdir2 = $userId + 999 - (($userId-1)%1000);
		return QUIZZ_DIR_IMG.'/'.$subdir1.'/'.$subdir2.'/'.$userId;
	}
	
	/*static function getUserImgUrl($userId)
	{
	    $subdir1 = $userId + 999999 - (($userId-1)%1000000);
	    $subdir2 = $userId + 999 - (($userId-1)%1000);
	    return URL_MEDIA.'/'.$subdir1.'/'.$subdir2.'/'.$userId;
	}*/
	
	
	/*
	 * for small 32x32 and medium 82x82 images only
	 * prepare and return url
	 */
	static function imageCachePrepare($user_id, $n, $size)
	{
		if ( !IMG_USE_CACHE_DIR || !$user_id || !$n || !in_array( $size, array('small', 'medium') ) )
			return false;
		
		$mkey = "imageCachePrepare_".$user_id."_".$n."_".$size;
		$fullUrl = Yii::app()->cache->get($mkey);
		if ( $fullUrl===false )
		{
			$fn = $user_id."_".$n."_".$size; 
			$fn = MD5(SALT.$fn).'.jpg';
			$subdir1 = substr($fn, 0, 2);
			$subdir2 = substr($fn, 2, 2);
			$subdir3 = substr($fn, 4, 2);
			$subdir4 = substr($fn, 6, 2);
			
			
			$dir = IMG_DIR_CACHE.'/'.$size.'/'.$subdir1.'/'.$subdir2.'/'.$subdir3.'/'.$subdir4;
			
			$fn = substr($fn, 8, (strlen($fn)-8) );    	
			
			$fileDest = $dir.'/'.$fn;
			
			if (!file_exists($fileDest))
			{
				if (!CHelperFile::createDir($dir))
					return false;				
				
				$pathSource = CHelperProfile::getUserImgDir($user_id);
				$filenameSource = $n."_".$size.".jpg";
				$fileSource = $pathSource .'/'.$filenameSource;
				
				@copy( $fileSource , $fileDest);
			}
			
			if (file_exists($fileDest))
				$fullUrl = IMG_URL_CACHE.'/'.$size.'/'.$subdir1.'/'.$subdir2.'/'.$subdir3.'/'.$subdir4.'/'.$fn;
			else
				$fullUrl = "";	
			
			Yii::app()->cache->set($mkey, $fullUrl, 600);
		}
		//FB::error($fullUrl, 'fullUrl');			
		return $fullUrl;
	}     
	
	/*
	 * check IP for blocking registartion from the same IP for last 6 mounth
	 * return true - existed 
	 * 
	 * 2012-07-24 changed to 3 month
	 */
	public static function checkExistedRegIP($ip='')
	{
//2013-03-07	
//2013-04-05	return false;//2013-03-12			2013-02-09		
		if (DEBUG_IP)
			return false;
		
		if (!$ip)
			$ip = CHelperLocation::getIPReal();
		
		if (LOCAL_OK) 
			$ip = '93.175.196.6';
		
		if (!$ip)
			return false;
		
		$mkey = "checkRegIP_".$ip;
		$res = Yii::app()->cache->get($mkey);
		if ($res === false)
		{
			$sql = "SELECT a.joined FROM `users_info` as i, `users_activity` as a WHERE i.ip_signup_long=:ip_signup_long AND a.user_id=i.user_id ORDER BY i.user_id DESC LIMIT 1";
			$joined = Yii::app()->db->createCommand($sql)
				->bindValue(":ip_signup_long", ip2long($ip), PDO::PARAM_INT)
				->queryScalar();

			if ( $joined && strtotime($joined)>strtotime("-2 MONTH") )
			{
				$res = 1;
				Yii::app()->cache->set($mkey, 1, 300);
			}			
			else
				$res = 0;
		}
		
		if ($res==1)
			Yii::app()->user->setFlash('regAlreadyRegistered', 'You are already registered member. Login please.');
		
		return ($res==1);
	}
	
	
	
	//n 2012-07-03
	static function getProfileLink($profile){
		$username = $profile->getDataValue('username');
		$encid = Yii::app()->secur->encryptID($profile->getId());
		return "<a href='/profile?id={$encid}'>{$username}</a>";
	}
	
	//n 2012-06-28
	//$type will prevent user change part of url to do another action
	//time() will cause url change by time
	public function getHashParameterEx($userid, $type = '', $expire = 22896000){ //365*86400 a year
		if (!$userid) return SITE_URL;
		$app=Yii::app();
		$expireTime = time() + $expire;
		$id = "$userid,$expireTime"; //Do not include $type here to keep idd as short as possible
		
		$idd = $app->secur->encryptByLikeNC($id);
		$idd = str_replace('/','-',$idd);
		$idd = urlencode($idd);
		
		//is there something I confuse about hashData. Does it hash each byte?
		//$hash = $app->getSecurityManager()->hashData($id.$type, SECURE_KEY);
		$hash = md5($id.$type.SECURE_KEY);
		
		return "$idd/$hash";
	}
	
	//n 2012-06-28
	public function getUseridFromHashParameterEx($get, $type=''){
		
		$idd = $get['idd'];
		$idd =urldecode($idd);
		$idd = str_replace('-','/',$idd);
		$hash = $get['hash'];
		
		$app=Yii::app();
		$id = $app->secur->decryptByLikeNC($idd);
		$temp = explode(',', $id);
		if(count($temp)==2){
			$userid = $temp[0];
			$expireTime = $temp[1];
			//$app->getSecurityManager()->hashData($id.$type, SECURE_KEY)
			if(md5($id.$type.SECURE_KEY) == $hash
				&& time() < $expireTime
			){
				return $userid;
			}			
		}
		return 0;
	}
	
	//n 2012-07-17

	/**
	 * if user is not gold member -> redirect to payment page
	 *
	 * @param mixed $action This is a description
	 * @param mixed $targetId This is a description
	 * @param mixed $link This is a description
	 * @return mixed return false if user is gold
	 *
	 */	
	static function getPaymentLinkWithAction($action, $targetId = null, &$link = '', &$nav = ''){
		$targetenc = '';
		if($targetId && $targetId != ''){
			$targetenc = Yii::app()->secur->encryptID($targetId);
		}
		$enc = Yii::app()->secur->encryptID(Yii::app()->user->id);
		if(Yii::app()->user->checkAccess('gold')){
			$link = '';
			return false;
		}
		$targetProfile = new Profile($targetId);
		
		//n 2012-07-17
		//Should hide action from link. new link will be: payment/\w{37},\w{32}
		$define = array(
			'sendmessage' => '73ff4',
			'sendfavourite' => '70134',
			'sendgift' => '00384',
			'viewfavourite' => '5de8g',
			'viewemail' => '356f5',
			'viewlargephoto' => 'acfcb',
			'searchmore' => '2d97e',
			'onlinemore' => '5f580',
			);
		
		$encaction = isset($define[$action])?$define[$action]:$action;
		$nav = $encaction;
		
		$targetName = $targetProfile->getDataValue('username');
		//How to check if profile is valid?
		switch($action){
			case 'sendmessage': 
				$link = "doRequestMember('{$encaction}{$targetenc}', '', 'You Must Upgrade To Send ".$targetName . " A Private Message', 'Your Message Has NOT been Sent!') ;";
				$nav = $encaction.$targetenc;
				return true;
				break;
			case 'sendfavourite':
				$link = "doRequestMember('{$encaction}{$targetenc}', '', 'You Must Upgrade To add " . $targetName . " as a favourite', 'You Must Upgrade To Favourite') ;";
				$nav = $encaction.$targetenc;
				return true;
				break;
			//case 'sendblock':
			//	break;
			case 'sendgift':
				$link = "doRequestMember('{$encaction}{$targetenc}', '', 'You Must Upgrade To Send ".$targetName . " as a gift', 'Your Message Has NOT been Sent!') ;";
				$nav = $encaction.$targetenc;
				return true;
				break;
			case 'viewfavourite':
				$link = "doRequestMember('{$encaction}', '', 'You Must Upgrade To see Who Favours You', 'See who is interested in your profile') ;";
				return true;
				break;
			case 'viewemail':
				$link = "doRequestMember('{$encaction}', '', 'To read more messages you must upgrade', 'To upgrade your account, please complete the details below to subscribe now!') ;";
				return true;
				break;
			//case 'viewlike':
			//	break;
			case 'viewlargephoto':
				$link = "doRequestMember('{$encaction}', '', 'You Must Upgrade To See Large Photos', 'As a free member you can only view thumbnail photos. To see full size photos you must upgrade to our Premium Plan.') ;";
				return true;
				break;
			case 'searchmore':
				$link = "doRequestMember('{$encaction}', '', 'You Must Upgrade To View More Search Results', 'To view the rest of the profiles in your search results you need to upgrade your account, please complete the details below to subscribe now!') ;";
				return true;
				break;
			case 'onlinemore':
				$link = "doRequestMember('{$encaction}', '', 'You Must Upgrade To View More Online Members', 'To view the rest of the online profiles available you need to upgrade your account, please complete the details below to subscribe now!') ;";
				return true;
				break;
			//case '':
			//	break;
		}
	}
	
	static public function countWords($text){
		if($text == null || $text == '') return 0;
		$word = 0;
		$text = strtolower($text);
		$i = 0;
		for($i=1; $i<strlen($text); $i++){
			$c = $text[$i-1];
			$cn = $text[$i];
			if((('a' <= $c && $c <= 'z') || ('0' <= $c && $c <= '9'))
				&& !(('a' <= $cn && $cn <= 'z') || ('0' <= $cn && $cn <= '9'))
			){
				$word++;
			}
		}
		if(strlen($text)>0){
			$c = $text[$i-1];
			if((('a' <= $c && $c <= 'z') || ('0' <= $c && $c <= '9'))){
				$word++;
			}
		}
		return $word;
	}
}
