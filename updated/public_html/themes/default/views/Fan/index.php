<style>
	.intro
	{
		font-size: 13px; 
		font-weight: normal; 
		margin-top: 0px; 
		margin-bottom: 5px;
	}
	table.summary-table tr td
	{
		font-size: 13px; 
		font-weight: normal; 
		margin-top: 0px; 
		margin-bottom: 5px;
	}
	table.report-table tr td, table.payout-table tr td
	{
		font-size: 13px; 
		font-weight: normal; 
		margin-top: 0px; 
		margin-bottom: 5px;
		padding: 5px;
	}
	table.payout-table tr td
	{
		text-align: center;
	}
	table.payout-table tr td:last-child
	{
		text-align: left;
	}
	table.report-table tr td:first-child, table.report-table tr td:nth-child(3)
	{
		text-align: center;
	}
	table.border-table tr td
	{
		border-top: solid 1px #ccc;
		border-left: solid 1px #ccc;
	}
	table.border-table tr td:last-child
	{
		border-right: solid 1px #ccc;
	}
	table.border-table tr:last-child td
	{
		border-bottom: solid 1px #ccc;
	}
	table.border-table thead
	{
		background-color: #eee;
	}
	table.border-table thead tr td
	{
		text-align: cener;
		font-weight: bold;
	}
	#content .ftitle
	{
		font-size: 15px;
		font-weight: bold; 
	}
	#payoutForm table
	{
	    width: 500px;
	}
	#payoutForm table tr td:first-child
	{
	    width: 180px;
	}
	
	#payoutForm > div
	{
	    border: solid 1px #ccc;
	    margin: 0px 0 10px 0px;
	    padding: 10px 10px 10px 10px;
	}
</style>

<div class="content_tabs bround">
    <ul>
        <li class="active"><a title="Settings" onclick="changeTab(this); return false;" href="javascript:void(0);">
            Information</a><span></span></li>
        <li><a title="Report" onclick="changeTab(this); return false;" href="javascript:void(0);">
            Daily Report</a><span></span></li>
        <li><a title="Payout" onclick="changeTab(this); return false;" href="javascript:void(0);">
            Payout</a><span></span></li>
    </ul>
    <div class="clear">
    </div>
    <div style="min-height: 300px; display: block;" class="content_tabs_wrap">
        <div style="display: block;" id="Settings" class="content_tabs_box">
            <?php include dirname(__FILE__).'/ui/setting.php'; ?>
            <div style="height: 15px !important;" class="clear">
            </div>
        </div>
        <div style="display: none;" id="Report" class="content_tabs_box">
            <?php include dirname(__FILE__).'/ui/report.php'; ?>
            <div style="height: 15px !important;" class="clear">
            </div>
        </div>
        <div style="display: none;" id="Payout" class="content_tabs_box">
            <?php include dirname(__FILE__).'/ui/payout.php'; ?>
            <div style="height: 15px !important;" class="clear">
            </div>
        </div>
        <div style="text-align: center; font-size: 14px; margin-top: 30px; margin-bottom: 20px;">
        </div>
    </div>
</div>