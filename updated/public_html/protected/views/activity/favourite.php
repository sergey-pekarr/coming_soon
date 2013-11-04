<div style="padding-bottom: 15px;">
    <p>
            Below you will find a list of pinkmeets.com members who have Favourite you
    </p>
    <br>
    <p></p>
</div>
<div class="search_results">
    <?php 
    function customeDataMethod($item){
    	return "Favour at: " . date('m/d/Y', strtotime($item['added']));
    }
    include dirname(__FILE__).'/userpanel.php'; ?>
</div>
