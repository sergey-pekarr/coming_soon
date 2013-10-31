<div class="box_rs1" style="margin-top: 10px;">
    
    
    <?php $profile = new Profile($id_to) ?>
    <h3 style="padding-left: 40px;">Conversation with <?php echo $profile->getDataValue('username') ?></h3>
    
    <?php if ($pages->getCurrentPage()==0) 
        $this->widget('application.components.message.SendFormWidget', array('profile'=>$profile, 'reply'=>true));
    ?>    
    
    <?php if ($messages['list']) {
        
        foreach ($messages['list'] as $m)
            $this->widget('application.components.message.AllBoxWidget', array('m'=>$m));
            
    } ?>
    
    <div class="clear"></div>
    
    <?php 

        $this->widget(
            'CLinkPager', 
            array(
                'pages' => $pages,
                'currentPage'=>$pages->getCurrentPage(),//(false)
                'header'=>'',
                'maxButtonCount'=>5,
                'htmlOptions'=>array('class'=>'messageAllPagination pagination'),                        
            )
    );?>
    <div class="clear"></div>

</div>





