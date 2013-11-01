<?php
class ProfilesPopularWidget extends CWidget
{
    public $limit = 4;
    //public $ageMin = 18;
    //public $ageMax = 99;
    public $withPhotoOnly = true;
    //public $milesRange = 30;
	
	public function init()
    {
    	
    	$ageMin = Yii::app()->user->settings('ageMin');
    	$ageMax = Yii::app()->user->settings('ageMax');
    	
    	$mkey = "ProfilesPopularWidget_"
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
	
			$where[] = "u.id=v.user_id";
				
	    	$sql = "SELECT id FROM `users` as u, `profile_viewed` as v 
	    		WHERE ".implode(' AND ', $where)." 
	    		ORDER BY FIELD( u.promo, '0', '1' ), v.count DESC
	    		LIMIT ".(20*$this->limit);    	
//FB::warn($sql);	    	
	    	$ids = Yii::app()->db->createCommand($sql)->queryColumn();
			
	    	shuffle($ids);
	    	$ids = array_slice($ids, 0, $this->limit);

	    	Yii::app()->cache->set($mkey, $ids, 300);
    	}
    	
		$cityNear = CHelperProfile::truncStr(Yii::app()->user->Profile->getDataValue('location', 'city'), 12);
    	
        $this->render( 'profilesPopular', array( 'ids'=>$ids, 'cityNear'=>$cityNear ) );
    }
}
?>
