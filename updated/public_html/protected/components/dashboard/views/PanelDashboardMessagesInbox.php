<?php if (!$ajax) { ?>
    
    <?php if ($title) { ?>
    <h3><?php echo $title ?></h3>
    <?php } ?>

    
    <div id="MessagesInboxAllBox"></div> 
        
    <script type="text/javascript">
            $(document).ready(function()
            {
                loadPanel("MessagesInboxAll",0,<?php echo ($all) ? '1' : '0' ?>);
            })
    </script>

<?php } else { ?>

    <?php 
            $this->widget(
                    'AjaxLinkPager', 
                    array(
                        'pages' => $pages,
                        'all' => $all,
                        'currentPage'=>$pages->getCurrentPage(false),
                        'panel'=>'MessagesInboxAll',
                        'id'=>'MessagesInboxAllPagination',
                    )
    );?>  
    <div class="clear"></div>
    

    <ul class="dashboard-list panel-list">

        <?php 
        if ($messages)
            foreach ($messages as $m) 
                $this->widget('application.components.dashboard.PanelDashboardMessagesBoxWidget',  array('message'=>$m, 'inboxAll'=>true));
        ?>
             
    </ul>
        
    <div class="clear"></div>
    
    <?php /* if (CHelperPlayer::playerSublime()) { ?>
        <script type="text/javascript">
            $(document).ready(function()
            {
                sublimevideo.load();
            });                            
        </script>
    <?php } */ ?>
    
<?php } ?>












<?php /*    
    
    <div style="float: right;"> 1 2 3 4 5 </div>
    <div class="clear"></div>

    <ul class="dashboard-list">

        <?php 
        if ($messages)
            foreach ($messages as $m) 
                $this->widget('application.components.dashboard.PanelDashboardMessagesBoxWidget',  array('message'=>$m));
        ?>
             
    </ul>
        
    <div class="clear"></div>
*/ ?>
