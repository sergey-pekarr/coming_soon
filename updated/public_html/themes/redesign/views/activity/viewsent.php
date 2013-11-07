<div style="padding-bottom: 15px;">
        <p>

            Below you will find a list of the last 50 meetsi.com members you have viewed.
            We suggest that you take the time to look through each user and add potential matches
            to 'My Favourites'.
        </p>
        <br>
</div>
<div class="search_results">
    <?php 
    function customeDataMethod($item){
    	return "Last: " . date('m/d/Y', strtotime($item['added']));
    }
    include dirname(__FILE__).'/userpanel.php'; ?>
</div>
