<div id="PanelDashboardMatches">
    <div class="matches-list">
        <ul id="dashboard-matches-list">
            <?php if (!count($matches)) { ?>
                <li class="no-matches">
                    <p class="no-matches-message">You have no matches yet.</p>
                    <p class="no-matches-info-text">Start liking users now and if they like you back you will become a match.</p>
                    <p class="no-matches-search-link">
                        <a title="Start your search now!" href="/search">Search for your first match!</a>
                    </p>
<?php /*                     
                    <p class="no-matches-dashboard-photo-<?php echo (Yii::app()->user->data('gender')=='M') ? 'female' : 'male'; ?>"></p>
*/ ?>
                </li>
            <?php } else { 
            
                foreach ($matches as $m) 
                { 
                    $id = $m['id'];
                    $profile = new Profile($id);
                    ?>
                    <li>
                        <div class="image">
                            <a href="/profile/<?php echo Yii::app()->secur->encryptID($id) ?>">
                                <img src="<?php echo $profile->imgUrl() ?>" />
                                <span class="premium"></span>
                            </a>
                        </div>                
                        
                        <div class="match dashboard">
                            <div class="header">
                                <a class="popup viewProfile" href="/profile/<?php echo Yii::app()->secur->encryptID($id) ?>">
                                <span class="name"> <?php echo $profile->getDataValue('username') ?> </span>
                                </a>
                                <span class="info"> 
                                    <?php echo $profile->getDataValue('age') ?>, 
                                    <?php echo $profile->getDataValue('gender') ?>, 
                                    <?php echo Yii::app()->helperProfile->showProfileInfoSimple($profile, 2, 30); ?> 
                                </span>                            
                            </div>
                            <div class="content">
                                <p class="match-aditional-photos"><?php echo ($profile->getDataValue('pics')-1) ?> additional photo(s).</p>
                                <p class="match-days-ago">Matched <?php echo Yii::app()->helperDate->date_distanceOfTimeInWords(strtotime($m['when']), time()) ?> ago.</p>
                                <a class="popup viewProfile match-contact-match" href="/billing/upgrade">Contact <?php echo ($profile->getDataValue('gender')=='F') ? 'her' : 'his'; ?> now!</a>
                            </div>
                        </div>
                        <div class="match-footer"> </div>                    
                    </li>    
                <?php 
                }
                
            } ?>
        </ul>
    </div>
</div>

