<div id="home-online-members-box">
    <h2>Online members</h2>

    <div class="panel_box">
        
        <ul class="profiles">
            <?php foreach ($profiles as $r) { ?>
                
                <li>
                    <?php $this->widget('application.components.ProfileBoxWidget', array('id'=>$r['id'], 'imgSize'=>'medium', 'infoType'=>4)); ?>
                </li>
            
            <?php } ?>
        </ul>    
        
    </div>

</div>