    <h3>Messages (<span id="messagesNewCount"><?php echo $newCount ?></span> new)</h3>   

    <ul <?php /* id="dashboard-messages-list" */ ?> class="dashboard-list">

        <?php 
        if ($messages)
            foreach ($messages as $m) 
                $this->widget('application.components.dashboard.PanelDashboardMessagesBoxWidget',  array('message'=>$m));
        ?>
             
    </ul>
        
    <div class="clear"></div>
    
    <div style="text-align: right; padding-top: 10px;">
        <a href="/messages/index" class="btn success">Go to all messages</a>
        <?php /* 
        <a href="/messages/inboxAll" style="border: none; text-decoration: none;">
            <img src="/images/design/buttonGoNewMessages.png" style="width: 162px; height: 34px; margin: 0; border: none;" />
        </a>
        */ ?>
    </div>
