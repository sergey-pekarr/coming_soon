<div id="fb-root"></div>

<script src="http://connect.facebook.net/en_US/all.js"></script>
<script type='text/javascript'>
    FB.init({
            appId : '<?php echo $facebook->getAppId(); ?>',
            session : <?php echo json_encode($facebook->getSession());?>,
            status : true,
            cookie : true,
            xfbml : true,
            
            frictionlessRequests : true,//profile request
            oauth: true//profile request
    });
        
    FB.Event.subscribe(
            'auth.login', 
            function() {
                window.location.reload();
    });
    
    //profile requests section        
    function sendRequestToRecipients() {
        var user_ids = document.getElementsByName("user_ids")[0].value;
        FB.ui({method: 'apprequests',
          message: 'Info Request',
          to: user_ids, 
        }, requestCallback);
    }
    /*function sendRequestViaMultiFriendSelector() {
        FB.ui({method: 'apprequests',
          message: 'My Great Request'
        }, requestCallback);
    } */     
    function requestCallback(response) {
        // Handle callback here
    }    
            
    /*(function() {
            var e = document.createElement('script');e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
            e.async = true;
            document.getElementById('fb-root').appendChild(e);
    }());*/
    
    
</script>



<?php 
//for like button:
/* <div id="fb-root"></div> */ ?>
<?php /* 
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
 */ ?>
