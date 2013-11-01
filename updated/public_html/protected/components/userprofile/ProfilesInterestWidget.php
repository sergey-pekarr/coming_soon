<?php
class ProfilesInterestWidget extends CWidget
{
    public $countNeed=4;
    public $ajax=0;
    public $excludeIdForce = 0;
    
    public function init()
    {
        //if (Yii::app()->user->id)
        {
            //if ($this->ajax) 
            {
                $model = new Profiles;
                
                $profiles = $model->Near( 1000/*$this->countNeed*/ );
                if ($profiles)
                {
                    $viewedIds = Yii::app()->user->Profile->viewedToGet();
//FB::warn($viewedIds, '$viewedIds$viewedIds$viewedIds'); 
                    if ($viewedIds)
                    {
                        foreach ($profiles as $p)
                            if ( !in_array($p['id'], $viewedIds) )
                                $profilesTMP[] = $p;                        
                    }
                    
                    if (count($profilesTMP)>=$this->countNeed)
                    {
                        $profiles = $profilesTMP;
                    }
                    
                    if ($this->excludeIdForce)
                    {
                        foreach ($profiles as $k=>$p)
                            if ($p['id']==$this->excludeIdForce)
                                unset($profiles[$k]);                        
                    }
                    
                    @shuffle($profiles);        
                    $profiles = @array_slice($profiles, 0, $this->countNeed);                     
                }
                
                $this->render( 'ProfilesInterest', array('profiles'=>$profiles, 'ajax'=>$this->ajax) );
            }
            /*else
                $this->render( 'ProfilesInterest' );*/
        }
    }
}
?>
