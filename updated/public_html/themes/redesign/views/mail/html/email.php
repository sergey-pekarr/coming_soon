<?php //<img src="< ?php echo SITE_URL ? >/images/img/header-message.png"> ?>

                    <div style="margin-left: auto; margin-right:auto
                        padding: 10px; width: 530px; text-align: left; color: rgb(114, 114, 114); line-height: 18px;
                        font-family: tahoma; font-size: 13px; border-collapse: collapse;">
                        
                        <table style="margin: 0px auto; width: 530px;">
                            <tbody>
                                <tr>
                                    <td width="100">
                                        <img alt="Profile Image" src="<?php echo (strpos($data['from_imgUrl'],'http://') === 0 ? '' : SITE_URL).$data['from_imgUrl'] ?>" width="82"
                                            height="82">
                                    </td>
                                    <td style="color: rgb(114, 114, 114); line-height: 18px; font-family: tahoma; font-size: 13px;
                                        vertical-align: top;">

                                        <p>
                                            <strong><?php echo $data['username'] ?></strong>, you have received a new private message from:<br>
                                            <strong><a href="<?php echo $data['from_profileUrl'] ?>"
                                                rel="nofollow" target="_blank"><span><?php echo $data['from_username'] ?></span></a></strong>,
                                            <?php echo $data['from_age'] ?>, <?php echo $data['from_location'] ?>!</p>
                                        <p>
                                            <strong>Subject: <a href="<?php echo $data['messages_Url'] ?>"
                                                rel="nofollow" target="_blank"><?php echo $data['message_subject'] ?></a></strong></p>
                                        <p>
                                            Quick, <?php echo $data['from_username'] ?> is horny and is waiting for you now, what are you waiting for?&nbsp;</p>
                                        <p>
                                            <a href="<?php echo $data['messages_Url'] ?>"
                                                rel="nofollow" target="_blank">CLICK HERE</a> now to read their email...who
                                            knows, you could be getting laid tonight!</p>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="margin: 0px auto; width: 530px;">
                            <tbody>
                                <tr>
                                    <td style="color: rgb(114, 114, 114); line-height: 18px; font-family: tahoma; font-size: 13px;
                                        border-right-color: rgb(221, 218, 210); border-right-width: 1px; border-right-style: solid;"
                                        width="50%">
                                        <?php echo $data['from_age'] ?> year old <?php echo $data['from_gender_text'] ?> from <?php echo $data['from_location'] ?><br>
                                        Looking for a <?php echo $data['from_looking_for_gender_text'] ?> between <?php echo $data['from_minage'] ?> and <?php echo $data['from_maxage'] ?>
                                    </td>
                                    <td align="right">
                                        <a href="<?php echo $data['from_profileUrl'] ?>"
                                            rel="nofollow" target="_blank">
                                            <img style="border: 0px currentColor;" alt="View Profile" src="<?php echo SITE_URL ?>/images/img/view-her-profile.png"></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
