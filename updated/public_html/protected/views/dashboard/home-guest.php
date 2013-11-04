<?php 
	if (isset($_SERVER['GEOIP_CITY']))
		$city = $_SERVER['GEOIP_CITY'];
	else
	{
		/*$location_id = Yii::app()->location->findLocationIdByIP();        
		$location = Yii::app()->location->getLocation( $location_id );
		$city = $location['city'];*/
		$city = "";
	}
	
	if (!$city)
		$city = "Your Area";

?>


<div id="content" class="guest">
	<div class="guest-left">
		<h1>Meet someone tonight...</h1>
		<h2>Hooking up has never been easier.</h2>
	</div>
	<div class="guest-right <?php if ( Yii::app()->user->data('role')=='justjoined' ) { ?>step2<?php } ?>" >

<?php /*
		<?php 
	        $location_id = Yii::app()->location->findLocationIdByIP();        
	        $location = Yii::app()->location->getLocation( $location_id );		
		?>
		
		<h3 class="find-buddy clean">Hookup With Members In</h3>
		<h4><?php echo Yii::app()->location->getCountryName($location['country']) ?></h4>
*/ ?>

		<div class="guest-right-box">		
			
			<?php /*
			<div id="ribbon2"> </div>
			
			<div class="signup-header">
				<div class="get-laid clean"></div>
				<?php if ( Yii::app()->user->isGuest ) { ?>
					<p> Hookup With Members In <?php echo ( isset($location['city']) && $location['city']) ? $location['city'] : 'Your Area' ?></p>
				<?php } ?>
			</div>
			*/ ?>
			
			<?php /* <h1>Create your free profile</h1> */ ?>
			
			<div id="reg-forms-box">	
				<?php 
				if ( Yii::app()->user->isGuest ) { ?>
					<h1>Create your free profile</h1>
					<?php $this->widget('application.components.UserRegistrationFormWidget'); ?>
				<?php } else {
					$this->widget('application.components.UserRegistrationStep2FormWidget');
				} ?>
			</div>
		</div>
	</div>
</div>







