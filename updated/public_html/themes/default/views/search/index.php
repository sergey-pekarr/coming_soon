<?php 
$compress  = (LOCAL) ? false : true;
$combine  = (LOCAL) ? false : true;
CHelperAssets::cssUrl('search', $compress, $combine); 
?>
<div class="search_wrap" id="search_result">
    <div id="advanced_search" style="overflow: hidden">
    <div class="content_tabs bround">
        <!--<input type="hidden" name="profile_id" value="<?php echo $profile->getDataValue('username'); ?>" class="profile_id">-->
        <input type="hidden" name="gender" value="<?php echo $profile->getDataValue('gender'); ?>" class="gender">
        <input type="hidden" name="gender_search" value="<?php echo $profile->getDataValue('looking_for_gender');  ?>" class="gender">
        <ul>
            <li class="active"><a href="#" title="option1" onclick="changeTab(this); return false;">Basic Info</a><span></span></li>
            <li><a href="#" title="option2" onclick="changeTab(this); return false;">Appearance</a><span></span></li>
            <li><a href="#" title="option3" onclick="changeTab(this); return false;">Character</a><span></span></li>
            <li><a href="#" title="option4" onclick="changeTab(this); return false;">Tastes</a><span></span></li>
            <li><a href="#" title="option5" onclick="changeTab(this); return false;">Habits</a><span></span></li>
        </ul>
        <div class="clear">
        </div>
        <div class="content_tabs_wrap">
            <div class="content_tabs_box" id="option1" style="overflow: hidden">
                <?php include dirname(__FILE__).'/ui/option1.php'; ?>                
            </div>
            <div class="content_tabs_box" id="option2" style="overflow: hidden">
                <?php include dirname(__FILE__).'/ui/option2.php'; ?>                   
            </div>
            <div class="content_tabs_box" id="option3" style="overflow: hidden">
                <?php include dirname(__FILE__).'/ui/option3.php'; ?> 
            </div>
            <div class="content_tabs_box" id="option4" style="overflow: hidden">
                <?php include dirname(__FILE__).'/ui/option4.php'; ?> 
            </div>
            <div class="content_tabs_box" id="option5">
                <?php include dirname(__FILE__).'/ui/option5.php'; ?> 
            </div>
            <hr />
            <div class="clear" style="height: 10px">
            </div>
            <?php //Note: We hide selected conditions from user's view, searchcondition is used to store condition ?>
            <form action="/search/result" onsubmit="" method="post">
                    <input type="hidden" name="searchcondition" value="" />
                    <a href="#" id="results" class="content_button" onclick="showResult();" style="width: auto;">Show Results<span><img
                        class="iconForward" src="/images/img/blank.gif" alt="" /></span></a>
            </form>
            <div class="clear"></div>
          </div>
    </div>
    </div>
    

</div>
<div class="search_filter">
<?php include dirname(__FILE__).'/ui/save.php'; ?>
</div>
<div class="clear"></div>


<?php CHelperAssets::jsUrl('search', $compress, $combine);  ?>

