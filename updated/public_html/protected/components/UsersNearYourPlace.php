<?php
class UsersNearYourPlace extends CWidget
{
    public $users, $city;

    public function init()
    {
        if(isset($this->users) && isset($this->city)){
        	$this->render('usersNearYourPlace', array('users'=>$this->users, 'city'=>$this->city));
        }
    }
}
?>
