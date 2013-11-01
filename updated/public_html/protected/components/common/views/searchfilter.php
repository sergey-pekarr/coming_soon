<style type="text/css">    
    .search_filter
    {
        width: 202px;
        float: left;
		margin-bottom:16px;
    }
    .search_filter h1
    {
        background: url("/images/img/control.png") no-repeat 0px -319px;
        padding: 0px 0px 9px 10px;
        height: 21px;
        color: rgb(255, 255, 255);
        position: relative;
        text-shadow: #1872b7 1px 1px;
        background: url("/images/img/pinkmeets/body_vertical.png") repeat-x 0px -42px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        -webkit-border-top-left-radius: 5px;
        -webkit-border-top-right-radius: 5px;
        -moz-border-radius-topleft: 5px;
        -moz-border-radius-topright: 5px;
    }
    .search_filter_box
    {
        border-width: 0px 1px 1px;
        border-style: none solid solid;
        border-color: currentColor rgb(221, 218, 210) rgb(221, 218, 210);
        padding: 10px 10px 4px;
        font-weight: bold;
        background-color: rgb(242, 242, 242);
    }
    .search_filter .content_button
    {
        height: 25px !important;
        float: right;
        margin-right: 20px;
    }
	.search_filter .forminput
	{
		background: url("/images/img/control.png") no-repeat -1px -292px;
		padding: 5px;
		border: 0px currentColor;
		width: 175px;
		height: 17px;
	}
	.search_filter select.forminput
	{
		width: 184px;
		height: 27px;
	}
</style>
<div class="search_filter">
    <h1>
        FILTER RESULTS<span></span></h1>
    <form id="filter" onsubmit="" method="post" action="/search/result">
    <div class="bround">
        <div class="search_filter_box">
            Age <span class="age_display">18 - 99</span>
            <input class="age_from" name="age" value="18" type="hidden" autocomplete="off">
            <input class="age_to" name="maxage" value="99" type="hidden" autocomplete="off">
            <div class="slider_wrap">
                <div class="age_slider">
                </div>
            </div>
            Location:
            <input id="locationInput2" class="forminput location" disabled="disabled" name="location"
                value="<?php echo $location; ?>" type="text">
            Username:
            <input class="forminput username" name="username" value="<?php echo $username ?>" type="text">
            Within: <span class="radius_display">50 Miles</span>
            <input class="radius" name="radius" value="50" type="hidden" autocomplete="off">
            <div class="slider_wrap">
                <div class="radius_slider">
                </div>
            </div>
            <input id="has_photo" class="has_photo" name="has_photo" value="1" type="checkbox">
            Photo Only?
            <input name="filter" value="true" type="hidden">
            <div class="clear"></div>
            <a id="filter_button" class="content_button " href="javascript:void();" onclick="$('#filter').submit(); return false;" style="width:40px;">Filter<span><img class="iconForward"
                alt="" src="/images/img/blank.gif"></span></a>
            <div class="clear">
            </div>
        </div>
    </div>
    </form>
</div>
<script>
$(document).ready(function () {
    rangeSlider('filter', 'age', 18, 99);
    slider('filter', 'radius', 5, 100, ' Miles');
    $('#filter .age_slider').slider("values", 0, <?php echo $age ?>);
    $('#filter .age_slider').slider("values", 1, <?php echo $maxage ?>);
    $('#filter .radius_slider').slider("value", <?php echo $radius ?>);
	<?php if($has_photo) { ?>
		$('#filter #has_photo').attr('checked', 'checked');
	<?php } ?>
});
</script>
</script>