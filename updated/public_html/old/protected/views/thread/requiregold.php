<?php
CHelperProfile::getPaymentLinkWithAction($action, $targetid, $link, $nav);
$res = array('alert' => array(
		'title' => '',
		'desc' => '',
		'content' => '<div> <script> 
							$(document).ready(function(){ 
								window.location = "' . "/payment/$nav" . '";
							}); 
						</script></div>'
		));
echo CJavaScript::jsonEncode($res);
?>