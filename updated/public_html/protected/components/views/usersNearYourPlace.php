<h3>Members near your place (<?php echo $city; ?>)</h3>
<div class="users">
    <ul class="profiles front">
        <?php if(!empty($users)) : ?>
            <?php foreach ($users as $user) : ?>
                <?php
                    if (empty($user->images[0]->n)) {                    
                        $male = ($user->gender === 'M') ? 'male' : 'female';    
                        $img = '/../../images/nophoto_' . $male . '_big.png';
                    } else {
                        $img = $user->id . '/' . $user->images[0]->n[0] . '_big.jpg';
                    }

                    $encid = Yii::app()->secur->encryptID($user->id);
                ?>
                <li>
                    <div class="user_profile">
                        <a href="profile/<?php echo $encid; ?>">
                            <img src="<?php echo $img; ?>" />
                        </a>
                        <a href="profile/<?php echo $encid; ?>">
                            <div class="username"><?php echo $user->username; ?></div>
                        </a>
                        <div class="clear"></div>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>