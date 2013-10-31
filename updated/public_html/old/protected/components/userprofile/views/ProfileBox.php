<div class="profileBox <?php echo $this->class ?> ">
    <?php 
		$id = $profile->getDataValue('id');
		$encid = Yii::app()->secur->encryptID($id);
		//$gold = Yii::app()->user->checkAccess('gold');
  //      if(!$gold) $favlink = "doRequestMember('favourite')";
  //      else $favlink = "doAction('favourite', '$encid', this)";
        if ($profile)
        {
    	if(!CHelperProfile::getPaymentLinkWithAction('sendfavourite', $id, $favlink)){
    		$favlink = "doAction('favourite', '$encid', this)";
    	}
    	if(!CHelperProfile::getPaymentLinkWithAction('sendmessage', $id, $emaillink)){
    		$emaillink = "window.location = '/thread/$encid'";
    	}
    if ($this->showIcons) { ?>
	
	<div class="icons">		
					<a title="Message" onclick="<?php echo $emaillink; ?>; return false;" href="#">
						<img class="iconEmail" alt="" src="/images/img/blank.gif">
					</a>
					<a title="Wink" href="#" onclick="doAction('winks', '<?php echo $encid; ?>', this); return false;">
						<img class="iconWinks" alt="Winks icon" src="/images/img/blank.gif">
					</a>
					<a title="Favourite" href="#" onclick="<?php echo $favlink; ?>; return false;">
						<img class="iconHeart" alt="Heart icon" src="/images/img/blank.gif">
					</a>        		
        		</div>
        		
        	<?php }
        else if($this->showBlockIcon){ ?>
			    <div class="icons">				
					<a title="unblock" onclick="doAction('unblock', '<?php echo $encid; ?>' , this); return false;" href="#">
					<img class="iconunblock " alt="unblock  icon" src="/images/img/blank.gif"></a>      		
        		</div>
		<?php
        }
        	//if ( !$forcePayment || Yii::app()->user->checkAccess('gold') )
        		$profileLink = '/profile/'.$encid;
            //else
                //$profileLink = Yii::app()->createAbsoluteUrl('/site/registration');
            
            ?>
            <a href="<?php echo $profileLink ?>">
            	<img src="<?php echo $profile->imgUrl($this->imgSize) ?>" />
            </a>            
            
            
            <div class="profile-info">
            	<a href="<?php echo $profileLink ?>">
            		<?php 
	                $truncCount = 12;//($profile->getDataValue('isOnline')) ? 8 : 11;
	                echo Yii::app()->helperProfile->truncName( $profile->getDataValue('username'), $truncCount );
            		?>
            	</a>,
            	<span class="info"><?php echo CHelperProfile::showProfileInfoSimple($profile, $infoType) ?></span>
            	<?php if(isset($this->showLocation) && $this->showLocation){
            		echo "<br><span>".$profile->getLocationValue('city').'</span>'.
            			"<br><span>".CHelperProfile::showProfileInfoSimple($profile,14,30).'</span>';
            	} else if(!isset($this->showLocation) || $this->showLocation === null) {					
            		echo "<br><span>".CHelperProfile::showProfileInfoSimple($profile,2,22).'</span>';					
            	}?>
            	<?php if(isset($this->showOnlineStatus) && $this->showOnlineStatus){
            		$isonline = $profile->getDataValue('isOnline');
					$class = $isonline?'online':'offline';
					$text = $isonline?'online':'offline';
            		echo "<br><a class='$class' href='$profileLink'></a>$text";
            	} ?>
				<?php if(isset($this->custom) && $this->custom != ''){
					echo "<br><span>{$this->custom}</span>";
				} ?>

			</div>
            
            		
            		
            		
            		
            		
            		
            		
            		
            		
            		
            		
<?php 
/*        	
        	
            //if ( !$forcePayment || Yii::app()->user->checkAccess('gold') )
                $profileLink = '/profile/'.Yii::app()->secur->encryptID($profile->getDataValue('id'));
            //else
                //$profileLink = Yii::app()->createAbsoluteUrl('/site/registration');
            
            if ($infoType) echo '<a href="'.$profileLink.'">';
            
            echo '<img src="'.$profile->imgUrl($this->imgSize).'" />';
                
            if ($infoType) echo '</a>';
            
            echo '<br />';
            
            //if ($infoType && $infoType!=8)
            {
                echo '<a class="name" href="'.$profileLink.'">';
                $truncCount = ($profile->getDataValue('isOnline')) ? 8 : 11;
                echo Yii::app()->helperProfile->truncName( $profile->getDataValue('username'), $truncCount );
                if ($infoType!=5 && $infoType!=6)
                    echo '<br />';
                else
                    echo '&nbsp;&nbsp;';
                echo '<span class="info">'.CHelperProfile::showProfileInfoSimple($profile, $infoType).'</span>';
                
                if ($profile->getDataValue('isOnline')) 
                    echo '&nbsp;&nbsp;<span class="online">&nbsp;</span>';
                    
                echo '</a>';                
            }

*/            
        }
        else
        {
            if (Yii::app()->user->Profile->getDataValue('gender')=='F')
                echo '<img src="/images/img/nophoto_male_'.$this->imgSize.'.jpg" />';
            else
                echo '<img src="/images/img/nophoto_female_'.$this->imgSize.'.jpg" />';  
            
            echo '<div class="name"></div>'; 
        }            
    ?>
</div>
