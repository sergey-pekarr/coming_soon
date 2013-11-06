<div class="left-side">
        <?php $this->widget('application.components.dashboard.PanelDashboardWidget'); ?>       
</div>
<div class="right-side">
        
        <?php 
        if (Yii::app()->user->data('gender')=='M')
        {
            $this->widget('application.components.PanelProfileViewedWidget');
            $this->widget('application.components.PanelProfileLikeWidget');
            $this->widget('application.components.PanelOnlineNowWidget');
            $this->widget('application.components.PanelNewMembersWidget');             
        }
        else
        {
            $this->widget('application.components.PanelOnlineNowWidget');
            $this->widget('application.components.PanelNewMembersWidget');
            $this->widget('application.components.PanelProfileViewedWidget');
            $this->widget('application.components.PanelProfileLikeWidget');                        
        }

        ?>
</div>


