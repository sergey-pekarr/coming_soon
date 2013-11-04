<form id="profile-more-form">
<label class="for_fun" for="for_fun">
    What do you do for fun?&nbsp;<span></span></label>
	<textarea id="for_fun" name="for_fun"><?php echo $profile->getPersonalValue('for_fun'); ?></textarea>
<div class="clear">
</div>
<label class="destinations" for="destinations">
    Favourite local hot spots or travel destinations?&nbsp;<span></span></label>
	<textarea id="destination" name="destination"><?php echo $profile->getPersonalValue('destination'); ?></textarea>
<div class="clear">
</div>
<label class="favourite_things" for="favourite_things">
    Favourite Sexual Experience?&nbsp;<span></span></label>
	<textarea id="favourite_things" name="favourite_things"><?php echo $profile->getPersonalValue('favourite_things'); ?></textarea>
<div class="clear">
</div>
<label class="favourite_book" for="favourite_book">
    Favourite Sex Toy?&nbsp;<span></span></label>
	<textarea id="favourite_book" name="favourite_book"><?php echo $profile->getPersonalValue('favourite_book'); ?></textarea>
<div class="clear">
</div>
<label class="job" for="job">
    Tell us more about your job&nbsp;<span></span></label>
	<textarea id="job" name="job"><?php echo $profile->getPersonalValue('job'); ?></textarea>
<hr>
        <?php $this->widget('application.components.common.FlirtButtonWidget', array('type'=>'Save' ,'text'=>'Save Changes', 'action'=>'savefavourite(); return false;', 'profileid'=>$profile->getId())); ?>
<div class="clear"></div>
</form>



