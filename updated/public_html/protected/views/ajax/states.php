<?php 
$options = '';
if ($states)
{
    foreach ($states as $k=>$v)
    {
        $options .= "<option value=\"".$k."\">".$v."</v>";
    }
}
echo json_encode($options);