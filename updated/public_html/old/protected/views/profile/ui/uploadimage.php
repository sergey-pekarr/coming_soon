<?php

$primaryimgurl = $profile->imgUrl('big', 0, true);
$imgs = $profile->getDataValue('image');

?>

<script>
    var isChanged = false;
    uploadPhoto = function () {
        $('#profileuploadimage').css('display', 'block');
        showMessageWithTitlePopup('profileuploadimage', 'Upload your images', 710, 470);

        $('#msgwithtitle-container-profileuploadimage .ui-dialog-system>a').click(function () {
            window.setTimeout(function () {
                if (isChanged && $('#msgwithtitle-container-profileuploadimage').parent().css('display') != 'block') {
                    window.location = '/profile';
                }
            }, 200);
        });

        var uploader = new qq.FileUploader({
            element: document.getElementById('uploadphotocontrol'),
            action: '/img/uploadprofile',
            onSubmit: function (id, fileName) { },
            onProgress: function (id, fileName, loaded, total) { },
            onComplete: function (id, fileName, responseJSON) {
                uploadUploadComplete(responseJSON);
                isChanged = true;
            },
            debug: true
        });
    }

    uploadChangeImage = function (ele) {
        var url = $(ele).find('img').attr('bigUrl');
        $('#profileuploadimage img.big').attr('src', url);

        //$('#profile-img-container img').prop('src', url);
        //Change image will cause dialog move. Need to fix later

        var picid = $(ele).find('img').attr('picId');
        $.get('/profile/selectprimary/' + picid, function () {
        })
        .success(function () {
            isChanged = true;
        });
    }

    uploadDeleteImage = function () {
        var url = $('#profileuploadimage img.big').attr('src');
        var imgId = null;
        var first = null;
        var next = null;
        $('#photoUpload-photos img').each(function (index, ele) {
            if (!first) first = $(ele).attr('bigUrl');
            if (!next && imgId) next = $(ele).attr('bigUrl');
            if ($(ele).attr('bigUrl') == url) {
                imgId = $(ele).attr('picId');
            }
        });
        if (imgId) {
            var delUrl = '/profile/deleteimage/' + imgId;
            $.get(delUrl, function (res) {
            })
            .success(function (res) {
                if (!next) {
                    next = first;
                }
                if (!next) {
                    next = '/images/design/nophoto_male_big.jpg';
                }
                $('#photoUpload-photos img[picId="' + imgId + '"]').remove();
                try {
                    res = $.parseJSON(res);
                }
                catch (exe) {
                }
                if (res && res.nextBigUrl) next = res.nextBigUrl;
                if (next) {
                    $('#profileuploadimage img.big').attr('src', next);
                }
                isChanged = true;
            });
        }
    }

    var uploadPhotoTemplate =
'<a class="small profile_img" onclick="uploadChangeImage(this); return false;" href="#">' +
	'<img class="selected" alt="" src="" bigUrl="" picId="" width="32" height="32" rel="0">' +
'</a>'

    function uploadUploadComplete(res) {
        var jq = $(uploadPhotoTemplate).appendTo($('#photoUpload-photos')).find('img');
        jq.attr('src', res.smallUrl);
        jq.attr('bigurl', res.bigUrl);
        jq.attr('picId', res.picId);
    }

    uploadUploadImages = function () {

    }

    uploadUploadClick = function () {
        if ($('#uploadForm #file').val() == '') {
            alert('You must select a photo to upload');
        }
        else {
            uploadUploadImages();
        }
    }
</script>

<div id="profileuploadimage" style="display: none;">
    <div style="padding: 5px;">
        <div class="photoUploadBig">
            <img style="display: block; max-height:310px;" class="big" alt="" src="<?php echo $primaryimgurl; ?>"
                width="200">
            <div style="display: block;">
            </div>
            <div style="margin-top: 10px;" id="imgactions" class="tooltips">
                <a title="Delete Photo" onclick="uploadDeleteImage(); return false;" href="#">
                    <img style="border: 0px currentColor; margin-left: 19px;" alt="Rotate Left" src="/images/img/profile.delete.png"></a>
            </div>
            <div class="clear">
            </div>
            <div style="background: rgb(235, 235, 235); padding: 6px 6px 0px; width: 192px !important; min-height:40px; overflow:auto; max-height:95px;" id="photoUpload-photos">
            	<?php 
	            if ($imgs)
	            foreach($imgs as $i=>$img) { ?>
		            <a class="small profile_img" onclick="uploadChangeImage(this); return false;" href="#">
				            <img class="selected" alt="" src="<?php echo $profile->imgUrl('small', $i, false); ?>" 
				            bigUrl="<?php echo $profile->imgUrl('big', $i, false); ?>"
				            picId="<?php echo $img['n']; ?>" width="32" height="32" rel="0">
			            </a>
		        <?php } ?>
            </div>
            <div class="clear">
            </div>
        </div>
        <form id="uploadForm" enctype="multipart/form-data" method="post" action="/profile/uploadimage">
        <div style="padding: 0px 20px; width: 440px;" class="photoUploadImages">
            <h3>
                Upload a file from your computer</h3>
            <p style="line-height: 19px; font-size: 12px;">
                Just click the button below to select a file from your computer to uploaded to your profile.</p>
            <fieldset style="padding: 0px; border: 0px currentColor; margin-top: 12px;">
                <div id='uploadphotocontrol'></div>
            </fieldset>
            
        </div>
		<div style="padding: 0px 0px 0px 20px;">
            <hr style="border-width: 1px 0px 0px; border-style: solid none none; border-color: rgb(189, 189, 189) currentColor currentColor;">
            <div style="text-align: center; margin-top: -15px;">
                <span style="background: rgb(255, 255, 255); padding: 5px 10px;">OR</span></div>
             <p style="line-height: 19px; font-size: 12px;">
            	<span class="bold">Upload from facebook</span>
            </p>
            <table>
                <tr>
                    <td style="width:240px; padding:10px 10px 0px 0px; vertical-align:top;">
                        If you wish to upload a picture from facebook, just click the button to proceed to our facebook uploader.
                    </td>
                    <td style="padding:10px 10px 0px 0px; vertical-align:top;">
                        <a href="profile/fbimage">
                            <img src="/images/img/upload-facebook.png"></a>
                    </td>
                </tr>
            </table>

            
            <div style="height: 25px !important;" class="clear">&nbsp;</div>
            
            <p style="line-height: 19px; font-size: 12px;">
            	<span class="bold">Image upload rules</span> <br />
            	1. pictures with any contact info will be declined <br />
				2. any pictures with kids, or other people then the user will be declined <br />
				3. any pictures not of the user will be declined
            </p>
        </div>
        </form>
        <div class="clear">
        </div>
    </div>
</div>
