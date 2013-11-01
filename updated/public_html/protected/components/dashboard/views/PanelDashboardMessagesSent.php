<?php if (!$ajax) { ?>
    
    <?php if ($title) { ?>
    <h3><?php echo $title ?></h3>
    <?php } ?>
    
    <div id="MessagesSentBox"></div> 
        
    <script type="text/javascript">
            $(document).ready(function()
            {
                loadPanel("MessagesSent",0,<?php echo ($all) ? '1' : '0' ?>);
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
                        'panel'=>'MessagesSent',
                        'id'=>'MessagesSentPagination',
                    )
    );?>  
    <div class="clear"></div>
    

    <ul class="dashboard-list panel-list">

        <?php 
        if ($messages)
            foreach ($messages as $m) 
                $this->widget('application.components.dashboard.PanelDashboardMessagesBoxWidget',  array('message'=>$m, 'direction'=>'id_to'));
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
    
<?php } 
