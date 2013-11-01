<br /><br /><br /><br />

<?php 

    
if ($reports)
{
        echo '<table>';
        
        foreach ($reports as $row)
        {
            $userId = $row['id_to'];
            $profile = new Profile($userId); 
        ?>
            
            <tr id="row_<?php echo $row['id'] ?>">
            
            <td>
                <img src="<?php echo $profile->imgUrl('152x86'); ?>" />
            <td>
            
            <td>
                <?php echo $profile->getDataValue('username'); ?>
            </td>
            
            <td>
                <a href="javascript:hideReportAbuse(<?php echo $row['id'] ?>)">Hide report</a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="/admin/users/edit?id=<?php echo $profile->getDataValue('id'); ?>">Edit user</a>
            </td>
            
            </tr>
             
        <?php
        }
        
        echo '</table>';
}
