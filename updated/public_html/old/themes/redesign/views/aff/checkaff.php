<html>
<head>
<title>Check aff</title>
<?php CHelperAssets::cssUrl('site'); ?>
</head>
<body style="padding: 0 20px">
<form method="get">
<br />
Affid: <input type="text" name="user" maxlength="6" value="<?php echo $userId; ?>" style="width:160px; height: auto; line-height: auto; padding: 4px; margin-top:8px" />
<input class="btn" type="submit" value="Check" />
</form>
<br /><br />
<?php

function display_info($info, $title)
{
    if (!$info) return;
	
	if (!$info['fullname']) $info['fullname'] = "&nbsp;";
    echo "<table cellspacing='0' cellpadding='5' border='0' width='250' class='dataTable'>";
    echo "<th colspan='2'>{$title}</th>";
    echo "<tr><td width='1%'>Id</td><td>{$info['id']}</td></tr>";
    echo "<tr><td>Username</td><td>{$info['login']}</td></tr>";
    echo "<tr><td>Name</td><td>{$info['fullname']}</td></tr>";
    echo "</table>";
}


if ($userId)
{
    $acctype = $user['acctype'];    
	
    switch ($acctype)
    {
        case "master" : echo display_info($user, "Master"), "<br />"; break;
        case "manager": echo display_info($user, "Manager"), "<br />", display_info($master, "Master"), "<br />"; break;
        default: echo display_info($user, "Affiliate"), "<br />", display_info($manager, "Manager"), "<br />", display_info($master, "Master"), "<br />"; break;
    }
}
else
{
	if ($user)
		echo "<p>This user is not found</p>";
}
?>
</body>
</html>