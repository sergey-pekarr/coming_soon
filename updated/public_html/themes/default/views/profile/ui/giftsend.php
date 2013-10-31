<?php
    $username = $profile->getDataValue('username');
?>
<div id="sendgiftpopup-container" style="display: none;">
    <div id="sendgiftpopup" style="width: 500px; height: 390px; background-color: White;">
        <div id="send-flirt-hdr">
        </div>
        <div class="sendgift_icons">
            <p class="text-pt1">
                Want to break the ice? Unsure what to say? Send a flirt to show <?php echo $username ?> you're
                interested!<br>
                It's simple, select the icon you wish and hit 'Send Flirt'</p>
            <p style="display: none;" class="text-pt2 text-pt2-1">
                Interested in <?php echo $username ?>? Send her your heart and break the ice!</p>
            <p style="display: none;" class="text-pt2 text-pt2-2">
                Want it to be a surprise? Send a present and leave her guessing what it is...</p>
            <p style="display: none;" class="text-pt2 text-pt2-3">
                Do you have a sweet tooth for <?php echo $username ?>? Send her a box of chocolates now</p>
            <p style="display: none;" class="text-pt2 text-pt2-4">
                Keep things classic and send a red rose... Show <?php echo $username ?> you're really interested</p>
            <p style="display: none;" class="text-pt2 text-pt2-5">
                It's time to celebrate.. Pop the cork &amp; send <?php echo $username ?> some champagne</p>
            <p style="display: none;" class="text-pt2 text-pt2-6">
                Feeling naughty? Send <?php echo $username ?> some sexy underwear</p>
            <p style="display: none;" class="text-pt2 text-pt2-7">
                Take it the next step... Heat things up with a kiss!</p>
            <p style="display: none;" class="text-pt2 text-pt2-8">
                Have your cake &amp; eat it. Send <?php echo $username ?> a fresh cupcake</p>
            <p style="display: none;" class="text-pt2 text-pt2-9">
                Fancy a treat? Send <?php echo $username ?> some seasonal candy.</p>
            <p style="display: none;" class="text-pt2 text-pt2-10">
                Feeling festive? Send <?php echo $username ?> a scary pumkin... Happy Halloween!</p>
        </div>
        <table class="sendgift_icons">
            <tr>
                <td>
                    <a class="gifticon gifticon_heart" onclick=" SelectGift2Send('1', this); return false;"
                        href="javascript:void(0);"></a>
                </td>
                <td>
                    <a class="gifticon gifticon_present" onclick=" SelectGift2Send('2' , this); return false;"
                        href="javascript:void(0);"></a>
                </td>
                <td>
                    <a class="gifticon gifticon_chocheart" onclick=" SelectGift2Send('3' , this); return false;"
                        href="javascript:void(0);"></a>
                </td>
                <td>
                    <a class="gifticon gifticon_rose" onclick=" SelectGift2Send('4' , this); return false;"
                        href="javascript:void(0);"></a>
                </td>
                <td>
                    <a class="gifticon gifticon_champagne" onclick=" SelectGift2Send('5' , this); return false;"
                        href="javascript:void(0);"></a>
                </td>
            </tr>
            <tr>
                <td>
                    <a class="gifticon gifticon_knickers" onclick=" SelectGift2Send('6' , this); return false;"
                        href="javascript:void(0);"></a>
                </td>
                <td>
                    <a class="gifticon gifticon_kiss" onclick=" SelectGift2Send('7' , this); return false;"
                        href="javascript:void(0);">
                </td>
                <td>
                    </a><a class="gifticon gifticon_cake" onclick=" SelectGift2Send('8' , this); return false;"
                        href="javascript:void(0);"></a>
                </td>
                <td>
                    <a class="gifticon gifticon_candycane" onclick=" SelectGift2Send('9' , this); return false;"
                        href="javascript:void(0);"></a>
                </td>
                <td>
                    <a class="gifticon gifticon_pumpkin" onclick=" SelectGift2Send('10', this); return false;"
                        href="javascript:void(0);"></a>
                </td>
            </tr>
        </table>
        <div class="center">
            <a id="send-button-click" href="#" onclick="clickSend(); return false;">
                <img class="send-gift-button" src="/images/img/blank.gif"></a>
            <script>
                    function sendGift(item) {
                        <?php 
                            if(CHelperProfile::getPaymentLinkWithAction('sendgift', $profile->getId(), $link)){
                                echo $link;
                            }
                            else{
                                $encid = Yii::app()->secur->encryptID($profile->getId());
                                ?>
                                $.getJSON('/action/sendgift/<?php echo $encid ?>/' + item, function(res){
                                })
                                .success(function(res){
                                    if(res && res.alert){
                                        showAlert(res.alert);
                                    }
                                    else if(res && res.title && res.desc){
                                        showAlert(res);
										//live update gifts_received
										var recjq = $('#gifts_received #gifts_received_icons');
										if(recjq.find('a.gifticon_' + item).length ==0){
										var template = '<a class="gifticon gifticon_' + item + '" onclick="return false;" href="javascript:void();"></a>'
										$(template).appendTo(recjq);
										}
                                    }
                                });
                                <?php
                            }
                        ?>
                    }
            </script>
        </div>
    </div>
</div>
