<?php



class Winks
{
    /**
     */
    public function sendWink($id_to, $id_from)
    {
        $id_to = intval($id_to);
        $id_from = intval($id_from);
        
        if (!$id_to || !$id_from)
            return 0;            
        
        $sql = "
            INSERT INTO profile_winks (
                id_to, 
                id_from,
                added
            ) VALUES (
                :id_to, 
                :id_from,
                NOW() 
            )";
            
        Yii::app()->db->createCommand($sql)
                ->bindParam(":id_to", $id_to, PDO::PARAM_INT)
                ->bindParam(":id_from", $id_from, PDO::PARAM_INT)
                ->execute();
        
        
        $mkey = "Winks_".$id_to."_".$id_from;
        Yii::app()->cache->delete($mkey);
        
        return Yii::app()->db->lastInsertId;            
    }

    /**
     * return array ...
     * 
     */
    public function getWinksFromTo($id_to, $id_from)
    {
        $mkey = "Winks_".$id_to."_".$id_from;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT COUNT(id) FROM profile_winks WHERE id_from={$id_from} AND id_to={$id_to} LIMIT 1";
            $res = Yii::app()->db->createCommand($sql)->queryScalar();
            Yii::app()->cache->set($mkey, $res, Yii::app()->params['cache']['profile']);            
        }   
        return $res;     
    }
    
    
    
}