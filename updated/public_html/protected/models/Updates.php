<?php

class Updates
{
    /**
     * CODS:
     * 1 - dashboard message
     * 2 - Became a new member! 
     * 3 - Has [uploaded] a new video!
     * 4 - Changed her/his profile picture! 
     * 
     */
    static function getUpdateKodName($kod, $text, $gender='')
    {
        switch ($kod)
        {
            case 2: return 'Became a new member!';
            case 3: return 'Has [uploaded] a new video!';
            case 4: $herhis = ($gender=='F') ? 'her' : 'his'; 
                    return 'Changed '.$herhis.' profile picture!';
        }
        return $text;
    }     

    /**
     */
    static function addUpdate($kod, $user_id, $text='')
    {
return;
        if (!$user_id)
        {
            return 0;//$user_id = Yii::app()->user->id;
        }
        
        
        $sql = "
            INSERT INTO profile_updates (
                kod, 
                user_id,
                added, 
                text
            ) VALUES (
                {$kod}, 
                {$user_id},
                NOW(), 
                :text
            )";           
            
        Yii::app()->db->createCommand($sql)
                ->bindParam(":text", $text, PDO::PARAM_STR)
                ->execute();
        
        $mkey = "Profiles_Updates_".$user_id;
        Yii::app()->cache->delete($mkey);
        
        return Yii::app()->db->lastInsertId;
    }     


    /**
     * return array (IDs of users, countAll)
     * 
     */
    public function getUpdates($limit=25)
    {
        $userId = Yii::app()->user->id;

        $look_for_gender = Yii::app()->helperProfile->whereLookGender();
        $ageMin = Yii::app()->user->settings('ageMin');
        $ageMax = Yii::app()->user->settings('ageMax');
        
        //$mkey = "Profiles_Updates_".$look_for_gender."_".$ageMin."_".$ageMax;
        $mkey = "Profiles_Updates_".$userId;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $lat = Yii::app()->user->location('latitude');
            $lon = Yii::app()->user->location('longitude');
            $users = Yii::app()->location->findNearestUsers($lat, $lon, 500, '', 1000);

            if ($users) 
            {
                foreach ($users as $u)
                {
                    $ids[] = $u['id'];
                }
            }
            
            if ($ids)  
            {
                $sql = "SELECT u.id, up.kod, up.added, up.text FROM profile_updates as up, users as u WHERE ";
                $sql .= " u.id IN (".implode(',',$ids).")";
                $sql .= " AND u.".$look_for_gender;
                $sql .= " AND u.".Yii::app()->params['user']['rolesWhere'];
                
                if ($ageMin<>18) $sql .= " AND u.".Yii::app()->helperProfile->whereAgeMin();
                if ($ageMax<>99) $sql .= " AND u.".Yii::app()->helperProfile->whereAgeMax();
                
                $sql .= " AND up.user_id=u.id";
                
                $sql .= " AND u.id<>{$userId}";
                
                $sql .= " ORDER BY up.id DESC LIMIT {$limit}";// AND pics<>0
                
                FB::warn($sql,'getUpdates()');        
                
                $res = Yii::app()->db->createCommand($sql)->queryAll();
                
                Yii::app()->cache->set($mkey, $res, 60);                
                
            }
            
            

        }
        
        return $res;
        
/*        $look_for_gender = Yii::app()->helperProfile->whereLookGender();
        $ageMin = Yii::app()->user->settings('ageMin');
        $ageMax = Yii::app()->user->settings('ageMax');
        
        $mkey = "Profiles_Updates_".$look_for_gender."_".$ageMin."_".$ageMax;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT u.id, up.kod, up.added, up.text FROM profile_updates as up, users as u WHERE u.".$look_for_gender;
            $sql .= " AND u.".Yii::app()->params['user']['rolesWhere'];
            
            if ($ageMin<>18) $sql .= " AND u.".Yii::app()->helperProfile->whereAgeMin();
            if ($ageMax<>99) $sql .= " AND u.".Yii::app()->helperProfile->whereAgeMax();
            
            $sql .= " AND up.user_id=u.id";
            
            $sql .= " ORDER BY up.id DESC LIMIT 25";// AND pics<>0
            
            FB::warn($sql);        
            
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            
            Yii::app()->cache->set($mkey, $res, 60);
        }
        
        $res = Profiles::_truncArrayIds($res, 25);      //$pagesMax*$perPage  
    
        //$profiles = $this->_getProfiles($res, $page, $perPage, false);
        return $res; //return array('ids'=>$profiles, 'count'=>count($res));
        */
    } 


    /**
     * return 
     * 
     */
    public function getUserUpdates($userId)
    {
        $mkey = "Profiles_Updates_".$userId;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT id,kod,user_id,added,text FROM profile_updates WHERE user_id=".$userId;
            $sql .= " ORDER BY id DESC LIMIT 25";
            
            $res = Yii::app()->db->createCommand($sql)->queryAll();
            
            Yii::app()->cache->set($mkey, $res, Yii::app()->params['cache']['profile']);
        }
        return $res;
    } 
    
    
    /**
     * return array ...
     * 
     */
    public function deleteUpdate($id, $user_id)
    {
        $id = intval($id);
        
        //check: current user is owner?
        $sql = "SELECT user_id FROM profile_updates WHERE id={$id} LIMIT 1";
        if ($user_id == Yii::app()->db->createCommand($sql)->queryScalar())
        {
            $sql = "DELETE FROM profile_updates WHERE id=".$id;
            Yii::app()->db->createCommand($sql)->execute();
            
            $mkey = "Profiles_Updates_".$user_id;
            Yii::app()->cache->delete($mkey);            
        }
    }    
        
}