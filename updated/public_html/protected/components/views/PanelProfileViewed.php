<div id="PanelProfileViewedTabs" class="tabs <?php if ($all) echo 'all' ?>">
    <ul>
        <li class="tab-left">
            <a href="#PanelProfileViewedTabs-1">You Viewed</a>
            <?php if (!$all) { ?>
                <a class="show-all" href="/profiles/viewedAll">Show all &raquo;</a>
            <?php } ?>    
        </li>
        <li class="tab-right">
            <a href="#PanelProfileViewedTabs-2">Viewed You</a>
            <?php if (!$all) { ?>
                <a class="show-all" href="/profiles/viewedAll">Show all &raquo;</a>
            <?php } ?>
        </li>
    </ul>
    <div id="PanelProfileViewedTabs-1">        
        <?php $this->widget('application.components.PanelProfileViewedToWidget', array('all'=>$all)); ?>
    </div>
    <div id="PanelProfileViewedTabs-2">        
        <?php $this->widget('application.components.PanelProfileViewedFromWidget', array('all'=>$all)); ?>
    </div>                
</div>

<script type="text/javascript">
    $(document).ready(function() 
    {
        $("#PanelProfileViewedTabs").tabs(/*{
            create: function() {
                $(this).tabs("select", "#PanelProfileViewedTabs-1");
            }
        }*/);
    })
</script>

