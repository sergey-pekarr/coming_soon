<div class="content_tabs bround">
    <ul>
        <li class="active"><a title="personal" onclick="changeTab(this); return false;" href="javascript:void(0);">
            Personal Info</a><span></span></li>
        <li><a title="subscription" onclick="changeTab(this); return false;" href="javascript:void(0);">
            My Subscription</a><span></span></li>
    </ul>
    <div class="clear">
    </div>
    <div style="min-height: 100px; display: block;" class="content_tabs_wrap">
        <div style="display: block;" id="personal" class="content_tabs_box">
            <?php include dirname(__FILE__).'/ui/personal.php'; ?>
            <div style="height: 15px !important;" class="clear">
            </div>
        </div>
        <div style="display: none;" id="subscription" class="content_tabs_box">
            <?php include dirname(__FILE__).'/ui/subscription.php'; ?>
            <div style="height: 15px !important;" class="clear">
            </div>
        </div>
        <?php /*
		<div style="text-align: center; font-size: 14px; margin-top: 30px; margin-bottom: 20px;">
            If you signed up using Zombaio for Billing Inquiries, or to cancel your membership,
            please visit <a style="color: blue !important;" href="https://zombaio.com/">Zombaio.com</a>,
            our authorized sales agent.
        </div>*/ ?>
        
		<?php /*
		<div style="text-align: center; font-size: 14px; margin-top: 30px; margin-bottom: 20px;">
            If you signed up using Segpay for Billing Inquiries, or to cancel your membership,
            please visit <a style="color: blue !important;" href="https://segpaycs.com/">Segpay.com</a>,
            our authorized sales agent.
        </div>
		*/ ?>
		
    </div>
</div>

<script>
    function resizeTab() {
    }

    var operationMessageATemplate =
'<div class="">' +
    '<div class="message_wrap">' +
        '<p>You must confirm your username.</p>' +
        '<span onclick="$(this).parent().parent().slideUp(200, function() {$(this).remove();resizeTab();});"></span></div>' +
'</div>';

    //type: err, ok, warn
    function showOperationMessageA(type, msg, targetid, interval) {
        hideOperationMessageA(targetid);
        var jq = $(operationMessageATemplate);
        jq.find('p').html(msg);
        jq.attr('class', 'message_' + type);
        jq.attr('targetid', targetid);
        jq.insertBefore('#' + targetid);

        var close = function () {
            jq.find('span').click();
        };

        if (interval && interval > 0) {
            window.setTimeout(function () { close(); }, interval);
        }

    }

    function hideOperationMessageA(targetid) {
        $('div[targetid="' + targetid + '"]').remove(0);
    }

    function test1() {
        showOperationMessageA('ok', 'function test1()', 'divUsername', 2000);
    }

    function test2() {
        showOperationMessageA('err', 'function test2()', 'divUsername');
    }

    function editUsername() {
        $('#divUsername').css('display', 'block');
        $('#divUsername input[type="text"]').val('');
    }

    function changeNotification() {
        var btnText = $('#unsubscribeEmail p').text();
        $.getJSON('/account/changeNotification/' + btnText, function (res) {
            if (!res || !res.success) {
                
            }
            else {
                text = res.subscribe ? 'Unsubscribe' : 'Re-subscribe';
                $('#unsubscribeEmail p').text(text);
                if (!res.subscribe) {
                    $('#unsubscribNotice').html('(Unsubscribed from emails)');
                }
                else {
                    $('#unsubscribNotice').html('');
                }
            }
        });
    }

    function saveUsername() {
        var newusername = $('#newusername').val();
        var confirmnewusername = $('#confirmnewusername').val();
        if (newusername == '') {
            showOperationMessageA('err', 'Please type new username.', 'divUsername');
            return;
        }
        if (confirmnewusername != newusername) {
            showOperationMessageA('err', 'You must confirm your username.', 'divUsername');
            return;
        }
        if (!confirm('Are you sure you want to change your username to ' + newusername)) {
            return;
        }
        //Note: we can't rely on client verification!
        $.post('/account/editusername', {'newusername':newusername,'confirmnewusername':confirmnewusername}, function (res) {
            if (typeof (res) == 'string') {
                res = $.parseJSON(res);
            }
            if (res && !res.success && res.message) {
                //res.message might be string or array
                showOperationMessageA('err', res.message.toString(), 'divUsername');
            }
            else if (res && res.success) {
                showOperationMessageA('ok', 'your username has been changed', 'divUsername', 2000);

                window.setTimeout(function () {
                    window.location = document.URL;
                }, 2000);
            }
            else {
                showOperationMessageA('err', 'Could not change your username. Please try again later', 'divUsername');
            }
        });
    }

    function cancelUsername() {
        $('#divUsername').css('display', 'none');
        $('#divUsername input[type="text"]').val('');
    }

    function editPassword() {
        $('#divPassword').css('display', 'block');
        $('#divPassword input[type="password"]').val('');
    }

    function savePassword() {
        var newpass = $('#newpass').val();
        var confirmnewpass = $('#confirmnewpass').val();
        if (newpass == '') {
            showOperationMessageA('err', 'Please type password.', 'divPassword');
            return;
        }
        if (newpass != confirmnewpass) {
            showOperationMessageA('err', 'You must confirm your password.', 'divPassword');
            return;
        }
        if (!confirm('Are you sure you want to change your password')) {
            return;
        }
        //Note: we can't rely on client verification!
        $.post('/account/editpassword', { 'newpass': newpass, 'confirmnewpass': confirmnewpass }, function (res) {
            if (typeof (res) == 'string') {
                res = $.parseJSON(res);
            }
            if (res && !res.success && res.message) {
                //res.message might be string or array
                showOperationMessageA('err', res.message.toString(), 'divPassword');
            }
            else if (res && res.success) {
                showOperationMessageA('ok', 'your password has been changed', 'divPassword', 2000);
                cancelPassword();
            }
            else {
                showOperationMessageA('err', 'Could not change your password. Please try again later', 'divPassword');
            }
        });
    }

    function cancelPassword() {
        $('#divPassword').css('display', 'none');
        $('#divPassword input[type="password"]').val('');
    }

    function editAge() {
        $('#divAge').css('display', 'block');
    }

    function saveAge() {
        var data = {
            'year': $('#year').val(),
            'month': $('#month').val(),
            'day': $('#day').val()
        };
        $.post('/account/editbirthday', data, function (res) {
            if (typeof (res) == 'string') {
                res = $.parseJSON(res);
            }
            if (res && !res.success && res.message) {
                //res.message might be string or array
                showOperationMessageA('err', res.message.toString(), 'divAge');
            }
            else if (res && res.success) {
                showOperationMessageA('ok', 'Your birthday has been changed', 'divAge', 2000);
                cancelAge();

                $('#editAge').parent().prev().html(res.age);
            }
            else {
                showOperationMessageA('err', 'Could not change your password. Please try again later', 'divAge');
            }
        });
    }

    function cancelAge() {
        $('#divAge').css('display', 'none');
    }
</script>