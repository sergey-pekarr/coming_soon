<?php 
	
function formatDate($date){
	return date('M d, Y', strtotime($date));
}

function showAuthor($authorProfile){
	echo "<img src=\"".$authorProfile->imgUrl()."\" width=\"62\" height=\"62\" />";
}

function showTestDesc($item){
?>
	<p><a class="testname" title="Edit test" href="quizz/test/<?php echo $item['id']; ?>"><?php echo $item['name']; ?></a></p>
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
    <p><?php echo "<span class='gray'>It was last edited on </span>$regdate<span class='gray'>.</span>"; ?></p>
    <p><?php echo "{$item['taken']}<span class='gray'> people have taken it.</span>"; ?></p>
    <p><?php echo "{$item['last_date_taken']}<span class='gray'> people took it in the last 24 hours</span>"; ?></p>
	<p><?php echo "<b><a href='/quizz/deletetest/{$item['id']}' >Delete</a></b>
					<span class='gray'> or </span>
					<b><a href='/quizz/edit/{$item['id']}' >Edit</a></b>"; 
					$s ="
					<span class='gray'> or </span>
					<b><a href='/quizz/stats/{$item['id']}' >View Statistic</a></b>"; ?></p>

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
<tr>
<td colspan="3">
<div>
					<iframe style="width:100%; overflow: hidden;" src="/quizz/statistic/<?php echo $item['id']; ?>" scrolling="no"></iframe>
				</div>
			</td>
		</tr>
	<?php } ?>
    </tbody>
</table>
<script>
	$(document).ready(function(){
		TestList.truncateDescription();
	});
</script>