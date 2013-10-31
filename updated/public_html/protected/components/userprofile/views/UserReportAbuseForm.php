<span class="userReport" class="form">

    <a href="javascript:void(0)" onclick="javascript:reportExpand(this);">Report</a>

    <span class="reportActions">
    Are you sure?
    <button 
            class="btn success" 
            data-loading-text='Saving...'
            data-success-text='Saved'
            onclick="javascript:reportSend(this, '<?php echo Yii::app()->secur->encryptID($id) ?>');" 
    >Yes</button>
    <button 
            class="btn danger" 
            data-loading-text='Saving...'
            data-success-text='Saved'
            onclick="javascript:reportClose(this);" 
    >No</button>        
	</span>

</span>