<?php
class PanelOnlineNowCounterWidget extends CWidget
{
    public function init()
    {
        $count = Profiles::countOnlineNowAll();
        
        //if (!$count/* && LOCAL_OK*/) $count = rand(1111, 99999);
        
        if ($count)
        {
	        $count = strval($count);
        }
        
        $country = (isset($_SERVER['GEOIP_COUNTRY_CODE']) && $_SERVER['GEOIP_COUNTRY_CODE']) ? $_SERVER['GEOIP_COUNTRY_CODE'] : 'US';
        //$locationRecord = Yii::app()->location->getGeoIPRecord();
        
        $profiles = $this->getProfilesGeoip($country);
        if (!$profiles || count($profiles)<6)
        {
        	$profiles = $this->getProfilesGeoip('US');
        }
        
        $this->render('panelOnlineNowCounter', array('count'=>$count, 'profiles'=>$profiles));
    }
    
    
    
    private function getProfilesGeoip($country='US')
    {
    	$mkey = "ProfilesFront_{$country}";
    	$res = Yii::app()->cache->get($mkey);
    	if ($res===false)
    	{
    		$sql = "SELECT 
    					u.id 
    				FROM 
    					`users_location` as l, users as u, user_image as i
    				WHERE 
    					l.country='{$country}' 
    					AND 
    					u.promo='1' 
    					AND 
    					i.primary='1' AND i.xrated='clothed'
    					AND 
    					u.id=l.user_id AND u.id=i.user_id 
    				LIMIT 5000";
    		$res = Yii::app()->db->createCommand($sql)->queryColumn();

//    		shuffle($res);
//    		array_slice($res,0,5);
    		
    		Yii::app()->cache->set($mkey, $res, Yii::app()->params->cache['profile']);
    	}
		
    	if ($res)
    		shuffle($res);
    	
    	return $res;
    }
}
?>
