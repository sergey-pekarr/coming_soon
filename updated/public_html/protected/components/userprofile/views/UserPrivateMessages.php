    <div class="messages-list wide-messages">
        <ul id="profile-private-messages-list">
        
            <li class="write">
                <div class="image">
                    <img src="<?php echo Yii::app()->user->Profile->imgUrl('small') ?>" />
                </div>
                
                <div class="message message-input">
                    <textarea id="messageBody" onkeyup="javascript:textchange(this.id,1000)" title="Write something...">Write something...</textarea>
                </div>
                
                <div class="message-footer message-input-footer"> </div>
                
                <div class="other">
                    <button id="sendMessage" class="btn disabled" <?php /* disabled="disabled" */ ?> onclick="javascript:sendPrivateMessage(<?php echo $profile->getDataValue('id') ?>)" >Send</button>
                </div>
                
                <div id="messageBody_count_sym" class="left-chars-counter">1000</div>
                
                <div class="clear"></div>                
            </li>        
        
        
            <?php foreach ($messages as $m) 
                $this->widget('application.components.userprofile.UserPrivateMessagesBoxWidget',  array('message'=>$m));
            ?>
             
        </ul>
    </div>
