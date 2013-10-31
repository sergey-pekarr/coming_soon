<script type="text/javascript">
var olhH = '14px';

$(document).ready(function() {
    $('#file_upload').uploadify({
        'uploader'  : '<?php echo $baseUrl ?>/uploadify.swf',
        'script'    : '<?php echo $baseUrl ?>/uploadify.php',
        'cancelImg' : '<?php echo $baseUrl ?>/cancel.png',
        'buttonImg' : '/images/design/uploadClipButton.png',
        //'folder'    : '/',//'/protected/tmp',
        
        <?php if ($fileDesc) { ?>
        'fileDesc'  : '<?php echo $fileDesc ?>',
        'fileExt'   : '<?php echo $fileExt ?>',
        <?php } ?>        
//  'fileExt'     : '*.jpg;*.gif;*.png',
//  'fileDesc'    : 'Image Files (*.jpg;*.gif;*.png*.jpg;*.gif;*.png*.jpg;*.gif;*.png*.jpg;*.gif;*.png)',
          
        'script'    : '<?php echo $callbackUrl ?>',

        'wmode'     : 'transparent',
        //'hideButton':true,        
        'buttonText': 'Upload Clip',
        
        'auto'      : true,
        
        onOpen    : function (event, queueID, fileObj) {
                        olhH = $("#file_uploadUploader").css('height');
                        $("#file_uploadUploader").css('height', '0');
                        $("#file_uploadUploader").removeAttr('height');//$("#file_uploadUploader").attr('height', '0');
                        <?php echo $onOpen; ?> 
        },
        //onSelect    : function (event, queueID, fileObj) {$("#file_uploadUploader").hide()},
        
        onCancel    : function (event, queueID, fileObj, data) {
                        $("#file_uploadUploader").css('height', olhH);
                        <?php echo $onCancel; ?> 
        },   
        
        onComplete: function (event, queueID, fileObj, response, data) {
                    // A function that triggers when a file upload has completed. The default 
                    // function removes the file queue item from the upload queue. The 
                    // default function will not trigger if the value of your custom 
                    // function returns false.
                    // Parameters 
                    //    event: The event object.
                    //    queueID: The unique identifier of the file that was completed.
                    //    fileObj: An object containing details about the file that was selected.
                    //    response: The data sent back from the server.
                    //    data: Details about the file queue.
//vd(fileObj);
                    <?php echo $onComplete ?>;  //window.location.reload();//
                        //alert('response.responseText');

                        return false;
        }
    });
});

</script>

<input id="file_upload" name="file_upload" type="file" />
