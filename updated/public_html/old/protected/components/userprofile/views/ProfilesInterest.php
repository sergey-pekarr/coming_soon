<?php if ($profiles) { ?>
<div id="Interest">    
    <span class="alsoInter">You may also be interested in the following members</span>
        
    <div id="InterestBox">
        <ul class="profiles">
            <?php foreach ($profiles as $k=>$r) { ?>
               
                <li <?php if ($k==(count($profiles)-1)) { ?>class="last"<?php } ?>>
                    <?php $this->widget('application.components.ProfileBoxWidget', array('id'=>$r['id'], 'imgSize'=>'152x86', 'infoType'=>6)); ?>
                </li>
            
            <?php } ?>
        </ul>    
    
    </div>
        
    <div class="clear"></div>

</div>
<?php } ?>







<?php /* if ($ajax && $profiles) { ?>
    <ul class="profiles">
        <?php foreach ($profiles as $k=>$r) { ?>
           
            <li <?php if ($k==(count($profiles)-1)) { ?>class="last"<?php } ?>>
                <?php $this->widget('application.components.ProfileBoxWidget', array('id'=>$r['id'], 'imgSize'=>'152x86', 'infoType'=>6)); ?>
            </li>
        
        <?php } ?>
    </ul>
    
<?php } else { ?>
<div id="Interest">    
    <span class="alsoInter">You may also be interested in the following members</span>
        
    <div id="InterestBox"></div>
        
    <div class="clear"></div>

    <script type="text/javascript">
        $(document).ready(function() 
        {
            loadPanel("Interest",0,<?php echo ($all) ? '1' : '0' ?>);
        })
    </script>
</div>
<?php } */ ?>


