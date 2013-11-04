<?php 
	
function formatDate($date){
	return date('M d, Y', strtotime($date));
}

function showAuthor($authorProfile){
	echo "<img src=\"".$authorProfile->imgUrl()."\" width=\"62\" height=\"62\" />";
}

function showTestDesc($item){
?>
	<p><a class="testname" title="View Test Result" href="/quizz/test/<?php echo $item['id']; ?>"><?php echo $item['name']; ?></a></p>
	<p class="testdescription" style="text-align:justify; display:none;"><?php echo $item['description']; ?></p>
<?php
}

function showUserTestInfo($item, $authorProfile, $mytest){
	$authorencid = Yii::app()->secur->encryptID($item['author']);
	$authorName = $authorProfile->getDataValue('username');
	$createdate = formatDate($item['createdate']);
	$regdate = formatDate($item['regdate']);
?>
	<div class="star">
		<div class="stargray ">
			<span style="width:<?php echo round($item['star']/5*100, 0) ?>%;"></span>
			</div>
			<div style="float:left;">  &nbsp;&nbsp;<?php echo round($item['star'], 2) ?></div>
			<div class="clear"></div>
			</div>
			<p><?php echo "<span class='gray'>Created by </span><a href='/profile/$authorencid'>$authorName
							</a><span class='gray'> on </span>$createdate<span class='gray'>.</span>"; ?></p>
			<?php if($mytest && isset($item['tookdate'])) { 
				echo "<p><span class='gray'>You took on </span>".formatDate($item['tookdate']).
					". <span></span><b><a href='/quizz/retaketest/{$item['id']}' >Retake</a></b></p>"; 
			} ?>
			<p><?php echo "<span class='gray'>Your result: </span><b> {$item['result']} </b>"; ?></p>
			<p><?php echo "<b><a href='/quizz/retaketest/{$item['id']}' >Retake</a></b>
						   <span class='gray'> Or </span>
						   <b><a href='/quizz/deletetakentest/{$item['id']}' >Delete</a></b>"; ?></p>
<?php
}

?>


<?php 
if($total > 1){
	$this->widget('application.components.common.PagingWidget', 
		array('title'=>$panelTitle, 'panelWidth'=>668, 'total'=>$total, 'page'=>$page, 'ajax' => true, 'ajaxMethod' => 'TestList.search', 'options' => array() )); 
}
else{
	echo "<div class='spacer'></div>";
}

?>

<table cellspacing="0">
    <tbody>
	<?php foreach($tests as $item) { 
	?>
		<tr style="<?php if(isset($item['point1'])) echo 'background-color:#efefff;'; ?>">
			<td><?php 
			$authorProfile = new Profile($item['author']);
			showAuthor($authorProfile); ?></td>
			<td style="width: 400px;"><?php showTestDesc($item); ?></td>
			<td style="padding-left:20px;"><?php showUserTestInfo($item, $authorProfile, $mytest); ?></td>
</tr>
	<?php } ?>
    </tbody>
</table>
<script>
	$(document).ready(function(){
		TestList.truncateDescription();
	});
</script>