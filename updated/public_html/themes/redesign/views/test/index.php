<?php
$this->widget( 
    'application.extensions.uploadify.UploadifyWidget', 
    array(
        'fileExt'=>VIDEO_FORMATS, 
        'callbackUrl'=>'/video/upload/'.Yii::app()->secur->encryptID( Yii::app()->user->id ),
    ) 
);