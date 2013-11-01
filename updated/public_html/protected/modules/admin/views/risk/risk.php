<style>
#risks-unapproved ul
{
	margin-left: 12px;
}
#risks-unapproved ul ul
{
	margin-left: 12px;
}
#risks-unapproved ul li
{
	list-style-type: disc;
}
</style>
<table>
	<tr>
		<td><h3>Unresolve Risks (<?php echo $model['count']; ?>)</h3></td>
		<td style="width: 100px;"></td>
		
		<td>
		</td>
		
		<?php if(false) { ?>
		<td>
			<b>Risk Type </b>
		</td>
		<td>
			<select style="width: 120px;" onchange="riskFilter(this.value);">
				<option value="all" <?php if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'all') echo 'selected="selected"'; ?>>All</option>
				<option value="location" <?php if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'location') echo 'selected="selected"'; ?>>Location</option>
				<option value="figure_print" <?php if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'figure_print') echo 'selected="selected"'; ?>>Figure Print</option>
			</select>
		</td>
		<?php } ?>
		
	</tr>
</table>

<?php 
function lineBreak($text, $length = 50, $separator = ',', $max = 65000){
	
	if(!$text || strlen($text)< $length) return $text;
	
	$res = '';
	$i = $length;	
	$s = 0;
	while($i < strlen($text) && ($f = strpos($text, $separator, $i))){
		$res .= substr($text, $s, $f - $s).' ';
		$i = $f + $length;
		$s = $f;
		if($s >= $max) return $res;
	}
	if($s >= $max) return $res;
	$res .= substr($text, $s, strlen($text) - $s);
	return $res;
}

function showDetail($text){
	try{
		$detailArr = json_decode($text);
		$result = '';
		if($detailArr && count($detailArr) >= 2){
			$result = '<ul>';
			$result .= '<li>Positive duplicate device: ' . json_encode($detailArr[0]).'</li>';
			$result .= '<li>Location and distance:';
			$result .= '<ul>';
			foreach($detailArr[1] as $key => $val){
				$result .= "<li> $key: $val miles</li>";
			}
			$result .= '</ul>';
			$result .= '</ul>';
		}
		return $result;
	}
	catch(exception $ex){
		
	}
	return '';
}
?>	
<div>      
    <?php 
    $this->widget(
    	'CLinkPager', 
    	array(
    			'pages' => $pages,
    			'currentPage'=>$pages->getCurrentPage(),//(false)
    			'header'=>'',
    			'htmlOptions'=>array('class'=>'pagination'),                        
    			)
    );?>
    <div class="clear"></div>        
</div>

<table id="risks-unapproved" class="table summary">
<thead>
	<tr>
		<td>No</td>
		<td>id</td>
		<td>Username</td>
		<td>Agent name</td>
		<td>Manager name</td>
		<?php if(false) { ?>
		<td>Duplicate by location</td>
		<?php } ?>
		<td>Duplicate</td>
		<td>Date</td>
		<td>Resolve</td>
		<!--<td> decline </td>-->
		<!--<td> status </td>-->
	</tr>
</thead>
<tbody>
	<?php
        $max = 0;
        $min = 0; 
        for($i=0;$i<count($risks); $i++) {
		$req = $risks[$i];
        if($max == 0 || $max < $req['id']) $max = $req['id'];
        if($min == 0 || $min > $req['id']) $min = $req['id'];
	?>
<tr>
		<td><?php echo $base + $i + 1; ?></td>
		<td><?php echo $req['id']; ?></td>
		<td><?php echo $req['loginAnchor']; ?></td>
		<td><?php echo $req['agent_name']; ?></td>
		<td><?php echo $req['manager_name']; ?></td>
		<?php if(false) { ?>
		<td style="width: 400px;">
			<div onclick="toggleShow(this);" style="width: 400px; word-break:break-all; overflow-y:hidden; max-height: 100px; cursor: pointer; " title="click to expand or collapse">
            <?php if($req['duplicate_by_location']) echo '<a href="'.SITE_URL."/admin/payments/todayGold/?sale={$req['id']},".$req['duplicate_by_location'].'">'.lineBreak($req['duplicate_by_location'],50,',',50).'</a>'; ?>
            <?php if($req['duplicate_by_location'] && $req['location_detail'] && $req['location_detail'] <> '[]') echo '<br /> Detail: '. lineBreak($req['location_detail']); ?>
			</div>
        </td>
		<?php } ?>
		<td style="width: 400px;">
			<div onclick="toggleShow(this);" style="width: 400px; word-break:break-all; overflow-y:hidden; max-height: 100px; cursor: pointer; " title="click to expand or collapse">
			<?php if($req['duplicate_by_figure_print']) echo '<a href="'.SITE_URL."/admin/payments/todayGold/?sale={$req['id']},".$req['duplicate_by_figure_print'].'">'.lineBreak($req['duplicate_by_figure_print'],50,',',50).'</a>'; ?>
			<?php if($req['figure_print_detail'] && $req['figure_print_detail'] <> '[]') echo '<br />'. showDetail($req['figure_print_detail']); ?>
			<a target="blank" href="/admin/risk/affcardinfo?AffCardInfoForm%5Bids%5D=<?php echo "{$req['id']},".$req['duplicate_by_figure_print'];  ?><?php if(isset($req['manager_id'])) echo "&AffCardInfoForm%5Baff%5D={$req['manager_id']}"; ?>">Check card information</a>
			</div>
		</td>
        <td><?php echo date('Y-m-d', strtotime($req['saledate'])); ?></td>
		<td><input type="button" value="Resolve" onclick="resolveRisk(this, <?php echo $req['id'] ?>);" /></td>
</tr>
	<?php } ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            <?php if($max > 0 && $min > 0) { ?>
            <input type="button" value="Resolve All" onclick="resolveRisk(this, <?php echo $min ?>, <?php echo $max ?>);" />
            <?php } ?>
        </td>
    </tr>
</tbody>
</table>

<script>
    function riskFilter(value) {
	/*
        var url = document.URL;
        if (url.indexOf('type') > 0) {
            url = url.replace(/type=\w+/, 'type=' + value);
        }
        else if (url.indexOf('?') > 0) {
            url = url + '&type=' + value;
        }
        else {
            url = url + '?type=' + value;
        }
        window.location = url;
	*/
    }
    function riskFilter2(incbrowser) {
        var url = document.URL;
        if (url.indexOf('incbrowser') > 0) {
            url = url.replace(/incbrowser=\w+/, 'incbrowser=' + incbrowser);
        }
        else if (url.indexOf('?') > 0) {
            url = url + '&incbrowser=' + incbrowser;
        }
        else {
            url = url + '?incbrowser=' + incbrowser;
        }
        window.location = url;
    }
    function toggleShow(ele) {
        var max = $(ele).css('max-height');
        if (max == 'auto' || max == '' || max == 'none') {
            $(ele).css('max-height', '100px')
        }
        else {
            $(ele).css('max-height', '')
        }
    }
    function resolveRisk(ele, id, max) {
        var url = '/admin/risk/resolve?id=' + id;
        if(max) url += '&maxid=' + max;
        $.post(url)
        .success(function () {
            if (!max) {
                $('tr').has(ele).attr('disabled', 'disabled');
                $('tr').has(ele).css('color', '#ccc');
                $(ele).attr('onclick', '');
                $(ele).attr('disabled', 'disabled');
            }
            else {
                window.location = document.URL;
            }
        })
        .fail(function () {
        });
    }
</script>