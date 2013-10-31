<?php




if (!LOCAL) 
{
    $f = fopen(realpath(dirname(__file__))."/../../log.log", "a");
    $s = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . "\n";
    fputs($f, $s);
    fclose($f); 
}
    


//!!!!!!!!!! надо для IE под канвас
/**/
header("Expires: Sat, 16 May 1978 00:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: post-check=0, pre-check=0",false);
session_cache_limiter("must-revalidate");





//для http://www.facebook.com/insights/
//facebookexternalhit
if (stristr(strtolower($_SERVER['HTTP_USER_AGENT']), "facebookexternalhit"))
{
    echo '<!DOCTYPE html><html lang="en"><head><meta property="fb:admins" content="100003097816684" /><meta property="fb:app_id" content="100003097816684" /></head><body>&nbsp;</body></html>';
    exit();
}

//  IE / FB bug
//http://blog.colnect.com/2010/10/fbxdfragment-bug-workaround.html
//http://stackoverflow.com/questions/3923775/integrating-facebook-to-the-leads-to-blank-pages-on-some-browsers-fb-xd-fragmen
//http://forum.developers.facebook.net/viewtopic.php?id=60571
//http://bugs.developers.facebook.net/show_bug.cgi?id=9777
//http://dorkage.net/blog/2011/01/18/fb_xd_fragment-facebook-bugfix/
if(isset($_GET['fb_xd_fragment']) || stristr($_SERVER["REQUEST_URI"], "fb_xd_fragment")) {
  echo "<script src=\"http://connect.facebook.net/en_US/all.js\"></script>";
  exit();//return false;
}



