<?php

class CHelperCron extends CApplicationComponent
{
    
    public function onStartCron($command, $action)
    {
        $sql = "INSERT INTO log_cron (command,action,timeStart) VALUES ('{$command}','{$action}',NOW())";
        Yii::app()->db->createCommand($sql)->execute();
        
        return Yii::app()->db->lastInsertId;                
    }
    
    public function onEndCron($id, $result)
    {
        if (is_array($result))
        {
            $result = serialize($result);
        }
        
        $sql = "UPDATE log_cron SET timeEnd=NOW(), result=:result WHERE id={$id}";
        Yii::app()->db->createCommand($sql)
            ->bindValue(":result", $result, PDO::PARAM_STR)
            ->execute();
    }    
}
