<?php
class ProfilesFeaturedWidget extends CWidget
{
    public $limit = 4;
    //public $ageMin = 18;
    //public $ageMax = 99;
    public $withPhotoOnly = true;
    //public $milesRange = 30;
	
	public function init()
    {
    	
/*    	
example if need location:

		if (Yii::app()->user->id)
    		$where[] = "u.id<>".Yii::app()->user->id;
    	
    	$where[] = "u.role='gold'";
    	
    	if ($this->withPhotoOnly) 
    		$where[] = "u.pics<>0";
    	
    	$where[] = "u.".CHelperProfile::whereLookGender();
    	    	
    	$where[] = "u.".CHelperProfile::whereAgeMin($this->ageMin);
    	
    	$where[] = "u.".CHelperProfile::whereAgeMax($this->ageMax);

    	
    	$location = Yii::app()->user->Profile->getDataValue('location');
		$where[] = CHelperProfile::whereLocation($location['latitude'], $location['longitude'], $this->milesRange, 'z');
			
		$where[] = "u.id=a.user_id";
		$where[] = "u.id=z.user_id";
			
			
    	$sql = "SELECT id FROM `users` as u, `users_activity` as a, `users_location` as z 
    		WHERE ".implode(' AND ', $where)." 
    		ORDER BY a.activityLast DESC
    		LIMIT ".$this->limit;
FB::warn($sql);
*/

//FROM ALL locations:
    	
    	
    	
    	$ageMin = Yii::app()->user->settings('ageMin');
    	$ageMax = Yii::app()->user->settings('ageMax');
    	
    	$mkey = "ProfilesFeaturedWidget_"
    			.$ageMin."_"
    			.$ageMax."_"
    			.Yii::app()->user->Profile->getDataValue('looking_for_gender')."_"
    			.$this->withPhotoOnly;
    	
    	$ids = Yii::app()->cache->get($mkey);
    			
    	if ($ids === false)
    	{
	    	/*if (Yii::app()->user->id)
	    		$where[] = "u.id<>".Yii::app()->user->id;*/
	    	
	    	$where[] = "u.role='gold'";
	    	
	    	if ($this->withPhotoOnly) 
	    		$where[] = "u.pics<>0";
	    	
	    	$where[] = "u.".CHelperProfile::whereLookGender();
	    	    	
	    	$where[] = "u.".CHelperProfile::whereAgeMin($ageMin);
	    	
	    	$where[] = "u.".CHelperProfile::whereAgeMax($ageMax);
	
			$where[] = "u.id=a.user_id";
				
	    	$sql = "SELECT id FROM `users` as u, `users_activity` as a 
	    		WHERE ".implode(' AND ', $where)." 
	    		ORDER BY a.activityLast DESC
	    		LIMIT ".$this->limit;    	
	    	
	    	$ids = Yii::app()->db->createCommand($sql)->queryColumn();

	    	Yii::app()->cache->set($mkey, $ids, 300);
    	}
    	
		$cityNear = CHelperProfile::truncStr(Yii::app()->user->Profile->getDataValue('location', 'city'), 12);
    	
        $this->render( 'profilesFeatured', array( 'ids'=>$ids, 'cityNear'=>$cityNear ) );
    }
}
?>
