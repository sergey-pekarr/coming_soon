<h3>Images for approval</h3>

<div class="listsInfo">
    <div class="left">
        Found: <?php echo $images['count'] ?>
    </div>
      
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




<table class="table table-condensed">
<thead>
    <th>#</th>
    <th>Preview</th>
    <th>User Info</th>
    <th>Approve as</th>
    <th style="width: 90px;">Decline</th>
	<th style="min-width: 300px;">Decline Reason</th>
</thead>

<tbody>
<?php

if ($images['list'])
{
        foreach ($images['list'] as $k=>$row)
        {
            $userId = $row['user_id'];
        	$profile = new Profile($userId);
            ?>
            
            <tr id="tr_<?php echo $row['user_id'].'_'.$row['n'] ?>" >
            	
            	<td><?php echo $k+1+($pages->getCurrentPage() * $pages->getPageSize()) ?></td>
            	
                <td>
                    <?php //$this->widget('application.modules.admin.components.user.UserPreviewWidget', array('userId'=>$userId, 'n'=>$row['n'])) ?>
					<a target="_blank" href="<?php echo $profile->getUrlProfile() ?>">	
						<img style="width:120px" class="img-big" alt="" src="<?php echo $profile->imgUrl('big', $profile->imgGetIndx($row['n']), false) ?>" />
					</a>                    
                </td>                
                
                <td>
                    <?php $this->widget('application.modules.admin.components.user.UserInfoShotWidget', array('userId'=>$userId)) ?>
                </td>
				
				<td>
					<button 
						class="btn btn-success"
						onclick="javascript:imageApprove(<?php echo $row['user_id'] ?>, <?php echo $row['n'] ?>,'clothed')"
					>clothed</button>
					
					&nbsp;&nbsp;&nbsp;
					
					<button 
						class="btn btn-danger"
					onclick="javascript:imageApprove(<?php echo $row['user_id'] ?>, <?php echo $row['n'] ?>,'naked')"
					>naked</button>					
					</td>
					
					<td>
					<button 
					class="btn btn-inverse"
					onclick="javascript: imageApprove(<?php echo $row['user_id'] ?>, <?php echo $row['n'] ?>,'declined', $(this).parent().parent().find('td textarea').val()); "
                	>DECLINE</button>
                </td>
				
				<td>
					<span class="smaller" style="line-height: 100%">We are sorry but your image was declined. <br /> Please read our Image upload rules before uploading any more images.</span>
					<br />
					<span style="color:#777">Aditional text (optional):</span>
					<br />
					<textarea style="width: 400px; height: 40px; margin: 0px;"></textarea>
				</td>
            
            </tr>
             
        <?php
        }
} ?>
</tbody>
</table>

