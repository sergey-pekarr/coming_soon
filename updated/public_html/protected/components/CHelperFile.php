<?php

class CHelperFile extends CFileHelper
{
    public static function clearDir( $dir, $startDir='', $ignoreFiles=array() ) 
    {
        if ($objs = glob($dir."/*")) 
            foreach($objs as $obj) 
                if (is_dir($obj)) 
                	CHelperFile::clearDir($obj, $startDir, $ignoreFiles); 
                else
                {
                	$fn = basename($obj);
                	if ($ignoreFiles) 
                	{
	                	if (!in_array($fn, $ignoreFiles))
	                		@unlink($obj);
                	}
                	else 
                		@unlink($obj);
                }
                	

        if (is_dir($dir) && $dir!=$startDir)
            @rmdir($dir);
    }
    
    
    public static function createDir($dir)
    {
        if (!is_dir($dir))
        {
            @mkdir($dir, 0777, true);
            @chmod($dir, 0777);
        }

        return is_dir($dir);
    }
    
    /*
     * find dirs in $dir
     */
    public static function findDirs( $dir ) 
    {
        $list = array();
    	
    	if ($objs = glob($dir."/*")) 
            foreach($objs as $obj) 
                if (is_dir($obj))
                {
                	$p = explode("/", $obj);
                	$dn = $p[count($p)-1];
                	$list[] = $dn;//pathinfo ( $obj , PATHINFO_FILENAME );//last element in path...
                }
                	

		return $list;
    }

    
    /*
     * chmod directory recursive
     */
	public static function chmodDirectory( $path, $level = 0 )
	{  
		if (is_dir($path))
			chmod($path, 0777);
		else
			return;		
//echo $path.'<br />';		
		
		$dirsFolder = CHelperFile::findDirs($path);

		if ($dirsFolder)
			foreach ($dirsFolder as $f)
			{
				chmod($path.'/'.$f, 0777);				
				
				self::chmodDirectory($path.'/'.$f);
			}
		
		/*$ignore = array( 'cgi-bin', '.', '..' ); 
  		$dh = @opendir( $path ); 
  		while( false !== ( $file = readdir( $dh ) ) )// Loop through the directory
  		{
  			if( !in_array( $file, $ignore ) )
  			{
  				if( is_dir( "{$path}/{$file}" ) )
		        {
		
		          	@chmod("{$path}/{$file}", 0777);
		
		          	self::chmodDirectory( "{$path}/{$file}", ($level+1));
		
		        } 
		        else 
		        {
		         	@chmod("{$path}/{$file}",0777); // desired permission settings
		        }//elseif 
			}//if in array 
		}//while 
	
		closedir( $dh ); */
	}    
    
}