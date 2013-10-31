<?php



class Messages
{
    /**
     */
    public function addWelcome($id_to)
    {    
        // нет админов Messages::addPrivateMessage($id_to, Admins::getId(), '', true);        
        Messages::addPrivateMessage($id_to, 0/*Admins::getId()*/, '', '', false, true);
    }
    
    /**
     */
    public function addPrivateMessage($id_to, $id_from/*0 - admin!!!*/, $subject='', $text='', $video=false, $welcome=false)
    {

        if (!$id_to)
            return 0;            
        
        /*if (!$id_from)
            $id_from = Yii::app()->user->id;*/
        
        $video = ($video) ? '1' : '0';
        $welcome = ($welcome) ? '1' : '0';
        
        $sql = "
            INSERT INTO profile_messages (
                id_to, 
                id_from,
                video,
                added,
                subject, 
                text,
                welcome
            ) VALUES (
                :id_to, 
                :id_from,
                '{$video}',
                NOW(), 
                :subject,
                :text,
                '{$welcome}'
            )";
            
        Yii::app()->db->createCommand($sql)
                ->bindParam(":id_to", $id_to, PDO::PARAM_INT)
                ->bindParam(":id_from", $id_from, PDO::PARAM_INT)
                ->bindParam(":subject", $subject, PDO::PARAM_STR)
                ->bindParam(":text", $text, PDO::PARAM_STR)
                ->execute();


        //unhide box about new message
        $profile = new Profile($id_to);
        $profile->settingsUpdate('hided_new_message', '0');


        //clear cache
        $mkey = "PrivateMessages_".$id_to."_".$id_from;
        Yii::app()->cache->delete($mkey);
        $mkey = "PrivateMessages_".$id_to;
        Yii::app()->cache->delete($mkey);
        
        
        
        return Yii::app()->db->lastInsertId;            
    }


    /**
     * video message
     */
	public function addPrivateMessageVideo($idTo, $streamname)
	{
        $userId = Yii::app()->user->id;
        
        if ( $userId && $streamname )
        {
            $videoRes = Video::addVideoMessageFromStream($userId, $streamname);

            if ($videoRes)
                $messId = Messages::addPrivateMessage($idTo, $userId, '', '', true);
            
            if ($messId)
                Video::addVideoMessageFromStreamStep2($messId, $userId, $streamname);
            
            if ($messId)
            {
                //send email
                Yii::app()->mail->SendHtmlMail($idTo, '', 'messageNew', array('id_from'=>$userId));
            }
        }
        
        return $messId;
	}

    /**
     * return array ...
     * 
     */
    public function getPrivateMessages($id_to, $id_from, $page=0, $perPage=10)
    {
        $where[] = "id_to IN (".$id_to.",".$id_from.")";
        $where[] = "id_from IN (".$id_from.",".$id_to.")";

        $sql = "SELECT COUNT(id) FROM profile_messages WHERE ".implode(" AND ", $where);
        
        $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();
        
        
        $sql = "SELECT * FROM profile_messages WHERE ".implode(" AND ", $where);
        $sql.= " ORDER BY id DESC";
        $sql.= " LIMIT ".($page * $perPage).", " . $perPage;
                    
        $res['list'] = Yii::app()->db->createCommand($sql)->queryAll();

        return $res;
    }
    /**
     * return array(messages, countNew) ...
     * 
     */
    public function getPrivateMessagesTo($id_to, $showNewOnly=false, $page=0, $perPage=15)
    {
        //$mkey = "PrivateMessages_".$id_to."_".$showHided."_".$showNewOnly;
        //if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $start = $page*$perPage;
            
            
            $sql = "SELECT COUNT(id) FROM profile_messages WHERE id_to=".$id_to;
            $sql .= " AND hided_to='0' ";
            $sql .= " LIMIT 1";
            $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();
            
            
            $sql = "SELECT COUNT(id) FROM profile_messages WHERE id_to=".$id_to;
            $sql .= " AND hided_to='0' "; 
            /*if ($showNewOnly) */$sql .= " AND readed='0' ";                           
            $sql .= " LIMIT 1";
            $res['newCount'] = Yii::app()->db->createCommand($sql)->queryScalar();


            $sql = "SELECT id,id_from,id_to,added,subject,text,video,welcome,readed FROM profile_messages WHERE id_to=".$id_to;            
            $sql .= " AND hided_to='0' "; 
            if ($showNewOnly) $sql .= " AND readed='0' ";
            $sql .= " ORDER BY id DESC LIMIT {$start}, {$perPage}";            
            $res['messages'] = Yii::app()->db->createCommand($sql)->queryAll();
           
            
//            Yii::app()->cache->set($mkey, $res, Yii::app()->params['cache']['profile']);
        }
        return $res;
    }

    /**
     * return messages...
     * 
     */
    public function getPrivateMessagesFrom($id_from, $page=0, $perPage=15)
    {
        //$mkey = "PrivateMessagesSent_".$id_from;
        //if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $start = $page*$perPage;

            $sql = "SELECT COUNT(id) FROM profile_messages WHERE id_from=".$id_from;
            $sql .= " AND hided_from='0' ";
            $sql .= " LIMIT 1";
            $res['count'] = Yii::app()->db->createCommand($sql)->queryScalar();
            
            $sql = "SELECT id,id_from,id_to,added,subject,text,video,welcome FROM profile_messages WHERE id_from=".$id_from;
            $sql .= " AND hided_from='0' ";            
            $sql .= " ORDER BY id DESC LIMIT {$start}, {$perPage}";            
            $res['messages'] = Yii::app()->db->createCommand($sql)->queryAll();
            
            //Yii::app()->cache->set($mkey, $res, Yii::app()->params['cache']['profile']);
        }
        return $res;
    }
    
    /**
     * return array ...
     * 
     */
    public function hidePrivateMessage($id, $user_id)
    {
        $id = intval($id);
        
        //check: current user is owner?
        $sql = "SELECT id_to, id_from FROM profile_messages WHERE id=".$id." LIMIT 1";
        if ($res = Yii::app()->db->createCommand($sql)->queryRow())
        {
            if ( $res['id_to'] == $user_id)
            {
                $sql = "UPDATE profile_messages SET hided_to='1' WHERE id=".$id;

                Yii::app()->db->createCommand($sql)->execute();
                //$mkey = "PrivateMessages_".$id_to;
                //Yii::app()->cache->delete($mkey); 
                
                $res = Messages::getPrivateMessagesTo($user_id, true);
                return $res['newCount'];                
                           
            }
            if ( $res['id_from'] == $user_id)
            {
                $sql = "UPDATE profile_messages SET hided_from='1' WHERE id=".$id;
                Yii::app()->db->createCommand($sql)->execute();
                //$mkey = "PrivateMessages_".$id_from;
                //Yii::app()->cache->delete($mkey);
            }            
        }
        
        return 0;

    }    
    
    /**
     * return array ...
     * 
     */
    public function markAsReadPrivateMessage($id, $id_to)
    {
        $id = intval($id);
        
        //check: current user is owner?
        $sql = "SELECT id_to FROM profile_messages WHERE id=".$id." LIMIT 1";
        if ($id_to == Yii::app()->db->createCommand($sql)->queryScalar())
        {
            $sql = "UPDATE profile_messages SET readed='1' WHERE id=".$id;
            Yii::app()->db->createCommand($sql)->execute();
            
            $res = Messages::getPrivateMessagesTo($id_to, true);
            return $res['newCount'];
            
            //$mkey = "PrivateMessages_".$id_to;
            //Yii::app()->cache->delete($mkey);            
        }
    }
    
    /**
     * return array ...
     * 
     */
    public function markAsReaded($ids)
    {
        if (is_array($ids) && !empty($ids))
        {
            $sql = "UPDATE profile_messages SET readed='1' WHERE id IN (".implode(',',$ids).")";
            Yii::app()->db->createCommand($sql)->execute();
        }
    }    
    
    
    
    
    
    
   
}