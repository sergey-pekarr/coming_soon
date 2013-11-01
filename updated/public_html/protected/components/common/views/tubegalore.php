<?php if($userprofile->getDataValue('role') == 'gold') { ?>
	<!-- 1054 270 -->
	<div id="ulp-container" style="display: none;">
		<div id="ulp-body" style="min-height:270px; width:1023px; background-color:#ffff00">
            <iframe id="frm-tubegalore" style="width:100%; height: 100%;">
            </iframe>
		</div>
	</div>
	<script>
	    function showUlp() {
	        var ulpHeight = $(window).height() - 100;
	        if (ulpHeight < 270) ulpHeight = 270;
	        $('#ulp-body').css('height', ulpHeight);
	        showPopup("ulp-container", 1023 + 20, ulpHeight + 20);
	        if ($('#frm-tubegalore').prop('src') == '') {
	            $('#frm-tubegalore').prop('src', 'http://www.tubegalore.com/')
	        }
	    }
	</script>
<?php } ?>