<?php if (!$ajax) { ?>

    <div class="container simple">
        
        <div id="ProfileLikeFromBox"></div> 
        
        <script type="text/javascript">
            $(document).ready(function()
            {
                loadPanel("ProfileLikeFrom",0,<?php echo ($all) ? '1' : '0' ?>);
            })
        </script>
        
    </div>

<?php } else { ?>

            <ul class="profiles">
            <?php 
                foreach ($profiles as $r) 
                {
                    echo '<li>';
                    $this->widget('application.components.ProfileBoxWidget', array('id'=>$r['id'], 'imgSize'=>(($all) ? 'big' : 'medium'), 'infoType'=>2));
                    echo '</li>';
                }            
            ?>
            </ul>
            
            <?php $this->widget(
                'AjaxLinkPager', 
                array(
                    'pages' => $pages,
                    'all' => $all,
                    'currentPage'=>$pages->getCurrentPage(false),
                    'panel'=>'ProfileLikeFrom',
                    'id'=>'ProfileLikeFromPagination',
                )
            );?>            

    
<?php } ?>
