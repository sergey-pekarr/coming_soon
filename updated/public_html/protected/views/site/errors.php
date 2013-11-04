<?php
$this->pageTitle=Yii::app()->params['site']['nameFull'] . ' - Error';
$this->breadcrumbs=array(
	'Error',
);


$mes = (Yii::app()->user->hasFlash('errorCustom')) ? Yii::app()->user->getFlash('errorCustom') : "";
Yii::app()->user->setFlash('errorCustom',$mes);//if user refreshed page
?>



<div class="errors" style="width: 400px; height:400px; margin: 0 auto">

<h2>Error</h2>

<?php if(Yii::app()->user->hasFlash('errorCustom')) { ?>

	<div class="alert alert-error">
    	<p class="bold"><?php echo $mes ?></p>
    </div>
        
    
<?php } ?>

</div>