<?php



class Admins
{
    /**
     */
    public function getId()
    {    
        $mkey = "Admins_id";
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT id FROM users WHERE role='administrator' ORDER BY id DESC LIMIT 1";
            $res = Yii::app()->db->createCommand($sql)->queryScalar();
            
            Yii::app()->cache->set($mkey, $res);
        }
        return $res;
    }
        
    
}