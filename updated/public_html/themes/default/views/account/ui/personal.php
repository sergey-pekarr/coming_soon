<table>
    <tr>
        <td>
            My Email
        </td>
        <td>
            <?php echo $profile->getDataValue('email'); ?>
        </td>
        <td>
            <a id="unsubscribeEmail" class="content_button editbutton " style="width: 90px; float: left;"
                href="javascript:void();" onclick="changeNotification(); return false;">
				<p style="padding-top:3px;">
                    <?php if($profile->getSettingsValue('hided_notify') == '0') echo 'Unsubscribe'; else echo 'Re-subscribe'; ?>
				</p>
                 <span><img class="iconEdit" alt="" src="/images/img/blank.gif"></span></a>
        </td>
        <td style="padding-left: 30px;">
            <span id="unsubscribNotice" style="color: rgb(178, 0, 0);">
                <?php if($profile->getSettingsValue('hided_notify') == '0') echo ''; else echo '(Unsubscribed from emails)'; ?>
            </span>
        </td>
    </tr>
</table>
<hr />
<!--<table>
    <tr>
        <td>
            I am a
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            Looking for
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>
</table>
<hr />
<table>
    <tr>
        <td>
            My Location
        </td>
        <td>
        </td>
        <td>
            <a id="editLocation" class="content_button editbutton " style="width: 40px; float: left;"
                href="#">Edit<span><img class="iconEdit" alt="" src="/images/img/blank.gif"></span></a>
        </td>
    </tr>
</table>
<hr />-->
<table>
    <tr>
        <td>
            My Username
        </td>
        <td>
            <?php echo $profile->getDataValue('username'); ?>			
        </td>
        <td>
            <a id="editUsername" class="content_button editbutton " style="width: 40px; float: left;"
                href="javascript:void();" onclick="editUsername(); return false;">Edit<span><img
                    class="iconEdit" alt="" src="/images/img/blank.gif"></span></a>
        </td>
    </tr>
</table>
<!--<div class="message_err">
    <div class="message_wrap">
        <p>You must confirm your username.</p>
        <span onclick="$(this).parent().parent().slideUp(200, function() {$(this).remove();resizeTab();});"></span></div>
</div>
<div class="message_ok">
    <div class="message_wrap">
        <p>You must confirm your username.</p>
        <span onclick="$(this).parent().parent().slideUp(200, function() {$(this).remove();resizeTab();});"></span></div>
</div>-->
<div style="" id="divUsername" class="edit-content-container">
    <table>
        <tr>
            <td style="width: 170px;">
                New Username:
            </td>
            <td style="width: 170px; font-weight: bold;">
                Confirm Username:
            </td>
            <td>
            </td>
        </tr>
        <tr>
            <td>
                <input id="newusername" class="forminput" name="newpass" value="" type="text" style="width: 170px;">
            </td>
            <td style="width: 170px;">
                <input id="confirmnewusername" class="forminput" name="confirmnewpass" value="" type="text"
                    style="width: 170px;">
            </td>
            <td>
                <a id="cancelUsername" class="content_button " style="width: 40px; float: left; margin: 0px 10px 0px 10px;"
                    href="javascript:void();" onclick="cancelUsername(); return false;">Cancel<span>
                        <img class="iconX" alt="" src="/images/img/blank.gif"></span></a> <a style="margin: 0px 22px 0px 20px;
                            float: left; width: 40px;" id="saveUsername" class="content_button " href="javascript:void();"
                            onclick="saveUsername(); return false;">Save<span>
                                <img class="iconDisc" alt="" src="/images/img/blank.gif"></span></a>
            </td>
        </tr>
    </table>
</div>
<hr />
<table>
    <tr>
        <td>
            My Password
        </td>
        <td>
			********
        </td>
        <td>
            <a id="editPassword" class="content_button editbutton " style="width: 40px; float: left;"
                href="javascript:void();" onclick="editPassword(); return false;">Edit<span><img
                    class="iconEdit" alt="" src="/images/img/blank.gif"></span></a>
        </td>
    </tr>
</table>
<div style="" id="divPassword" class="edit-content-container">
    <table>
        <tr>
            <td style="width: 170px;">
                New Password:
            </td>
            <td style="width: 170px; font-weight: bold;">
                Confirm New Password:
            </td>
            <td>
            </td>
        </tr>
        <tr>
            <td>
                <input id="newpass" class="forminput" name="newpass" value="" type="password" style="width: 170px;">
            </td>
            <td style="width: 170px;">
                <input id="confirmnewpass" class="forminput" name="confirmnewpass" value="" type="password"
                    style="width: 170px;">
            </td>
            <td>
                <a id="cancelPassword" class="content_button " style="width: 40px; float: left; margin: 0px 10px 0px 10px;"
                    href="javascript:void();" onclick="cancelPassword(); return false;">Cancel<span>
                        <img class="iconX" alt="" src="/images/img/blank.gif"></span></a> <a style="margin: 0px 22px 0px 20px;
                            float: left; width: 40px;" id="savePassword" class="content_button " href="javascript:void();"
                            onclick="savePassword(); return false;">Save<span>
                                <img class="iconDisc" alt="" src="/images/img/blank.gif"></span></a>
            </td>
        </tr>
    </table>
</div>
<hr />
<table>
    <tr>
        <td>
            My Age
        </td>
        <td>
				<?php echo $profile->getDataValue('age'); ?>
        </td>
        <td>
            <a id="editAge" class="content_button editbutton " style="width: 40px; float: left;"
                href="javascript:void();" onclick="editAge(); return false;">Edit<span><img class="iconEdit"
                    alt="" src="/images/img/blank.gif"></span></a>
        </td>
    </tr>
</table>
<div style="" id="divAge" class="edit-content-container">
    <table>
        <tr>
            <td style="width: 300px;">
                Your date of birth:
            </td>
            <td>
            </td>
        </tr>
        <tr>
            <td>
				<?php
	//Temporary use the method! Should be replace by standard Yii methods
	function ToNumberOptions($from, $to, $selected, $postValueName = '', $revert = false)
	{
		$step = $revert?-1:1;
		$count = abs($from-$to) + 1;
		$from = $revert?max($from,$to):min($from, $to);
		if(isset($_POST[$postValueName])) $selected = $_POST[$postValueName];
		for($i=0; $i<$count;$i++)
		{
			?>
			 <option value='<?php echo $from ?>' <?php if($from==$selected) echo 'selected=\'selected\''; ?>><?php echo $from ?></option>
			 <?php
			 $from+=$step;
			}
	}
	
	function ToOptionFromList($arrayItems, $selected, $postValueName = '')
	{
		$count = count($arrayItems);
		for($i=0; $i<$count;$i++)
		{
			$item = $arrayItems[$i];
			$name = $item[1];
			$value = $item[0];
	?>
		 <option value='<?php echo $value ?>' <?php if($value==$selected) echo 'selected=\'selected\''; ?>><?php echo $name ?></option>
		 <?php
		}
	}

	function ToMonthOption_en($selected, $postValueName = '')
	{
		static $monthlist= array(array(1, 'January'),
			array(2, 'February'),
			array(3, 'March'),
			array(4, 'April'),
			array(5, 'May'),
			array(6, 'June'),
			array(7, 'July'),
			array(8, 'August'),
			array(9, 'September'),
			array(10, 'October'),
			array(11, 'November'),
			array(12, 'December'),
			);	
		ToOptionFromList($monthlist, $selected, $postValueName);
	}
	$birthday = strtotime($profile->getDataValue('birthday'));
	$toYear = date('Y') - 18;
	$fromYear = $toYear - 80;
		 ?>
	<select name="month" id="month" style="display: inline; width: 90px;">
		<?php ToMonthOption_en(date('n', $birthday));  ?>
	</select>
	<select name="day" id="day" style="display: inline; width: 50px;">
		<?php ToNumberOptions(0, 31, date('j', $birthday));  ?>
	</select>
	<select name="year" id="year" style="display: inline; width: 70px;">
		<?php ToNumberOptions($toYear, $fromYear, date('Y', $birthday), 'year', true);  ?>
	</select>
            </td>
            <td>
                <a id="cacelAge" class="content_button " style="width: 40px; float: left; margin: 0px 10px 0px 10px;"
                    href="javascript:void();" onclick="cancelAge(); return false;">Cancel<span>
                        <img class="iconX" alt="" src="/images/img/blank.gif"></span></a> <a style="margin: 0px 22px 0px 20px;
                            float: left; width: 40px;" id="saveAge" class="content_button " href="javascript:void();"
                            onclick="saveAge(); return false;">Save<span>
                                <img class="iconDisc" alt="" src="/images/img/blank.gif"></span></a>
            </td>
        </tr>
    </table>
</div>

<?php 
//oleg 2012-07-24      hided for gold users
if ($profile->getDataValue('role')!='gold') { ?>
<hr />
<table>
    <tr>
        <td>
            Delete Account
        </td>
        <td>
            <a id="deleteAccount" class="content_button editbutton " style="width: 120px; margin-right: 30px;"
                onclick="return confirm('Are you sure you want to delete account?'); " href="/account/deleteaccount">
                Delete My Account<span><img class="iconEdit" alt="" src="/images/img/blank.gif"></span></a>
        </td>
        <td>
        </td>
    </tr>
</table>
<?php } ?>
