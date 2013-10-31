<div id="NearBox">

    <ul class="profiles">
        <?php 
        if ($profiles)
            foreach ($profiles as $r) { ?>
            
            <li>
                <?php $this->widget('application.components.ProfileBoxWidget', array('id'=>$r['id'], 'imgSize'=>'152x114'/*, 'infoType'=>6*/)); ?>
            </li>
        
        <?php } ?>
    </ul>

</div>