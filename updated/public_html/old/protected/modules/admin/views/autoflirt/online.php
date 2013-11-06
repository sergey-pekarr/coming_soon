<?php ?>
<strong>Number items:<strong>
    <input type="text" name="days" style="width: 40px; text-align: right;" value="" /><input
        type="button" name="submitdays" value="Submit" onclick="addDefaultOnlineItems($('input[name=\'days\']').val(), true);" />
    <table id="tableitems" cellpadding="0px" cellspacing="0px">
        <thead>
            <tr>
                <td>
                    Start
                </td>
                <td>
                    Duration
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
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <input type="button" value="Save" style="margin: 10px 0px 0px 0px; width: 60px;" onclick="save()" />
    <script>
        var autoflirtitems = <?php echo json_encode($items); ?>;
        $(document).ready(function () {
            addOnlineItems(autoflirtitems);
            addDefaultOnlineItems(2, false);
        });
    </script>