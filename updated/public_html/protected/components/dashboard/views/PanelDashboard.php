<div id="PanelDashboardTabs" class="tabs <?php if ($all) echo 'all' ?>">
    <ul>
        <li class="tab-left">
            <a href="#PanelDashboardUpdates">Updates</a>
        </li>
        <li class="tab-center">
            <a href="#messages">Messages</a>
        </li>
        <li class="tab-right">
            <a href="#PanelDashboardTabs-3">Matches</a>
            <?php if (!$all) { ?>
                <a class="show-all" href="/profiles/likeAll">Show all &raquo;</a>
            <?php } ?>
        </li>        
    </ul>
    <div id="PanelDashboardUpdates" class="updates-list">        
        <?php $this->widget('application.components.dashboard.PanelDashboardUpdatesWidget'); ?>
    </div>
    <div id="messages">
        <?php $this->widget('application.components.dashboard.PanelDashboardMessagesWidget'); ?>
    </div> 
    <div id="PanelDashboardTabs-3">        
        <?php $this->widget('application.components.dashboard.PanelDashboardMatchesWidget'); ?>
    </div>                   
</div>

<script type="text/javascript">
    $(document).ready(function() 
    {
        $("#PanelDashboardTabs").tabs();
    })
</script>