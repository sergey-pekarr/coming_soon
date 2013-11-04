<html>
<head>
<title>Check user</title>
</head>
<body>
<form method="get">
Username/email/id: <input type="text" name="user" maxlength="129" value="<?php echo $user; ?>" style="width:300px" /> <input type="submit" value="Check" />
</form>
<?php
if (isset($row) && $row)
{
        echo "<p>Username: {$row['username']}</p>";
        echo "<p>UserId: ".Yii::app()->secur->encryptID($row['id'])."</p>";
        echo "<p>Level: ";
        if ($row['role']=='gold')
        {
            echo "<font color=green>Premium</font>";
        }
        else
        {
            echo "<font color=red>Free</font>";
        }
        echo "</p>";
        echo "<p>Regdate: ".date("Y-m-d", strtotime($row['activity']['joined']))."</p>";
        if ($row['role']=='gold' && $payment)
        {
            echo "<p>Upgrade: {$payment['firstpay']}</p>";
        }
		
		if ($aff)
        {
			echo "<p>Affid: {$row['affid']} ({$aff['login']})</p>";
        }
		else
		{
			echo "<p>Affid: without affid</p>";
		}
		
    //var_dump($row);
}
else
{
	if ($user)
		echo "<p>This user is not found</p>";
}
?>
</body>
</html>