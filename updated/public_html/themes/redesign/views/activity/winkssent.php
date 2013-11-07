<div style="padding-bottom: 15px;">
        <p>
            Here you can view winks that you have sent to other meetsi.com members in date order. If you haven't yet heard from them, why wait? <br>
			Select the icon <img class="iconWinks inline" alt="Winks inline icon" src="/images/img/blank.gif"> and get in contact! Don't miss the chance to hook up with our horny members! 

        </p>
        <br>
</div>
<div class="search_results">
    <?php 
    function customeDataMethod($item){
    	return "Last Wink: " . date('m/d/Y', strtotime($item['added']));
    }
    include dirname(__FILE__).'/userpanel.php'; ?>
</div>
