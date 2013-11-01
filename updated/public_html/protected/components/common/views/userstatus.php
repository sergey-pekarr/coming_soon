
<div id="mood_status" class="mood_status">
    <img id="current_mood_icon" class="current_mood_icon" src="<?php echo $icon; ?>">
    <p>
        <?php echo $title; ?></p>
    <h4 id="current_mood" onclick="<?php if($selection) echo 'showMoodSelection(this);'; ?>" class="<?php echo $class; ?>">
        <span><?php echo $text; ?></span>
		<?php if($selection) { ?>
			<img src="/images/img/mood/arrow.png">
		<?php } ?>
	</h4>
</div>
<?php if($selection) { ?>
<div style="margin: -15px 0px 0px 9px; display: none;" id="mood_selection" class="mood_selection">
    <div onclick="MoodUpdate('1','Excited')">
        <p>
            Excited</p>
        <img src="/images/img/mood/thumb_1.png">
        <div class="clear">
            &nbsp;</div>
    </div>
    <div onclick="MoodUpdate('2','Loved Up')">
        <p>
            Loved Up</p>
        <img src="/images/img/mood/thumb_2.png">
        <div class="clear">
            &nbsp;</div>
    </div>
    <div onclick="MoodUpdate('3','Angry')">
        <p>
            Angry</p>
        <img src="/images/img/mood/thumb_3.png">
        <div class="clear">
            &nbsp;</div>
    </div>
    <div onclick="MoodUpdate('4','Sad')">
        <p>
            Sad</p>
        <img src="/images/img/mood/thumb_4.png">
        <div class="clear">
            &nbsp;</div>
    </div>
    <div onclick="MoodUpdate('5','Happy')">
        <p>
            Happy</p>
        <img src="/images/img/mood/thumb_5.png">
        <div class="clear">
            &nbsp;</div>
    </div>
</div>
<?php } ?>
