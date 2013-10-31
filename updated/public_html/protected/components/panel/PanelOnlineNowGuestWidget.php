<?php
class PanelOnlineNowGuestWidget extends CWidget
{
    public function init()
    {
        $sql = "
                SELECT u.id FROM users as u, users_activity as a 
                WHERE 
                a.activityLast>='".date("Y-m-d H:i:s",strtotime( Yii::app()->params['user']['isOnline'] .' seconds ago'))."'
                AND
                u.".Yii::app()->helperProfile->whereLookGender()."
                AND
                u.id=a.user_id
                ORDER BY a.activityLast DESC
                LIMIT 9
            ";
       
        $profiles = Yii::app()->db->createCommand($sql)->queryAll();
                
        if (count($profiles)<9)
        {
            $fillStart = (count($profiles)) ? count($profiles)-1 : 0;
            $fillCount = (count($profiles)) ? 9-count($profiles)+1 : 9;
            $profiles += array_fill($fillStart, $fillCount, null);
        }                
                
        $this->render('PanelOnlineNowGuest', array('profiles'=>$profiles));
    }
}
?>
