<div id="save_wrap">
    <h1>
        SAVE SEARCH<span></span></h1>
    <form action="" onsubmit="return false" id="filter">
    <div class="bround">
        <div class="search_filter_box">
            Name:
            <input type="text" name="searchName" id="searchName" class="forminput" value="" autocomplete="off" />
            <a href="#" id="remove" style="float: left; margin: 0 27px 10px 0px; width: 44px;"
                class="content_button">Remove<span><img class="iconX" src="/images/img/blank.gif"
                    alt="" /></span></a> <a href="#" id="save" class="content_button" style="width: 27px;">
                        Save<span><img class="iconDisc" src="/images/img/blank.gif" alt="" /></span></a>
            <div class="clear">
            </div>
            Load Search:
            <select name="searches" id="searches" class="forminput">
                <option value="0">Select One</option>
            </select>
            <a href="#" id="load" class="content_button" style="width: 40px;">Load<span><img
                class="iconForward" src="/images/img/blank.gif" alt="" /></span></a>
            <div class="clear">
            </div>
        </div>
    </div>
    </form>
</div>

<script>
var search_forms = <?php echo json_encode($condition); ?>;
$(document).ready(function () {
    $('#load').click(loadSearch);
    $('#save').click(saveSearch);
    $('#remove').click(removeSearch);
    loadSearchCondition();
});
</script>