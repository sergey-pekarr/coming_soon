
<?php /*
<div id="fb-root"></div>

<script type='text/javascript'>
    window.fbAsyncInit = function() {
        FB.init({
            appId : '<?php echo $facebook->getAppId(); ?>',
            session : <?php echo json_encode($facebook->getSession());?>,
            status : true,
            cookie : true,
            xfbml : true
        });
        
        FB.Event.subscribe(
            'auth.login', 
            function() {
                window.location.reload();
        });
    };
    (function() {
            var e = document.createElement('script');e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
            e.async = true;
            document.getElementById('fb-root').appendChild(e);
    }());
    
    
</script>
*/ ?>

<?php
//echo $facebook->getLoginUrl();

/*
if ($user){
echo '<a href="'.$logout.'">';
echo Chtml::image("http://static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif",'Logout');
echo '</a>';
}else{ */ 


/*
echo '<a href="'.$facebook->getLoginUrl().'">';
//echo Chtml::image("http://static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif",'Login');
echo Chtml::image("http://".DOMAIN."/images/design/loginFB.png",'Login');
echo '</a>';
*/
?>

<a href="<?php  echo (DEMO) ? '' : Yii::app()->createAbsoluteUrl('site/facebooklogin') /*$facebook->getLoginUrl()*/ ?>">
    <img width="197" height="35" src="http://<?php echo DOMAIN ?>/images/design/loginFB.png" style="border: none;"/>
</a>
 