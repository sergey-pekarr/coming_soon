<div style="padding-bottom: 15px;">
    <p>
        
            Below you will find a list of meetsi.com members who have liked your photo(s).
            We suggest that you take the time to look through each user and add potential matches
            to 'My Favourites'.
    </p>
    <br>
    <p>
        This will allow you to locate your potential matches profile and contact them with
        ease.</p>
</div>
<div class="search_results">
    <?php 
    function customeDataMethod($item){
    	return "Like On: " . date('m/d/Y', strtotime($item['added']));
    }
    include dirname(__FILE__).'/userpanel.php'; ?>
</div>
