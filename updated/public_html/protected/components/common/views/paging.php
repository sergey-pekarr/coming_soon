<?php
$minrange = 1;
$maxrange = $total;
$first = 1;
$leftcont = -1;
$last = $total;
$rightcont = $total+1;
$N = 3;
if($page<=$N){
	$maxrange = 2*$N;
	if($maxrange<$total){
		$rightcont = $maxrange+1;
		$last = $total;
	} 
}
else if($page>=$total-$N){
	$minrange = $total - 2*$N;
	if($minrange>1){
		$leftcont = $minrange-1;
		$first = 1;
	}
}
else{
	$minrange = $page - $N + 1;
	$maxrange = $page + $N - 1;
	$leftcont = $minrange -1;
	$rightcont = $maxrange + 1;
}
?>
<div class="content_nav round" style="width: <?php if(isset($panelWidth)) echo $panelWidth; else echo '668px'; ?>">
    <table style="table-layout: fixed;">
        <tbody>
            <tr>
                <td class="content_nav_left" style="width: 150px;">							
					<?php if (1< $page) {?>
                    <a href="<?php echo $this->getLink(min($page -1,max($total,1)) ); ?>" class="content_nav_prev" style="float: right; right: 40px">Go Back<span></span></a>
					<?php } else { ?>
                    <h1 style="float: left; margin-left: 5px; padding-bottom:5px; font-size: 16px; font-weight: normal; color:#333333; line-height:18px" class="title"><?php echo $paneltitle ?></h1>
                    <?php } ?>
                </td>
                <td class="content_nav_center">
                    Page(s)
					<?php for($i=1; $i<= $total; $i++){
						if(($first < $i && $i <$leftcont) or ($rightcont < $i && $i < $last)) continue;
						if($i==$first || $i == $last || ($minrange <= $i && $i <= $maxrange)){
					?>
						<a href="<?php echo $this->getLink($i); ?>" <?php if ($page==$i) echo 'class="active content_nav_page"'; else echo 'class="content_nav_page"'; ?>><?php echo $i;?> </a>
						<?php } 
						else if($i == $leftcont || $i == $rightcont){ ?>
						<a href="<?php echo $this->getLink($i); ?>" <?php if ($page==$i) echo 'class="active content_nav_page"'; else echo 'class="content_nav_page"';?>>...</a>
					<?php }
					}?>
                </td>
                <td class="content_nav_sright" style="text-align: center; width:0px;">
                </td>
                <td class="content_nav_right">
				<?php if ($page<$total) {?>
                    <a href="<?php echo $this->getLink($page +1); ?>" class="content_nav_next" style="float: right; right: 40px">Next Page<span></span></a>
				<?php };?>
                </td>
            </tr>
        </tbody>
    </table>
</div>