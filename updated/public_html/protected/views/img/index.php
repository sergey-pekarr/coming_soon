<?php 

function caching_headers ($file, $timestamp) 
{
    $gmt_mtime = gmdate('r', $timestamp);
    header('ETag: "'.md5($timestamp.$file).'"');


    if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) 
    {
        if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5($timestamp.$file)) 
        {
            header('HTTP/1.1 304 Not Modified');
            exit();
        }
    }

    header('Last-Modified: '.$gmt_mtime);
    header('Cache-Control: public');
}

//FB::info($img);

if ($img['imgPath'])
{
    caching_headers($img['imgPath'], $img['filemtime']);
    
    header("Content-Type: ".$img['imgInfo']['mime']);
    echo @file_get_contents($img['imgPath']);     
}
else
{
    header("HTTP/1.0 404 Not Found");
    echo '<h1>Not Found</h1>';
    echo '<p>The image you requested could not be found.</p>';
}



   
