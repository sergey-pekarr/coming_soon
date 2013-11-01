
                    <div style="border-width: 0px 1px 1px; border-style: none solid solid; border-color: currentColor rgb(221, 218, 210) rgb(221, 218, 210);
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
                                            <strong>From: <a href="<?php echo $data['from_profileUrl'] ?>"
                                                rel="nofollow" target="_blank"><span><?php echo $data['from_username'] ?></span></a></strong>,
                                            <?php echo $data['from_age'] ?>, <?php echo $data['from_location'] ?></p>
                                        <p>
                                            Hey <?php echo $data['username'] ?>,
                                        </p>
                                        <p>
                                            <?php echo $data['from_username'] ?> is really interested in getting to know you and has asked to see a sexy
                                            photo of you. Why don't you upload one and show them what they could be missing?
                                            Test results have shown that profiles which contain photos receive up to 10 times
                                            the XXX fun than ones without.
                                        </p>
                                        <p style="text-align: center;">
                                            <strong>
                                            	<a href="<?php echo $data['photoUploadUrl'] ?>" rel="nofollow" target="_blank">
                                            		<span>CLICK HERE now to upload photos to your account</span>
                                            	</a>
                                            </strong>
										</p>
<?php /*
                                        <p style="text-align: center;">
                                            <strong><a href="http://pinkmeets.com/profile.php?facebookphoto&auth={authuser}&l={logincode}" rel="nofollow" target="_blank">CLICK HERE
                                                to add from your Facebook account</a></strong></p>
*/?>
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
