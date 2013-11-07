<div style="padding-bottom: 15px;">
    <p>
        
            Below you will find a list of meetsi.com members who have requested to see more
            photos of you. Why not <a href="/profile/editphotos">upload a new photo</a> now?
    </p>
    <br>
    <p></p>
</div>
<div class="search_results">
    <?php 
    function customeDataMethod($item){
    	return "Requested: " . date('m/d/Y', strtotime($item['added']));
    }
    include dirname(__FILE__).'/userpanel.php'; ?>
</div>
