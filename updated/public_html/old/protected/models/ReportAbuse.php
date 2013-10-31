<?php

class ReportAbuse
{
    /**
     */
    static function newReport($id_from, $id_to)
    {
        if (!$id_from || !$id_to)
        {
            return false;
        }
        
        $reported = ReportAbuse::getReportFromTo($id_from, $id_to);        
        if (!$reported)
        {
            $sql = "INSERT INTO admin_report_abuse (id_from, id_to, added) VALUES (:id_from, :id_to, NOW())";
            Yii::app()->db->createCommand($sql)
                ->bindParam('id_from', $id_from, PDO::PARAM_INT)
                ->bindParam('id_to', $id_to, PDO::PARAM_INT)
                ->execute();
    
            $mkey = "ReportFromTo_".$id_from.'_'.$id_to;
            Yii::app()->cache->delete($mkey);            
        }
    }
    


    /**
     */
    static function getReportFromTo($id_from, $id_to)
    {
        if (!$id_from || !$id_to)
        {
            return false;
        }
        
        $mkey = "ReportFromTo_".$id_from.'_'.$id_to;
        if ( !$res = Yii::app()->cache->get($mkey) )
        {
            $sql = "SELECT COUNT(id) FROM admin_report_abuse WHERE id_from={$id_from} AND id_to={$id_to} LIMIT 1";
            $res = Yii::app()->db->createCommand($sql)->queryScalar();
            
            Yii::app()->cache->set($mkey, $res);            
        }
        
        return $res;
    }

        
}