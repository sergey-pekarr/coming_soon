<div id="UserSettUpd" class="tabs all">
    <ul>
        <li class="tab-left">
            <a href="#editMySettings">Settings</a>
        </li>
        <li class="tab-right">
            <a href="#profileUpdates">Updates</a>
        </li>
    </ul>
    <div id="editMySettings" class="form">        
        <?php $this->widget('application.components.userprofile.UserSettingsFormWidget'); ?>
    </div>
    <div id="profileUpdates">        
        <?php $this->widget('application.components.userprofile.UserUpdatesWidget', array('profile'=>Yii::app()->user->Profile)); ?>
    </div>                
</div>

<script type="text/javascript">
    $(document).ready(function() 
    {
        $("#UserSettUpd").tabs(/*{
            create: function() {
                $(this).tabs("select", "#PanelProfileViewedTabs-1");
            }
        }*/);
    })
</script>
