<?php $value = $profile->getPersonalValue('for_fun');
if($value && trim($value) != '') { ?>
<h3>
    What do you do for fun?</h3>
<p>
<?php echo $value ?></p>
<?php } ?>

<?php $value = $profile->getPersonalValue('destination');
if($value && trim($value) != '') { ?>
<h3>
    Favourite local hot spots or travel destinations?
</h3>
<p>
<?php echo $value ?></p>
<?php } ?>

<?php $value = $profile->getPersonalValue('favourite_things');
if($value && trim($value) != '') { ?>
<h3>
    Favourite Sexual Experience?
</h3>
<p>
<?php echo $value ?></p>
<?php } ?>

<?php $value = $profile->getPersonalValue('favourite_book');
if($value && trim($value) != '') { ?>
<h3>
    Favourite Sex Toy?
</h3>
<p>
<?php echo $value ?></p>
<?php } ?>

<?php $value = $profile->getPersonalValue('job');
if($value && trim($value) != '') { ?>
<h3>
    Tell us more about your job
</h3>
<p>
<?php echo $value ?></p>
<?php } ?>
