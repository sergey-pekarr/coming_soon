<?php if (!$ajax) { ?>

    <div class="container simple <?php if ($all) echo 'all' ?>">

        <?php if (!$all) { ?>
            <div class="header">
                <h3 class="title">New Members</h3>
                <a class="show-all" href="/profiles/NewMembersAll">Show all &raquo;</a>
            </div>        
        <?php } else { ?>
            <h1>New Members</h1>
        <?php } ?>    
        
        <div id="NewMembersBox"></div> 
        
        <script type="text/javascript">
            $(document).ready(function() 
            {
                loadPanel("NewMembers",0,<?php echo ($all) ? '1' : '0' ?>);
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
                    'panel'=>'NewMembers',
                    'id'=>'NewMembersPagination',
                )
            );?>            

    
<?php } ?>