
<h3>Total exists</h3>
<table class="table table-condensed summary">
	<tr>
        <td>Not Approved Images</td>
        <td>
        	<?php if ($summary['totalImagesNotApproved']) { ?>
        		<a class="bold" href="/admin/users/approveImage"><?php echo $summary['totalImagesNotApproved'] ?></a>
        	<?php } else { ?>
        		0
        	<?php } ?>
        </td>
    </tr>
<?php /*
	<tr>
        <td>Not Rated Images</td>
        <td>
        	<?php if ($summary['totalImagesNotRated']) { ?>
        		<a class="bold" href="/admin/users/xrateImage"><?php echo $summary['totalImagesNotRated'] ?></a>
        	<?php } else { ?>
        		0
        	<?php } ?>
        </td>        
        
    </tr>
*/ ?>
</table>

