<?php
class ProfilesLatestVerifiedWidget extends CWidget
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
    	
    	$mkey = "ProfilesLatestVerifiedWidget_"
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
	
	    	$where[] = "s.email_activated_at<>'0000-00-00 0:00:00'";
	    	
			$where[] = "u.id=s.user_id";
				
	    	$sql = "SELECT id FROM `users` as u, `users_settings` as s 
	    		WHERE ".implode(' AND ', $where)." 
	    		ORDER BY s.email_activated_at DESC
	    		LIMIT ".$this->limit;    	
	    	
	    	$ids = Yii::app()->db->createCommand($sql)->queryColumn();

	    	Yii::app()->cache->set($mkey, $ids, 300);
    	}
    	

    	$cityNear = CHelperProfile::truncStr(Yii::app()->user->Profile->getDataValue('location', 'city'), 12);
    	
        $this->render( 'profilesLatestVerified', array( 'ids'=>$ids, 'cityNear'=>$cityNear ) );
    }
}
?>
