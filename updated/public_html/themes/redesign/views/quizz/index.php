<style>
</style>

    <div class="quizzseach">
        <label>
            Search by name or author:</label><input name="testsearch" type="text" style="width: 330px;" value="" /><input
                type="button" value="Search" onclick="TestList.search();" style="" /><br />
        <label>
            Order by:</label>
        <input type="radio" name="testorder" value="1" /><span>24 hours takers</span>
        <input type="radio" name="testorder" value="2" /><span>24 hours rank</span>
        <input type="radio" name="testorder" value="3" checked="checked" /><span>All time takers</span>
        <input type="radio" name="testorder" value="4" /><span>All time rank</span>
        <div class="clear"></div>
    </div>
    <div class="quizzlist">
    </div>
	<script>
		$(document).ready(function(){
			window.setTimeout(function(){
				TestList.search();
			}, 1000);
		});
	</script>
