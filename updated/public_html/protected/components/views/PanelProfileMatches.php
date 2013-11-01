<?php if (!$ajax) { ?>

    <div class="container simple">
        
        <div id="ProfileMatchesBox"></div> 
        
        <script type="text/javascript">
            $(document).ready(function()
            {
                loadPanel("ProfileMatches",0,<?php echo ($all) ? '1' : '0' ?>);
            })
        </script>
        
    </div>

<?php } else { ?>

            <ul class="profiles">
            <?php 
                foreach ($profiles as $r) 
                {
                    $id = $r['id'];
                    echo '<li>';
                    $this->widget('application.components.ProfileBoxWidget', array('id'=>$id, 'imgSize'=>(($all) ? 'big' : 'medium'), 'infoType'=>2));
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
                    'panel'=>'ProfileMatches',
                    'id'=>'ProfileMatchesPagination',
                )
            );?>            

    
<?php } ?>
