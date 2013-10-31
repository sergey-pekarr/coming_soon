<h3>Recent Activity</h3> 
    
<ul class="dashboard-list update">
            <?php
                if ($updates)
                foreach ($updates as $up) 
                { 
                    $this->widget('application.components.dashboard.PanelDashboardUpdatesBoxWidget',  array('update'=>$up));
                }
            ?>
</ul>

<div class="clear"></div>
