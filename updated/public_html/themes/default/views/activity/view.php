<div style="padding-bottom: 15px;">
    <p>
Below you will find a list of pinkmeets.com members who have viewed your profile in last 7 days. 
We suggest that you take the time to look through each user and add potential matches to 'My Favourites'.
    </p>
    <br>
    <p>
        This will allow you to locate your potential matches profile and contact them with
        ease.</p>
</div>
<style>
.search_results .profileBox
{
	height: 174px;
}
</style>
<div class="search_results">
    <?php 
    function customeDataMethod($item){
    	return "Viewed You: {$item['count']} times"."<br>Last: " . date('m/d/Y', strtotime($item['added']));
    }
	include dirname(__FILE__).'/userpanel.php'; ?>
</div>
