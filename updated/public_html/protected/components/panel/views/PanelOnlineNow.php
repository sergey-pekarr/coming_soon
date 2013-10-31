<?php if (!$this->ajax) { ?>
        <div class="<?php //if ($this->all) echo 'all' ?>">
            
            <?php /* if (!$this->all) { ?>
            <div class="header">
                <h3 class="title">Online Now</h3>
                <a class="show-all" href="/profiles/OnlineNowAll">Show all &raquo;</a>
            </div>
            <?php } else */ { ?>
                <h1>Online Now</h1>
            <?php } ?>
            
            <div id="OnlineNowBox"></div> 
            
            <script type="text/javascript">
                $(document).ready(function() 
                {
                    loadPanel("OnlineNow",<?php echo $this->page ?>,<?php echo ($this->all) ? '1' : '0' ?>);
                })
            </script>
            
        </div>
<?php } else { ?>

            <div class="profiles panel-list">
            <?php 
            if ($profiles)    
            	foreach ($profiles as $k=>$r) 
                {
                    $class = ($k%5) ? "" : "last";
					
                    $this->widget('application.components.userprofile.ProfileBoxWidget', array('id'=>$r['id'], 'imgSize'=>'medium'/*(($this->all) ? 'big' : 'medium')*/, 'infoType'=>11, 'class'=>$class));

                }            
            ?>
            </div>
            
            <div class="clear"></div>
            
            <?php $this->widget(
                'AjaxLinkPager', 
                array(
                    'pages' => $pages,
                    'all' => $this->all,                    
                    'currentPage'=>$pages->getCurrentPage(false),
                    'panel'=>'OnlineNow',
                    'id'=>'OnlineNowPagination',
                )
            );?>            

    
<?php } ?>
