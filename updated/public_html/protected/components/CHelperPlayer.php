<?php

class CHelperPlayer
{
    
    function playerSublime()
    {
        return !(LOCAL || Yii::app()->browser->isIE9());
        //return false;
    }



    function getPlayerWidth($ratio)
    {
        return 640;//($ratio=='4:3') ? 480 : 360;
    }    
    function getPlayerHeight($ratio)
    {
        return ($ratio=='16:9') ? 360 : 480;
    }   
            
}