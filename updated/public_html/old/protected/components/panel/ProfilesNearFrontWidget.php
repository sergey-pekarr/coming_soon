<?php
class ProfilesNearFrontWidget extends CWidget
{
    public $b=false;
    
    public function init()
    {
        //$model = new Profiles;
        $profiles = Yii::app()->user->Profile->getDataValue('promos');
        
        @shuffle($profiles);

        if ($profiles)
            foreach ($profiles as $k=>$r) 
            {
                $profile = new Profile($r['id']);
                if ($profile->getDataValue('role')!='approved')
                    unset($profiles[$k]);
            }
        
        $profiles = @array_slice($profiles, 0, 9);
        
        /*if ( !CHelperPlayer::playerSublime() )
            $this->render( 'ProfilesNearFrontJW2', array( 'profiles'=>$profiles, 'b'=>$this->b ) );
        else
            $this->render( 'ProfilesNearFront', array( 'profiles'=>$profiles, 'b'=>$this->b ) );*/
        
        $this->render( 'ProfilesNearFrontNoVideo', array( 'profiles'=>$profiles, 'b'=>$this->b ) );
    }
}
?>
