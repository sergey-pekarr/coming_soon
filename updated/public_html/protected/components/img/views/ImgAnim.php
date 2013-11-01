<?php
if ($profile->imgHave($i))
{
$imgId = "animImg_".rand(1111,9999).'_'.$i;
?>
    
    <img 
        id="<?php echo $imgId ?>"
        onmouseover="javascript: showAnim('<?php echo $imgId ?>', 1)" 
        onmouseout="javascript:stopAnim('<?php echo $imgId ?>')"
        src="<?php echo $profile->imgUrl('152x86',$i, false) ?>" 
    />
    
    <script type="text/javascript">
        
        animImages["<?php echo $imgId ?>"] = new Array(
            <?php for ($animN=1; $animN<=VIDEO_SCREENSHOTS_COUNT; $animN++) { ?>
                "<?php echo $profile->imgUrl('152x86', $i, false, $animN) ?>"
                <?php if ($animN!=VIDEO_SCREENSHOTS_COUNT) echo "," ?>
            <?php } ?>        
        );
        
        $(document).ready(function()
        {
            <?php for ($animN=1; $animN<=VIDEO_SCREENSHOTS_COUNT; $animN++) { ?>
                $.preloadImages("<?php echo $profile->imgUrl('152x86', $i, false, $animN) ?>");
            <?php } ?>
        });
    </script>
    
<?php } else { ?> 

    <img src="<?php echo $profile->imgUrl('152x86',$i, false) ?>" />

<?php } 