<?php
class ProfilesNearTestPlayersWidget extends CWidget
{
    public function init()
    {
        //$model = new Profiles;
        $this->render( 'ProfilesNearTestPlayers', array( 'profiles'=>Yii::app()->user->Profile->getDataValue('promos') ) );
    }
}
?>
