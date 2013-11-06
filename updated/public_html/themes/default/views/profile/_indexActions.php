<?php 
            if (!$isOwner /*&& Yii::app()->user->checkAccess('limited')*/) { ?>
                <?php /* <a class="btn" href="#" data-controls-modal="sendMessageBox" data-backdrop="static" > */ ?>
                
                <a 
                    class="btn"
                    
                    <?php if (Yii::app()->user->checkAccess('limited')) { ?>
                        href="javascript:void(0)" 
                        onclick="javascript:messBoxShow()"
                        
                        id="messLink" 
                        title="" 
                        rel="popover" 
                    <?php } else { ?>
                        href="<?php echo Yii::app()->createAbsoluteUrl('/site/registration') ?>"
                    <?php } ?>
                >Send message</a>
                
                

<?php if (Yii::app()->user->checkAccess('limited')) {?>
    <script type="text/javascript">
        
        function messBoxShow() 
        {
            /*var bubbleTemplate = '';
            var bubbleAutoShow = false;
            
            bubbleTemplate = "<div class='content nobg'><p class='triangle-left top'>If you'd like to make your video later, click <a style='color:#fff;text-decoration:underline;' href='javascript:void(0)' onclick='javascript:window.location.href=\"/\"'>here</a></p></div>";
            bubbleAutoShow = true;
            
            if (bubbleTemplate)
            {
                $("#logoMain").popover({placement:'below-left', html:true, template:bubbleTemplate, delayIn:200, delayOut:400});//.popover('show');
                
                if (bubbleAutoShow)
                    $("#logoMain").popover('show');
            }*/
            
    /*var top = 300;
    var left = 370;
    $("#sendMessageBox").css('top', top);
    $("#sendMessageBox").css('left', left);*/
            
    $("#sendMessageBox").show();
    
    
    
            /*var bubbleMessTemplate = "";
            bubbleMessTemplate = $("#bubbleMessTemplate").html();
            
            $("#messLink").popover({placement:'left', html:true, template:bubbleMessTemplate, delayIn:200, delayOut:600000});
            $("#sendMessageBox").show();
            $("#messLink").popover('show');*/
             
        }
        
    </script>
<?php } ?>
              
                
                
                <br />
                
                <a 
                    class="btn"
                    <?php if (Yii::app()->user->checkAccess('limited')) {?>
                        href="/messages/videoMessageCreate/<?php echo Yii::app()->secur->encryptID( $profile->getDataValue('id') ) ?>"
                    <?php } else { ?>
                        href="<?php echo Yii::app()->createAbsoluteUrl('/site/registration') ?>"
                    <?php } ?>
                >Swoon <span class="videoMessage">video message</span> </a>
                
                <br />
                
                <?php 
                if (!$owner && $profileData['ext']['facebook']) { 
                    //$this->widget('application.components.facebook.FacebookScriptsWidget'); 
				?>
                    
                    <a 
                        class="btn" 
                        <?php if (Yii::app()->user->checkAccess('limited')) {?>
                            href="javascript:void(0)"
                            onclick="sendRequestToRecipients(); return false;"
                        <?php } else { ?>
                            href="<?php echo Yii::app()->createAbsoluteUrl('/site/registration') ?>"
                        <?php } ?>
                        
                    >Request Facebook info</a>
                    <input type="hidden" value="<?php echo $profileData['ext']['facebook']['fb_id'] ?>" name="user_ids" />
                    <br />
                    
                    
                    
                    
                    
<?php /*                    
    <div id="fb-root"></div>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <p>
      <input type="text" 
        value="Comma Delimited List of User IDs" 
        name="user_ids" size=”50” />
      <input type="button"
        onclick="sendRequestToRecipients(); return false;"
        value="Send Request to User(s)"
      />
    </p>
    
    <script>
      FB.init({
        appId  : '<?php echo Yii::app()->facebook->getAppId(); ?>',
        status : true,
        cookie : true,
        frictionlessRequests : true,
        oauth: true
      });

      function sendRequestToRecipients() {
        var user_ids = document.getElementsByName("user_ids")[0].value;
        FB.ui({method: 'apprequests',
          message: 'My Great Request',
          to: user_ids,
        }, requestCallback);
      }
      
      function requestCallback(response) {
        // Handle callback here
        console.log(response);
      }
    </script>
*/ ?>                    
                    
                    
                    
                    
                    
                    
                <?php } ?>
                
                <a 
                    class="btn" 
                    <?php if (Yii::app()->user->checkAccess('limited')) {?>
                        href="javascript:void(0)" 
                    <?php } else { ?>
                        href="<?php echo Yii::app()->createAbsoluteUrl('/site/registration') ?>"
                    <?php } ?>
                >Add to favorites</a>
                
<?php }
            
 /*
            if (!$isOwner && Yii::app()->user->checkAccess('limited')) { ?>
                <a href="#" data-controls-modal="sendMessageBox" data-backdrop="static" >
                    Send Message
                </a>
                <br />
                <a href="javascript:void(0)">Swoon (video message)</a>
                <br />
                <a href="javascript:void(0)">Request facebook</a>
                <br />
                <a href="javascript:void(0)">Add to favorites</a>
                
<?php }*/ ?>        






