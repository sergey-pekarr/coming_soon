<?php if ($all) { ?>
    <div id="PanelProfileLikeTabs" class="tabs all">
            <ul>
                <li class="tab-left">
                    <a href="#PanelProfileLikeTabs-1">Matches</a>
                </li>

                <li class="tab-center">
                    <a href="#PanelProfileLikeTabs-2">You Like</a>
                </li>

                <li class="tab-right">
                    <a href="#PanelProfileLikeTabs-3">Likes You</a>
                </li>
            </ul>
            <div id="PanelProfileLikeTabs-1">        
                <?php $this->widget('application.components.dashboard.PanelDashboardMatchesWidget', array('all'=>$all)); ?>
            </div>
            <div id="PanelProfileLikeTabs-2">        
                <?php $this->widget('application.components.PanelProfileLikeToWidget', array('all'=>$all)); ?>
            </div>
            <div id="PanelProfileLikeTabs-3">        
                <?php $this->widget('application.components.PanelProfileLikeFromWidget', array('all'=>$all)); ?>
            </div>                
    </div>
    
<?php } else { ?>

    <div id="PanelProfileLikeTabs" class="tabs">
        <ul>
            <li class="tab-left">
                <a href="#PanelProfileLikeTabs-1">You Like</a>
                <a class="show-all" href="/profiles/likeAll">Show all &raquo;</a>
            </li>
            <li class="tab-right">
                <a href="#PanelProfileLikeTabs-2">Likes You</a>
                <a class="show-all" href="/profiles/likeAll">Show all &raquo;</a>
            </li>
        </ul>
        <div id="PanelProfileLikeTabs-1">        
            <?php $this->widget('application.components.PanelProfileLikeToWidget', array('all'=>$all)); ?>
        </div>
        <div id="PanelProfileLikeTabs-2">        
            <?php $this->widget('application.components.PanelProfileLikeFromWidget', array('all'=>$all)); ?>
        </div>                
    </div>
<?php } ?>





<script type="text/javascript">
    $(document).ready(function() 
    {
        $("#PanelProfileLikeTabs").tabs(/*{
            create: function() {
                $(this).tabs("select", "#PanelProfileLikeTabs-1");
            }
        }*/);
    })
</script>

