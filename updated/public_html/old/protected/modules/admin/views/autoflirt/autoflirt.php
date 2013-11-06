<?php ?>
<strong>Number days: <strong>
    <input type="text" name="days" style="width: 40px; text-align: right;" value="" /><input
        type="button" name="submitdays" value="Submit" onclick="addDefaultItems($('input[name=\'days\']').val(), true);" />
		<br>
<strong>Inactive after: <strong>
    <input type="text" name="inactive" style="width: 40px; text-align: right;" value="<?php echo $inactive ?>" /><span> unread flirts (PhotoRequet, and Email)</span>
    <table id="tableitems" cellpadding="0px" cellspacing="0px">
        <thead>
            <tr>
                <td>
                    Day
                </td>
				<td>
					Time
				</td>
                <td>
                    Messages
                </td>
                <td>
                    Winks
                </td>
                <td>
                    PhotoRequest
                </td>
                <td>
                    View
                </td>
				<td>
				</td>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <input type="button" value="Save" style="margin: 10px 0px 0px 0px; width: 60px;" onclick="save()" />
    <script>
		$(document).ready(function () {
			items = <?php echo json_encode($items); ?>;
			//Correct int value
			for(var i = 0;i<items.length;i++){
				subItems = items[i].subItems;
				for(var j = 0;j<subItems.length;j++){
					subItem = subItems[j];
					if(subItem.duration && typeof(subItem.duration) == 'string') subItem.duration = parseInt(subItem.duration);
				}
			}
            buildView();
			if(items.length<5){
				addDefaultItems(5, false);
			}
        });
    </script>