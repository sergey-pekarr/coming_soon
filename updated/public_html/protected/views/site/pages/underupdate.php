<?php

if (!SITE_UNDER_UPDATE)
{
    $this->redirect(Yii::app()->createAbsoluteUrl('site/index'));
}

$this->layout='//layouts/ajax';
?>

<div style="text-align: center; padding-top: 10px;">
	<img alt="meetsi.com" src="/images/design/logoBigTv.png" />
    <h1 style="color: #555; margin-top: 30px; font-family: arial, sans-serif;">Coming soon</h1>
</div>