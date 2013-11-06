<?php include dirname(__FILE__).'/style.php'; ?>
<div class="content_tabs bround">
    <ul>
        <li class="<?php if($this->action == 'inbox') echo 'active'; ?>"><a onclick="" href="msg/inbox">
            Message <span></span></a></li>
        <li class="<?php if($this->action == 'sent') echo 'active'; ?>"><a onclick="" href="msg/sent">
            Sent <span></span></a></li>
        <li class="<?php if($this->action == 'archive') echo 'active'; ?>"><a onclick=""
            href="msg/archive">Archive <span></span></a></li>
    </ul>
    <div class="clear">
    </div>
    <div class="content_tabs_wrap">
    </div>
</div>
<div style="padding-top: 10px; margin-bottom: 20px;">
    <p></p>
    <ul class="message-list">
<?php foreach($items as $item) { 
    $id = $item['id_'.$dir];
    $profile = new Profile($id);
	$msgid = Yii::app()->secur->encryptID($id).'/'.$item['id'];
	$threadid = Yii::app()->secur->encryptID($id).'/'.((isset($item['parent']) && $item['parent']!= '')?$item['parent']:$item['id']);
	$read = ($item['read'] == '1');
	$itemClass = $read?'read':'unread';
?>
		<li class="spacer"></li>
		<li class="message-row">
            <table id="<?php echo $threadid ?>" class="messagerow <?php echo $itemClass; ?>">
                <tbody>
                    <tr>
                        <td class="message-row" width="50">
                            <img class="profile-small left" alt="" src="<?php echo $profile->imgUrl(); ?>"
                                width="50" height="50">
                        </td>
                        <td class="message-row">
                            <strong><?php echo $profile->getDataValue('username'); ?></strong>, 
                            <span class="strong"><?php echo CHelperProfile::showProfileInfoSimple($profile, 4) .', '.CHelperProfile::showProfileInfoSimple($profile, 7, 50); ?></span><br>
                            <span style="font-size: 12px;" class="msg-fade"><?php echo $item['subject']; if($item['subject'] && $item['subject'] != '') echo '<br>'; ?></span>
                            <span style="font-size: 12px;" class="msg-fade"><?php echo $item['text']; ?></span>
                        </td>
                        <td class="message-row msg-date" width="100">
                            <?php echo CHelperDate::date_distanceOfTimeInWords(time(), strtotime($item['added']), true); ?>
                        </td>
                        <td class="actionsread" width="10">
                            <a onclick="" href="thread/<?php echo $threadid; ?>">
                                <img id="unread2" alt="" src="/images/img/icons/email<?php if($read) echo '_open'; ?>.png" width="16" height="16"></a>
                        </td>
                        <td width="10">
                            <a onclick="deleteThread('<?php echo $msgid; ?>',this); return false;" href="#" title="destroy the message">
                                <img id="inbox0" alt="" src="/images/img/icons/delete.png" width="16" height="16"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </li>
<?php } ?>
    </ul>
</div>


