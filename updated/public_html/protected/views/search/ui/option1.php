<div class="left">
    Between the ages of: <span class="age_display">18 - 99</span>
</div>
<div class="right">
    <input type="hidden" name="age" value="18" class="age_from" autocomplete="off">
    <input type="hidden" name="maxage" value="99" class="age_to" autocomplete="off">
    <div class="slider_wrap">
        <div class="age_slider">
        </div>
    </div>
</div>
<hr />

<div class="left">
    Username:
</div>
<div class="right">
    <input type="text" name="username" value="" class="forminput username" />
</div>
<hr />
<div class="left" id="location-search">
    Location:
</div>
<div class="right">
    <input id="locationInput" type="text" name="location" value="<?php echo CHelperProfile::showProfileInfoSimple($profile,  12); ?>"
        class="forminput location" autocomplete="off" disabled="disabled" />
    <div id="locationSuggest">
    </div>
</div>
<div class="clear">
</div>
<div class="left">
    Within: <span class="radius_display">50 Miles</span>
</div>
<div class="right">
    <input type="hidden" name="radius" value="50" class="radius" autocomplete="off">
    <div class="slider_wrap">
        <div class="radius_slider">
        </div>
    </div>
</div>
<hr />

<script>
$(document).ready(function () {
    rangeSlider('advanced_search', 'age', 18, 99);
    slider('advanced_search', 'radius', 5, 100, ' Miles');
});
</script>

<div class="left">
    Relationship Status:
</div>
<div class="right">
    <label class="relationship_status[1]" for="relationship_status[1]">
        <input id="relationship_status[1]" class="checkbox " name="relationship_status" value="1"
            type="checkbox">&nbsp;Currently Separated &nbsp;<span></span></label>
    <label class="relationship_status[2]" for="relationship_status[2]">
        <input id="relationship_status[2]" class="checkbox " name="relationship_status" value="2"
            type="checkbox">&nbsp;Divorced&nbsp;<span></span></label>
    <label class="relationship_status[3]" for="relationship_status[3]">
        <input id="relationship_status[3]" class="checkbox " name="relationship_status" value="3"
            type="checkbox">&nbsp;Widowed&nbsp;<span></span></label>
    <label class="relationship_status[4]" for="relationship_status[4]">
        <input id="relationship_status[4]" class="checkbox " name="relationship_status" value="4"
            type="checkbox">&nbsp;Single&nbsp;<span></span></label>
    <label class="relationship_status[5]" for="relationship_status[5]">
        <input id="relationship_status[5]" class="checkbox " name="relationship_status" value="5"
            type="checkbox">&nbsp;Married&nbsp;<span></span></label>
    <label class="relationship_status[6]" for="relationship_status[6]">
        <input id="relationship_status[6]" class="checkbox " name="relationship_status" value="6"
            type="checkbox">&nbsp;In A Relationship&nbsp;<span></span></label>
</div>
<hr />
<div class="left">
    Living:
</div>
<div class="right">
    <label class="i_live_[1]" for="i_live_[1]">
        <input id="i_live_[1]" class="checkbox " name="live" value="1" type="checkbox">&nbsp;Live
        Alone&nbsp;<span></span></label>
    <label class="i_live_[2]" for="i_live_[2]">
        <input id="i_live_[2]" class="checkbox " name="live" value="2" type="checkbox">&nbsp;Live
        With Kids&nbsp;<span></span></label>
    <label class="i_live_[3]" for="i_live_[3]">
        <input id="i_live_[3]" class="checkbox " name="live" value="3" type="checkbox">&nbsp;Live
        With Parents&nbsp;<span></span></label>
    <label class="i_live_[4]" for="i_live_[4]">
        <input id="i_live_[4]" class="checkbox " name="live" value="4" type="checkbox">&nbsp;Live
        With Roommate(s)&nbsp;<span></span></label>
    <label class="i_live_[5]" for="i_live_[5]">
        <input id="i_live_[5]" class="checkbox " name="live" value="5" type="checkbox">&nbsp;Live
        With Partner&nbsp;<span></span></label>
</div>
<hr />
<div class="left">
    Looking For:
</div>
<div class="right">
    <label class="looking_for[1]" for="looking_for[1]">
        <input id="looking_for[1]" class="checkbox " name="looking_for" value="1" type="checkbox">&nbsp;Sexual&nbsp;<span></span></label>
    <label class="looking_for[2]" for="looking_for[2]">
        <input id="looking_for[2]" class="checkbox " name="looking_for" value="2" type="checkbox">&nbsp;Encounter&nbsp;<span></span></label>
    <label class="looking_for[3]" for="looking_for[3]">
        <input id="looking_for[3]" class="checkbox " name="looking_for" value="3" type="checkbox">&nbsp;Threesome/Group&nbsp;<span></span></label>
    <label class="looking_for[4]" for="looking_for[4]">
        <input id="looking_for[4]" class="checkbox " name="looking_for" value="4" type="checkbox">&nbsp;Talk/Email&nbsp;<span></span></label>
    <label class="looking_for[5]" for="looking_for[5]">
        <input id="looking_for[5]" class="checkbox " name="looking_for" value="5" type="checkbox">&nbsp;Webcam/Flirt/Pics&nbsp;<span></span></label>
    <label class="looking_for[6]" for="looking_for[6]">
        <input id="looking_for[6]" class="checkbox " name="looking_for" value="6" type="checkbox">&nbsp;Open
        To Relationship&nbsp;<span></span></label>
</div>
<hr />
<div class="left">
    Photo Only:
</div>
<div class="right">
    <input type="checkbox" name="has_photo" value="1" checked="checked" class="has_photo"
        id="has_photo" />
</div>
<div class="clear">
</div>
<a href="#" onclick="changeTab('a[title=\'option2\']'); return false;" class="content_button"
    style="width: auto;">Search Appearance<span><img class="iconForward" src="/images/img/blank.gif"
        alt="" /></span></a>