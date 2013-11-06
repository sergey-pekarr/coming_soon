<?php if ($showWindow) { ?>
    
    <div id="noAccess" class="modal hide fade">
        <br />
        <p style="font-weight: bold"> 
            <span style="color:red">ACCESS DENIED</span> 
            <br /><br /> 
            Only users with video profiles are allowed to do this, 
            <br />
            it's kind of an exclusive thing.
            
            <br /><br />
             
            We could let you in to join us, 
            <br /> all you need to do is   
            <a href="javascript:void(0)" onclick="javascript:window.location.href = '<?php echo Yii::app()->createAbsoluteUrl('/profile/myVideos') ?>'">record or upload</a>
            your video and 
            <br /> 
            enjoy premium membership for free.
            
            <?php /*
            
            If you just can't wait to get in, try _tipping the Swoonr bouncer $10_
            (this will be uncommented when we have more users, and also added to the "Waiting for video approval" screen)
             
            */ ?>
            
            
        </p>
        
        <br />

        <p>
<a href="javascript:void(0)" onclick="javascript:$('#noAccess').modal('hide'); $('.videoRecorder').show(); " class="btn">Close</a>
<?php /*            
            <?php if ($showButtonCreateVideo) { ?>
            <a href="javascript:void(0)" onclick="javascript:window.location.href = '<?php echo Yii::app()->createAbsoluteUrl('/profile/myVideos') ?>'" class="btn" style="margin-right: 20px;">Create Video</a>
            <?php } ?>
            
            <a href="javascript:void(0)" onclick="javascript:window.location.href = '<?php echo Yii::app()->createAbsoluteUrl('/dashboard/inbox') ?>'" style="color:grey">Return to Dashboard</a>
*/ ?>
        </p>                                       
    </div>
    
    <script type="text/javascript">
        $(document).ready(function() 
        {
            $(".videoRecorder").hide();
            $("#noAccess").modal({backdrop:'static'}).modal('show');
        })
    </script>
     
<?php } ?>