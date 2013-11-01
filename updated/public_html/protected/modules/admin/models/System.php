<?php

class System
{
	
    public function clearCache()
    {
        Yii::app()->cache->flush();
    }
	
    public function clearAssets()
    {
        $dir = DIR_ROOT.'/assets';
        CHelperFile::clearDir( $dir, $dir, array('index.php', '.htaccess') );//$this->_clearDir( $dir, $dir );
    }
}
