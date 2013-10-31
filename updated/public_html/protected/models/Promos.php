<?php

class Promos
{
    public $minLimit = 100;//>=9!!!
    public $maxLimit = 101;
    
    
    public function mergeIds($a1, $a2)
    {
        $res = array();
        
        if (!empty($a1))
            foreach($a1 as $v)
                $res[] = $v;
                
        if (!empty($a2))
            foreach($a2 as $v)
                $res[] = $v;
        
        return $res;
    }
    

    /**
     * promos for GUEST storing to SESSION
     */
    public function createPromosLocationsForGuest($guestData)
    {
return;    	
        //for storing in database - больше количество промосов - больше рандомли на первую страницу )
/*        $countPromos = Yii::app()->db->createCommand("SELECT COUNT(*) FROM promo_roles")->queryScalar();
        $limit = ceil($countPromos/3);
        if ($limit<$this->minLimit) $limit = $this->minLimit;
        if ($limit>$this->maxLimit) $limit = $this->maxLimit;
*/
        $limit = 100;
//FB::warn($guestData['promos']);         
        /*if ($guestData['promos'])
        {
            return $guestData['promos'];
        }*/
        
        //find in database using country_state_city
        $country_state_city = $guestData['location']['country'].'_'.$guestData['location']['state'].'_'.$guestData['location']['city'];
        
        $mkey = "PromosLocationsForGuest_".$country_state_city;
        $res = Yii::app()->cache->get( $mkey );
//FB::warn($res);
        if ( !$res )
        {
            //get existed ALL for this location
            $sql = "SELECT promo_id FROM promo_locations_guest WHERE country_state_city_hash='".MD5($country_state_city)."'"; 
            $promosExistedALL = Yii::app()->db->createCommand($sql)->queryColumn();
//FB::error($promosExistedALL);
            
            
            
            $sql = "SELECT promo_id,location_id FROM promo_locations_guest WHERE country_state_city_hash='".MD5($country_state_city)."' LIMIT {$limit}"; 
            $dbres = Yii::app()->db->createCommand($sql)->queryAll();
            if ($dbres)
            {
                foreach ($dbres as $r)
                {
                    $promo = Yii::app()->location->getLocation($r['location_id']);
                    $promo['id'] = $r['promo_id'];
                    $promo['location_id'] = $r['location_id'];
                    
                    $res[] = $promo;
                    
                    //$promosExistedALL[] = $r['promo_id'];
                }
            }
    
            $lat = $guestData['location']['latitude'];
            $lon = $guestData['location']['longitude'];
            
            //find in forced location (in full) 
            if (count($res)<$limit)//факе мог быть вытерт
            {
                $promosForcedFull = Yii::app()->location->findPromosForcedFullForFront($lat,$lon,0.1/*500*/,5*$limit);
                if ($promosForcedFull)
                    foreach($promosForcedFull as $p)
                        if ( empty($promosExistedALL) || !in_array($p['user_id'], $promosExistedALL) )
                        {
                            $location_id = Yii::app()->location->findNearestID($p['force_lat'], $p['force_lon']);
                            $promo = Yii::app()->location->getLocation($location_id);
                            $promo['location_id'] = $location_id;
                            $promo['id'] = $p['user_id'];
                            
                            $res[] = $promo;
                            $promosExistedALL[] = $p['user_id'];
                            
                            
                            $sql = "INSERT INTO promo_locations_guest (country_state_city_hash, country_state_city_info, promo_id, location_id) VALUES (:country_state_city_hash, :country_state_city_info, :promo_id, :location_id)";
                            Yii::app()->db->createCommand($sql)
                                ->bindParam('country_state_city_hash', MD5($country_state_city), PDO::PARAM_STR)
                                ->bindParam('country_state_city_info', $country_state_city, PDO::PARAM_STR)
                                ->bindParam('promo_id', $p['user_id'], PDO::PARAM_INT)
                                ->bindParam('location_id', $location_id, PDO::PARAM_INT)
                                ->execute();
                        }                   
            }
    
    
            //find in forced location (in country_only)        
            $location_id = Yii::app()->location->findLocationIdByIP();        
            $locationGuest = Yii::app()->location->getLocation( $location_id );
            $country = $locationGuest['country'];
            
            if ($country && count($res)<$limit)
            {
                $promosForcedInCountry = Yii::app()->db->createCommand("SELECT COUNT(user_id) FROM promo_roles WHERE force_country='{$country}' LIMIT 1")->queryScalar();
                if ($promosForcedInCountry)
                {
                    $locations = Yii::app()->location->findLocationForPromos($lat,$lon,0.1/*500*/,$limit, $country);
    //FB::error($locations);                          
                    $sql = "SELECT id FROM users as u, promo_roles as r WHERE ";
                    $sql.= " r.force_country='{$country}'";
                    $sql.= " AND r.front='1' AND r.user_id = u.id"; 
                    $sql.= " AND u.role='approved'";//.Yii::app()->params['user']['rolesWhere'];
                    if ($promosExistedALL)
                        $sql .= " AND r.user_id NOT IN (".implode(',',$promosExistedALL).")";
                    $sql.= " ORDER BY RAND()";
                    $sql .= " LIMIT ".($limit - count($res));
            
                    $promoIds = Yii::app()->db->createCommand($sql)->queryColumn();
    //FB::error($promoIds);                
                    if ($promoIds && $locations)
                        foreach ($promoIds as $id)
                        {
                            $indxRand = rand(0, (count($locations)-1));
                                    
                            //$location = $locations[$indxRand];
                            $location_id = $locations[$indxRand]['id'];
                            $promo = Yii::app()->location->getLocation($location_id);
                            $promo['location_id'] = $location_id;
                            $promo['id'] = $id;
                            
                            $res[] = $promo;
                            $promosExistedALL[] = $id;
                            
                            $sql = "INSERT INTO promo_locations_guest (country_state_city_hash, country_state_city_info, promo_id, location_id) VALUES (:country_state_city_hash, :country_state_city_info, :promo_id, :location_id)";
                            Yii::app()->db->createCommand($sql)
                                ->bindParam('country_state_city_hash', MD5($country_state_city),PDO::PARAM_STR)
                                ->bindParam('country_state_city_info', $country_state_city,PDO::PARAM_STR)
                                ->bindParam('promo_id', $id,PDO::PARAM_INT)
                                ->bindParam('location_id', $location_id,PDO::PARAM_INT)
                                ->execute();
                        }                   
                }
             
            }
    /**/
            //без форсанyтых локатион
            if (count($res)<$limit)//факе мог быть вытерт
            {
                $locations = Yii::app()->location->findLocationForPromos($lat,$lon,0.1/*500*/,$limit);
                          
                $sql = "SELECT id FROM users as u, promo_roles as r WHERE r.front='1'";
                $sql.= " AND r.force_country='' AND r.force_lat=0.0 AND r.force_lon=0.0";
                $sql.= " AND r.user_id=u.id"; 
                $sql.= " AND u.role='approved'";//.Yii::app()->params['user']['rolesWhere'];
                if ($promosExistedALL)
                    $sql .= " AND r.user_id NOT IN (".implode(',',$promosExistedALL).")";
                $sql.= " ORDER BY RAND()";
                $sql .= " LIMIT ".($limit - count($res));
        
                $promoIds = Yii::app()->db->createCommand($sql)->queryColumn();
                
                if ($promoIds && $locations)
                    foreach ($promoIds as $id)
                    {
                        $indxRand = rand(0, (count($locations)-1));
                                
                        //$location = $locations[$indxRand];
                        $location_id = $locations[$indxRand]['id'];
                        $promo = Yii::app()->location->getLocation($location_id);
                        $promo['location_id'] = $location_id;
                        $promo['id'] = $id;
                        
                        $res[] = $promo;
                        //$promosExistedALL[] = $id;
                        
                        $sql = "INSERT INTO promo_locations_guest (country_state_city_hash, country_state_city_info, promo_id, location_id) VALUES (:country_state_city_hash, :country_state_city_info, :promo_id, :location_id)";
                        Yii::app()->db->createCommand($sql)
                            ->bindParam('country_state_city_hash', MD5($country_state_city),PDO::PARAM_STR)
                            ->bindParam('country_state_city_info', $country_state_city,PDO::PARAM_STR)
                            ->bindParam('promo_id', $id,PDO::PARAM_INT)
                            ->bindParam('location_id', $location_id,PDO::PARAM_INT)
                            ->execute();
                    }            
            }
            
            Yii::app()->cache->set($mkey, $res, 10);            
        }
        
        return $res;
    }

    /**
     * after registration from session data
     */
    public function createPromosLocationsForUserFromSession($userId, $promos)
    {
        /*$profile = new Profile($userId);
        if ($profile->getDataValue('promo'))
            return;
        
        if ($userId && $promos)
        {
            foreach ($promos as $k=>$f)
            {
                $sql = "
                            INSERT INTO promo_locations (
                                user_id, 
                                promo_id, 
                                latitude, 
                                longitude, 
                                location_id
                            ) VALUES (
                                {$userId},
                                {$f[id]},
                                {$f[latitude]},
                                {$f[longitude]},
                                {$f[location_id]}
                            )";                        
                Yii::app()->db->createCommand($sql)->execute();
                
                if ($k>=($this->minLimit-1)) 
                    break;                
            }             
        }*/
    }
    
    
    public function createPromosLocationsForUser($userId)
    {
        $profile = new Profile($userId);

        if ( (!FACEBOOK && !Yii::app()->user->checkAccess('limited')) || $profile->getDataValue('promo') )
            return;
        
        $lat = $profile->getLocationValue('latitude');
        $lon = $profile->getLocationValue('longitude');
       
        $mkey = "createPromosLocationsForUser_".$userId.'_'.$lat.'_'.$lon;
        if ( !Yii::app()->cache->get($mkey) )
        {
            $limit = rand($this->minLimit, $this->maxLimit);
            
            //юзеру уже присвоены?
            $promosExisted = Yii::app()->location->findLocationPromosExisted($lat,$lon,0.1/*500*/,$userId);
            $promosExistedALL = self::getPromosIdsForUser($userId); // ALL locations, not only current!!!
//FB::error($promosExistedALL, '0000000000');
            
            //find in forced location (in full) 
            if (count($promosExisted) < $this->minLimit)
            {
                $promosExistedALL = self::mergeIds($promosExisted, $promosExistedALL); 
                
                $promosForcedFull = Yii::app()->location->findPromosForcedFullForMembers($userId, $promosExistedALL,$lat,$lon,0.1/*500*/,$limit);
//FB::error($promosForcedFull, '1111111111');
                if ($promosForcedFull)
                    foreach($promosForcedFull as $p)
                    {
                        $promosExisted[] = $p['user_id'];
                        
                        $p['location_id'] = Yii::app()->location->findNearestID($p['force_lat'], $p['force_lon']);
                        $sql = "
                            INSERT INTO promo_locations (
                                user_id, 
                                promo_id, 
                                latitude, 
                                longitude, 
                                location_id
                            ) VALUES (
                                {$userId},
                                {$p[user_id]},
                                {$p[force_lat]},
                                {$p[force_lon]},
                                {$p[location_id]}
                            )";
//FB::error($sql,'promocreate');                        
                        Yii::app()->db->createCommand($sql)->execute();                        
                    }
            }                        

            //find in forced location (in country)
            $country = Yii::app()->user->location('country'); 
            if ($country && count($promosExisted)<$this->minLimit)
            {
                $promosForcedInCountry = Yii::app()->db->createCommand("SELECT COUNT(user_id) FROM promo_roles WHERE force_country='{$country}' LIMIT 1")->queryScalar();
                if ($promosForcedInCountry)
                {
                    $locations = Yii::app()->location->findLocationForPromos($lat,$lon,0.1/*500*/,$limit, $country);
    //FB::error($locations);                          
                    if ($locations)
                    {
                        $where[] = "r.force_country='{$country}'";
                        $where[] = "r.members='1'";
                        $where[] = "r.user_id = u.id";
                        $where[] = "u.role='approved'";//.Yii::app()->params['user']['rolesWhere'];
                        $promosExistedALL = self::mergeIds($promosExisted, $promosExistedALL);
                        if ($promosExistedALL)
                            $where[] = "r.user_id NOT IN (".implode(',',$promosExistedALL).")";
//FB::error($promosExisted, '0000000001');
                        $sql = "SELECT u.id FROM users as u, promo_roles as r ";
                        $sql.= " WHERE ".implode(' AND ', $where);
                        $sql.= " ORDER BY RAND()";
                        $sql.= " LIMIT ".($limit - count($promosExisted));
    
                        $promoIds = Yii::app()->db->createCommand($sql)->queryColumn();
        //FB::error($promoIds);                
                        if ($promoIds)
                            foreach ($promoIds as $id)
                            {
                                $promosExisted[] = $id;
                                
                                $indxRand = rand(0, (count($locations)-1));
                                
                                $location = $locations[$indxRand];
                                
                                $sql = "
                                    INSERT INTO promo_locations (
                                        user_id, 
                                        promo_id, 
                                        latitude, 
                                        longitude, 
                                        location_id
                                    ) VALUES (
                                        {$userId},
                                        {$id},
                                        {$location[latitude]},
                                        {$location[longitude]},
                                        {$location[id]}
                                    )";
                                
                                Yii::app()->db->createCommand($sql)->execute();
                            }
                    }
                  
                }
            } 


            
            
            if (count($promosExisted) < $this->minLimit)
            {
                $locations = Yii::app()->location->findLocationForPromos($lat,$lon,0.1/*500*/,$limit);
                
                if ($locations)
                {
                    $sql = "SELECT id FROM users as u, promo_roles as r WHERE u.promo='1'";                
                    $sql.= " AND r.members='1'";
                    $sql.= " AND r.force_country='' AND r.force_lat=0.0 AND r.force_lon=0.0";
                    $sql.= " AND u.id=r.user_id";
                    $sql.= " AND u.role='approved'";//.Yii::app()->params['user']['rolesWhere'];                    
                    ///$genderWhere = Yii::app()->helperProfile->whereLookGender($userId);
                    ///$sql.= " AND u.{$genderWhere}";
                    $promosExistedALL = self::mergeIds($promosExisted, $promosExistedALL);                
                    if ($promosExistedALL)
                        $sql .= " AND u.id NOT IN (".implode(',',$promosExistedALL).")";
//FB::error($promosExisted, '0000000002');
                    $sql .= " LIMIT ".($limit - count($promosExisted));
//FB::warn($sql);
                    $promoIds = Yii::app()->db->createCommand($sql)->queryColumn();
                    if ($promoIds && $locations)
                        foreach ($promoIds as $id)
                        {
                            $indxRand = rand(0, (count($locations)-1));
                            
                            $location = $locations[$indxRand];
                            
                            $sql = "
                                INSERT INTO promo_locations (
                                    user_id, 
                                    promo_id, 
                                    latitude, 
                                    longitude, 
                                    location_id
                                ) VALUES (
                                    {$userId},
                                    {$id},
                                    {$location[latitude]},
                                    {$location[longitude]},
                                    {$location[id]}
                                )";
                            
                            Yii::app()->db->createCommand($sql)->execute();
                        }
                }
            }
            
            Yii::app()->cache->set($mkey, 1, Yii::app()->params['cache']['profile']); 
            
            //$mkey = "PromosIdsForUser_".$userId;
            //Yii::app()->cache->delete($mkey);  
            
        }
    }






    
    /**
     * !!! ALL promos ids 
     */
    public function getPromosIdsForUser($userId)
    {
        if ($userId)
        {
            $sql = "SELECT promo_id FROM promo_locations WHERE user_id={$userId}";
                    
            $res = Yii::app()->db->createCommand($sql)->queryColumn();
        }
        return $res;        
    }
    
    /**
     * 
     */
    public function getPromoForcedLocation($promoId)
    {
        $sql = "SELECT * FROM promo_roles WHERE user_id=".$promoId." LIMIT 1";
        if ($role = Yii::app()->db->createCommand($sql)->queryRow())
        {
            $role['force_location'] = 'no';
            $role['location'] = array();
            if ($role['force_country'])
            {
                $role['force_location'] = 'country_only';
            }
            elseif ($role['force_lat']!=0.0 && $role['force_lon']!=0.0)
            {
                $role['force_location'] = 'full';
                $role['location_id'] = Yii::app()->location->findNearestID($role['force_lat'], $role['force_lon']);
                $role['location'] = Yii::app()->location->getLocation($role['location_id']);
            }
//FB::warn($role, $promoId);
        }
        
        return $role;
    }
    
}