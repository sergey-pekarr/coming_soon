<?php



class Profiles
{
    /**
     * 
     */
    public function NearFront($countNeed=9)
    {
        //$this->data['location']
        
        /*if (!$location)
        {
            $location = Yii::app()->user->data('location');
        }
        $lat = $location['latitude'];
        $lon = $location['longitude'];

        $look_gender = 'F,M';       
        
        $profiles = Yii::app()->location->findNearestUsers($lat,$lon, 30,$look_gender, $countNeed);
        
        if (count($profiles)<$countNeed)
        {
            $profiles = Yii::app()->location->findNearestUsers($lat,$lon, 500,$look_gender, $countNeed);
        }

        if (count($profiles)<$countNeed)
        {
            $locationDefault = Yii::app()->location->getLocationDefault();
            //$profilesDef = Yii::app()->location->findNearestUsers($locationDefault['latitude'],$locationDefault['longitude'], 30, $look_gender, ($countNeed-count($profiles)) );
            $profilesDef = self::promosGetNearFront( $countNeed-count($profiles) );
            $profiles = array_merge_recursive($profiles, $profilesDef);
        }*/
        
        //$profiles = self::promosGetNearFront( $countNeed );
        
        return $profiles;
    } 

    /**
     * 
     */
    public function Near($countNeed=6, $location=array(), $look_gender='')
    {
        if (!$location)
        {
            $location = Yii::app()->user->data('location');
        }
        $lat = $location['latitude'];
        $lon = $location['longitude'];

            
        if (!$look_gender)
        {
            $look_gender = CHelperProfile::getLookGender();//Yii::app()->user->data('looking_for_gender');
        }        
        
        $mkey = "Near_".Yii::app()->user->id."_".$lat."_".$lon."_".$countNeed."_".$look_gender;
FB::error($mkey);
        if ( !$profiles = Yii::app()->cache->get($mkey) )
        {
FB::error($mkey, 'EMPTY');
            ///$profiles = Yii::app()->location->findNearestUsers($lat,$lon, 30,$look_gender, $countNeed);
    
            ///if (count($profiles)<$countNeed)
            {
                $profiles = Yii::app()->location->findNearestUsers($lat,$lon, 500,$look_gender, $countNeed);
            }
            
            Yii::app()->cache->set($mkey, $profiles, 180);            
        }
        

        
        /*$profiles1 = Yii::app()->location->findNearestUsers($lat,$lon, 30,$look_gender, $countNeed);

        if (count($profiles1)<$countNeed)
        {
            $profiles2 = Yii::app()->location->findNearestUsers($lat,$lon, 500,$look_gender, ($countNeed-count($profiles1)));
        }
        
        if ($profiles1)
            foreach($profiles1 as $p)
                $profiles[] = $p;

        if ($profiles2)
            foreach($profiles2 as $p)
                $profiles[] = $p;*/
        
        return $profiles;
    }    
    
    
    /**
     * 
     */
    public function NearAdv($location=array(), $range_to=100, $where, $start=0, $limit=20)
    {
        if (!$location)
        {
            $location = Yii::app()->user->data('location');
        }
        $lat = $location['latitude'];
        $lon = $location['longitude'];

        
        $profiles = Yii::app()->location->findNearestUsersAdv($lat,$lon, $range_to, $where, 0, 20);

        
        return $profiles;
    }
    
    


    private function _getProfiles($arr, $page, $perPage, $fillEmpty=true)
    {
        //page pagination
		$profiles = array();
        if ($arr)
        {
            foreach ($arr as $k=>$v)
                if ($k>=$page*$perPage && $k<($page*$perPage+$perPage))
                    $profiles[] = $v;            
        }
        
        //fill empty boxes
        if ( $fillEmpty && count($profiles)<$perPage)
        {
            $fillStart = (count($profiles)) ? count($profiles)-1 : 0;
            $fillCount = (count($profiles)) ? $perPage-count($profiles)+1 : $perPage;
            if (count($profiles))
                $profiles += array_fill($fillStart, $fillCount, null);
            else
                $profiles  = array_fill($fillStart, $fillCount, null);
        }        
                
        return $profiles;
    }
    
    public/*private*/ function _truncArrayIds($arr, $limit)
    {
        if ($arr && $limit)
        {
            if (Yii::app()->user->id)
                foreach ($arr as $k=>$v)
                    if ( $v['id']==Yii::app()->user->id )
                        unset($arr[$k]);
            
            while (count($arr)>$limit)
                unset($arr[count($arr)-1]);
            
            if ($arr)
            {
                foreach($arr as $v)
                {
                    $arrTMP[] = $v;
                }
                
                $arr = $arrTMP;
            }
        }
        return $arr;
    }
    
    /**
     * return array (IDs of users, countAll)
     * 
     */
    public function findProfileViewedTo($page, $perPage, $pagesMax)
    {
        $mkey = "Profiles_findProfileViewedTo_".Yii::app()->user->id;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT id_to as id FROM profile_view 
                    WHERE id_from=".Yii::app()->user->id."            
                    ORDER BY `when` DESC LIMIT 900";            
                        
            //FB::warn($sql);        
            
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            
            Yii::app()->cache->set($mkey, $res, Yii::app()->params['cache']['profile']);
        }

        $res = $this->_truncArrayIds($res, $pagesMax*$perPage);

        $profiles = $this->_getProfiles($res, $page, $perPage);
        
        return array('ids'=>$profiles, 'count'=>count($res));
    }  
    /**
     * return array (IDs of users, countAll)
     * 
     */
    public function findProfileViewedFrom($page, $perPage, $pagesMax)
    {
        $mkey = "Profiles_findProfileViewedFrom_".Yii::app()->user->id;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT id_from as id FROM profile_view 
                    WHERE id_to=".Yii::app()->user->id."            
                    ORDER BY `when` DESC LIMIT 900";
            
            FB::warn($sql);        
            
            $res = Yii::app()->db->createCommand($sql)->queryAll();

            Yii::app()->cache->set($mkey, $res, Yii::app()->params['cache']['profile']);
        }
        
        $res = $this->_truncArrayIds($res, $pagesMax*$perPage);        
        
        $profiles = $this->_getProfiles($res, $page, $perPage);
        
        return array('ids'=>$profiles, 'count'=>count($res));
    }  





    /**
     * return array (IDs of users, countAll)
     * 
     */
    public function findProfileLikeTo($page, $perPage, $pagesMax)
    {
        $mkey = "Profiles_findProfileLikeTo_".Yii::app()->user->id;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT * FROM profile_like 
                    WHERE 
                    (id_from=".Yii::app()->user->id." AND `like`=1) 
                    OR 
                    id_to=".Yii::app()->user->id."                    
                    ORDER BY id DESC LIMIT 5000";            
                        
            //FB::warn($sql);        
            
            $resTMP = Yii::app()->db->createCommand($sql)->queryAll();
            
            if ($resTMP)
            {
                foreach ($resTMP as $v1)
                {
                    if ($v1['id_from']==Yii::app()->user->id)
                    {
                        $findMatch = 0;
                        foreach ($resTMP as $v2)
                        {
                            if ( $v2['id_from'] == $v1['id_to'] && $v2['like']!=-1 )
                            {
                                $findMatch = 1;
                            }
                        }
                        if (!$findMatch)
                        {
                            $res[]['id'] = $v1['id_to'];
                        }                        
                    }

                } 
            }
            
            Yii::app()->cache->set($mkey, $res, Yii::app()->params['cache']['profile']);
        }

        $res = $this->_truncArrayIds($res, $pagesMax*$perPage);

        $profiles = $this->_getProfiles($res, $page, $perPage);
        
        return array('ids'=>$profiles, 'count'=>count($res));
    } 
    /**
     * return array (IDs of users, countAll)
     * 
     */
    public function findProfileLikeFrom($page, $perPage, $pagesMax)
    {
        $mkey = "Profiles_findProfileLikeFrom_".Yii::app()->user->id;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT * FROM profile_like 
                    WHERE 
                    (id_to=".Yii::app()->user->id."  AND `like`=1) OR id_from=".Yii::app()->user->id."
                    ORDER BY id DESC LIMIT 5000";            
                        
            //FB::warn($sql);        
            
            $resTMP = Yii::app()->db->createCommand($sql)->queryAll();
            
            if ($resTMP)
            {
                foreach ($resTMP as $v1)
                {
                    if ($v1['id_to']==Yii::app()->user->id)
                    {
                        $findMatch = 0;
                        foreach ($resTMP as $v2)
                        {
                            if ( $v2['id_to'] == $v1['id_from'] )
                            {
                                $findMatch = 1;
                            }
                        }
                        if (!$findMatch)
                        {
                            $res[]['id'] = $v1['id_from'];
                        }                        
                    }

                } 
            }
            
            Yii::app()->cache->set($mkey, $res, Yii::app()->params['cache']['profile']);
        }

        $res = $this->_truncArrayIds($res, $pagesMax*$perPage);

        $profiles = $this->_getProfiles($res, $page, $perPage);
        
        return array('ids'=>$profiles, 'count'=>count($res));
    } 
    /**
     * return array ( array ( id_to of users, when ), countAll )
     * 
     */
    public function findProfileMatches($page, $perPage, $pagesMax, $fillEmpty)
    {
        $mkey = "Profiles_findProfileMatches_".Yii::app()->user->id;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT * FROM profile_like 
                    WHERE 
                    (id_from=".Yii::app()->user->id." AND `like`=1) 
                    OR 
                    (id_to=".Yii::app()->user->id." AND `like`=1)                    
                    ORDER BY id DESC LIMIT 5000";            
                        
            //FB::warn($sql);        
            
            $resTMP = Yii::app()->db->createCommand($sql)->queryAll();
            
            if ($resTMP)
            {
                foreach ($resTMP as $v1)
                {
                    if ($v1['id_from']==Yii::app()->user->id)
                    {
                        $findMatch = 0;
                        foreach ($resTMP as $v2)
                        {
                            if ( $v2['id_from'] == $v1['id_to'] )
                            {
                                $when = (strtotime($v1['when']) > strtotime($v2['when'])) ? $v1['when'] : $v2['when'];
                                
                                $res[] = array(
                                    'id' => $v1['id_to'],
                                    'when'  => $when
                                );
                                break;
                            }
                        }
                    }

                } 
            }
            
            Yii::app()->cache->set($mkey, $res, Yii::app()->params['cache']['profile']);
        }

        $res = $this->_truncArrayIds($res, $pagesMax*$perPage);

        $profiles = $this->_getProfiles($res, $page, $perPage, $fillEmpty);
      
        return array('ids'=>$profiles, 'count'=>count($res));
    } 







    /**
     * return array (IDs of users, countAll)
     * 
     */
    public function findOnlineNow($page, $perPage, $pagesMax)
    {
        $look_for_gender = CHelperProfile::whereLookGender();
        $ageMin = Yii::app()->user->settings('ageMin');
        $ageMax = Yii::app()->user->settings('ageMax'); 
    	
    	$mkey = "Profiles_findOnlineNow_".$look_for_gender."_".$ageMin."_".$ageMax;

        $res = Yii::app()->cache->get($mkey);
        
        if ( $res === false )
        {
	    	$where[] = "u.".CHelperProfile::whereLookGender();
	    	if ($ageMin<>18) 
	    		$where[] = "u.".CHelperProfile::whereAgeMin($ageMin);
	    	 if ($ageMax<>99)
	    		$where[] = "u.".CHelperProfile::whereAgeMax($ageMax);
	    	$where[] = "a.activityLast>='" . date("Y-m-d H:i:s",strtotime( Yii::app()->params['user']['isOnline'] .' seconds ago')) . "'";
	    	$where[] = "u.id=a.user_id";
        	$where = implode(" AND ", $where);
	    	
        	$limit = $pagesMax*$perPage + 1;

            $sql = "SELECT u.id FROM users as u, users_activity as a 
                    WHERE ".$where;
            //$sql .= " ORDER BY a.activityLast DESC LIMIT 1001";// AND pics<>0
            $sql .= " ORDER BY FIELD(promo, '0','1'), FIELD(role, 'gold','free'), a.activityLast DESC LIMIT 1001";// AND pics<>0
            
//FB::warn($sql);        
            
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            
            Yii::app()->cache->set($mkey, $res, 60);
        }
        
        $res = $this->_truncArrayIds($res, $pagesMax*$perPage);        
        
        $profiles = $this->_getProfiles($res, $page, $perPage);
        
        return array('ids'=>$profiles, 'count'=>count($res));
    }       
           
    /**
     * return array (IDs of users, countAll)
     * 
     */
    public function findNewMembers($page, $perPage, $pagesMax)
    {
        $look_for_gender = Yii::app()->helperProfile->whereLookGender();
        $ageMin = Yii::app()->user->settings('ageMin');
        $ageMax = Yii::app()->user->settings('ageMax');
        
        $mkey = "Profiles_findNewMembers_".$look_for_gender."_".$ageMin."_".$ageMax;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT id FROM users WHERE ".$look_for_gender;
            $sql .= " AND ".Yii::app()->params['user']['rolesWhere'];
            
            if ($ageMin<>18) $sql .= " AND ".Yii::app()->helperProfile->whereAgeMin();
            if ($ageMax<>99) $sql .= " AND ".Yii::app()->helperProfile->whereAgeMax();
            
            $sql .= " ORDER BY id DESC LIMIT 901";// AND pics<>0
            
            FB::warn($sql);        
            
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            
            Yii::app()->cache->set($mkey, $res, 60);
        }
        
        $res = $this->_truncArrayIds($res, $pagesMax*$perPage);        
        
        $profiles = $this->_getProfiles($res, $page, $perPage);
        
        return array('ids'=>$profiles, 'count'=>count($res));
    }




    /**
     * 
     */
    public function Search($attr, $page, $perPage)
    {
        if (!Yii::app()->user->id)
            return;
        
        $start=0; 
        $limit=20;
        
        $location = Yii::app()->user->data('location');
        $lat = $location['latitude'];
        $lon = $location['longitude'];
        $range_to = $attr['miles'];
        $ageMin = $attr['ageMin'];
        $ageMax = $attr['ageMax'];
        
        $userId = Yii::app()->user->id;
        
        $genderWhere = ($attr['looking_for_gender']) ? "gender='".$attr['looking_for_gender']."'" : "";
        $ageMinWhere = CHelperProfile::whereAgeMin($ageMin);
        $ageMaxWhere = CHelperProfile::whereAgeMax($ageMax);
        
        $order[] = "FIELD(role,'approved','limited')";
        $order[] = ($attr['sortby']=='N') ? "u.id DESC" : "a.activityLast DESC";
        
        //$profiles = Yii::app()->location->findNearestUsersAdv($lat,$lon, $range_to, $where, 0, 20);
        
        //changed code from http://www.micahcarrick.com/php-zip-code-range-and-distance-calculation.html
      
        // Find Max - Min Lat / Long for Radius and zero point and query
        $lat_range = $range_to/69.172;
        $lon_range = abs($range_to/(cos($lat) * 69.172));
        $min_lat = number_format($lat - $lat_range, "4", ".", "");
        $max_lat = number_format($lat + $lat_range, "4", ".", "");
        $min_lon = number_format($lon - $lon_range, "4", ".", "");
        $max_lon = number_format($lon + $lon_range, "4", ".", "");
        
        
        
        $whereReal[] = "u.promo='0'";
        $whereReal[] = "z.latitude BETWEEN '$min_lat' AND '$max_lat'";
        $whereReal[] = "z.longitude BETWEEN '$min_lon' AND '$max_lon'";
        $whereReal[] = "z.user_id=u.id";
        
        
        $wherePromo[] = "pz.user_id={$userId}";
        $wherePromo[] = "pz.promo_id=pr.user_id";
        $wherePromo[] = "pr.members='1'";
        $wherePromo[] = "pz.promo_id=u.id";
        $wherePromo[] = "pz.latitude BETWEEN '$min_lat' AND '$max_lat'";
        $wherePromo[] = "pz.longitude BETWEEN '$min_lon' AND '$max_lon'";
        $wherePromo[] = "u.promo='1'";
        
        
        $where[] = "a.user_id=u.id";
        $where[] = "u.".Yii::app()->params['user']['rolesWhere'];
        if ($genderWhere) $where[] = "u.{$genderWhere}";
        $where[] = "u.{$ageMinWhere}";
        $where[] = "u.{$ageMaxWhere}";
        if ($userId)
            $where[] = "u.id<>{$userId}";
        
        
        $sqlList  = "SELECT DISTINCT(u.id)";
        $sqlCount = "SELECT COUNT(DISTINCT(u.id))";

        $sql = " FROM users_location as z, users as u, users_activity as a, ";
        $sql.= " promo_locations as pz, promo_roles as pr ";
        $sql.= " WHERE ";
        $sql.= " ( (" . implode(' AND ', $whereReal) . ") OR (" . implode(' AND ', $wherePromo) . ") )";
        $sql.= " AND ";
        $sql.= implode(' AND ', $where);
        
        $sqlCount .= $sql." LIMIT 1";
//FB::error($sqlCount, 'search SQL COUNT');
        $profiles['count'] = Yii::app()->db->createCommand($sqlCount)->queryScalar();
        
        $sql .= " ORDER BY ".implode(', ', $order);
        $sql .= " LIMIT ".($page * $perPage).", {$perPage}";


        
/*        $sql = "SELECT u.id FROM users_location as z, users as u, users_activity as a WHERE ";
        $sql .= implode(' AND ', $where);
        $sql .= " ORDER BY {$order}";
        $sql .= " LIMIT 20";*/
FB::info($sqlList.$sql, 'search SQL LIST');
        
        
/*$sql = "SELECT *, id as user_id FROM users ORDER BY id DESC LIMIT 20";
FB::info($sql,'------------------------------------------------------');
return Yii::app()->db->createCommand($sql)->queryAll();*/
        
        $profiles['list'] = Yii::app()->db->createCommand($sqlList.$sql)->queryColumn();
FB::info($profiles, 'search RES');
       
        
        return $profiles;
    }





    public function promosGetNearFront($count)
    {
        $mkey = "promosGetNearFront_".$count;        
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "
                SELECT id FROM users as u, promo_roles as f
                WHERE 
                    u.promo='1' 
                    AND 
                    u.id=f.user_id
                    AND
                    f.front='1'
                    AND
                    u.role='approved'   
                ORDER BY RAND() LIMIT {$count}
            ";  //u.".Yii::app()->params['user']['rolesWhere']."
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            
            Yii::app()->cache->set($mkey, $res, 60);            
        }

        return $res;
    }        

    
    
    
    
    
    
    
    
    
	static public function countOnlineNowAll()
    {
    	$mkey = "Profiles_countOnlineNowAll";

        $res = Yii::app()->cache->get($mkey);
        
//        if ( $res === false )
        {
	    	$where[] = "activityLast>='" . date("Y-m-d H:i:s",strtotime( Yii::app()->params['user']['isOnline'] .' seconds ago')) . "'";
        	$where = implode(" AND ", $where);
	    	
            $sql = "SELECT COUNT(user_id) FROM users_activity 
                    WHERE ".$where;
            $sql .= " LIMIT 1";
FB::warn($sql);            
          	$res = Yii::app()->db->createCommand($sql)->queryScalar();
            
            Yii::app()->cache->set($mkey, $res, 60);
        }
        return $res;
    }     
    

}