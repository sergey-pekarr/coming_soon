<div id="wrapper">
    <div id="conditionpanel">
        <input type="radio" name="membertype" id="conditionfree" value="free" onclick="onChangeMemberType('free');" /><label>Free
            members</label><br />
        <input type="radio" name="membertype" value="gold" onclick="onChangeMemberType('gold');" /><label>Gold
            members</label><br />
        <input type="radio" name="membertype" value="onlinefree" onclick="onChangeMemberType('onlinefree');" /><label>Online
            free members</label><br />
        <input type="radio" name="membertype" value="onlinegold" onclick="onChangeMemberType('onlinegold');" /><label>Online
            gold members</label><br />
    </div>
    <div id="contentpanel">
    </div>
</div>
<!--
+ Add item
+ Add subitems(number, period)
	- 
+ Remove subItems, Rebuild item


-->
<script>
    function onChangeMemberType(type) {
        $('#contentpanel').load('/admin/autoflirt/' + type + 'member');
    }

//    function addItems(items) {
//        index = $('#tableitems tbody tr').length;
//        for (i = 0; i < items.length; i++) {
//            data = items[i];
//            if (!data.subItems) {
//                $('<tr><td>' + (index + i + 1) + '</td>' +
//					'<td></td><td><input value="' + data.messages +
//					'" /></td><td><input value="' + data.winks +
//					'" /></td><td><input value="' + data.photorequest +
//					'" /></td><td><input value="' + data.view +
//					'" /></td>' + '<td></td>')
//				.appendTo($('#tableitems tbody'));
//            }
//            else {

//            }
//        }
//        $('input[name=\'days\']').val($('#tableitems tbody tr').length);
//    }

    function addSplitControl() {

    }

    function addDefaultItems(maxRow, removeIfOver) {
        updateData();
        try {
            maxRow = parseInt(maxRow);
        }
        catch (ex) {
            return;
        }
        if (maxRow > items.length) {
            for (var i = items.length; i < maxRow; i++) {
                addItem([{ duration: 3600*24}]);
            }
        }
        else if (removeIfOver) {
            items.splice(maxRow, items.length);
        }
        buildView();
    }

    function getItems() {
        updateData();
        var inactive = 5;
        try {
            inactive = parseInt($('input[name="inactive"]').val());
        }
        catch (exe) {
        }
        var result = { 'inactive': inactive };
        for (var i = 0; i < items.length; i++) {
            result['items[' + i + ']'] = items[i];
        }
        return result;
    }

    function save() {
        var option = $('#conditionpanel input:checked').val();
        if (option == 'free') {
            $.post('/admin/autoflirt/savefreemember', getItems(), function (res) {
            })
            .success(function (res) {
            })
            .fail(function (res) {
            });
        }
        else if (option == 'gold') {
            $.post('/admin/autoflirt/savegoldmember', getItems(), function (res) {
            })
            .success(function (res) {
            })
            .fail(function (res) {
            });
        }
        else if (option == 'onlinefree') {
            $.post('/admin/autoflirt/saveonlinefreemember', getOnlineItems(), function (res) {
            })
            .success(function (res) {
            })
            .fail(function (res) {
            });
        }
        else if (option == 'onlinegold') {
            $.post('/admin/autoflirt/saveonlinegoldmember', getOnlineItems(), function (res) {
            })
            .success(function (res) {
            })
            .fail(function (res) {
            });
        }
    }






    function convertOnlineTime(duration) {
        var hour = Math.floor(duration / 3600);
        var minute = Math.floor((duration % 3600) / 60);
        var second = Math.floor(duration % 60);
        if (hour > 0) return duration.toString() + ' (' + hour + ':' + minute + ':' + second + ')';
        if (minute > 0) return duration.toString() + ' (' + minute + ':' + second + ')';
        return duration.toString();
    }

    function addOnlineItems(items) {
        index = $('#tableitems tbody tr').length;
        var total = 0;
        $('#tableitems tbody tr td:nth-child(2) input').each(function (index, input) {
            try {
                total += parseInt(input.value);
            }
            catch (ex) {
            }
        });
        for (i = 0; i < items.length; i++) {
            data = items[i];
            $('<tr><td>' + convertOnlineTime(total) +
                '</td><td><input value="' + data.duration +
                '" /></td><td><input value="' + (data.messages?data.messages:0) +
                '" /></td><td><input value="' + (data.winks ? data.winks : 0) +
                '" /></td><td><input value="' + (data.photorequest ? data.photorequest : 0) +
                '" /></td><td><input value="' + (data.view ? data.view : 0) +
                '" /></td>')
            .appendTo($('#tableitems tbody'));
            total += parseInt(data.duration);
        }
        $('input[name=\'days\']').val($('#tableitems tbody tr').length);
    }

    function addDefaultOnlineItems(maxRow, removeIfOver) {
        try {
            maxRow = parseInt(maxRow);
        }
        catch (ex) {
            $('input[name=\'days\']').val($('#tableitems tbody tr').length);
            return;
        }
        if (maxRow < 0) maxRow = 0;
        if (maxRow > 10) maxRow = 10;
        if (removeIfOver) {
            $('#tableitems tbody tr').each(function (index, row) {
                if (index >= maxRow) $(row).remove(0);
            });
        }
        for (index = $('#tableitems tbody tr').length; index < maxRow; index++) {
            $('<tr><td></td><td><input value="0" /></td><td><input value="0" /></td><td><input value="0" /></td><td><input value="0" /></td><td><input value="0" /></td>')
            .appendTo($('#tableitems tbody'));
        }
        $('input[name=\'days\']').val($('#tableitems tbody tr').length);
    }

    function getOnlineItems() {
        var items = {};
        var i = 0;
        $('#tableitems tbody tr').each(function (index, row) {
            var controls = $(row).find('input');
            items['items[' + i + ']'] = {
                'duration': controls[0].value,
                'messages': controls[1].value,
                'winks': controls[2].value,
                'photorequest': controls[3].value,
                'view': controls[4].value
            };
            i++;
        });
        return items;
    }


    $(document).ready(function () {
        $('#conditionpanel #conditionfree').click();
    });











    var items = [];
    var maxDuration = 3600 * 24;

    function convertTime(duration) {
        var hour = Math.floor(duration / 3600);
        var minute = Math.floor((duration % 3600) / 60);
        if (minute < 10) minute = '0' + minute;
        var second = Math.floor(duration % 60);
        if (second < 10) second = '0' + second;
        if (hour > 0) return hour + ':' + minute + ':' + second;
        if (minute > 0) return minute + ':' + second;
        return duration.toString();
    }

    function deleteSubItemHtml(itemIndex, subItemIndex) {
        return '<a class="removeSubItem" href="#" onclick="removeSubItem(' + itemIndex + ',' + subItemIndex + ', true); return false" >Remove</a>';
    }

    function addSubItemHtml(itemIndex, defaultNumber, defaultDuration) {
        if (!defaultNumber) defaultNumber = 1;
        if (!defaultDuration) defaultDuration = 600;
        defaultDuration = convertTime(defaultDuration);
        return '<a class="addSubItem" href="#" onclick="addSubItems(' + itemIndex + ', this); return false;" >Add </a>' +
                '<input type="textbox" style="width:40px; border:solid 1px #ccc" value="' + defaultNumber + '" />' +
                '<span> items with duration </span>' +
                '<input type="textbox" style="width:60px; border:solid 1px #ccc"  value="' + defaultDuration + '" />';
    }

    function updateData() {
        var rowsjq = $('#tableitems tbody tr');
        var rowIndex = 0;
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            for (var j = 0; j < item.subItems.length; j++) {
                var inputs = $(rowsjq[rowIndex]).find('input');
                var subItem = item.subItems[j];
                subItem.messages = parseInt(inputs[0].value);
                subItem.winks = parseInt(inputs[1].value);
                subItem.photorequest = parseInt(inputs[2].value);
                subItem.view = parseInt(inputs[3].value);
                rowIndex++;
            }
        }
    }

    function buildView() {
        $('#tableitems tbody tr').remove();
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            if (!item.subItems || item.subItems.length == 0) addSubItem(item, {});
            var sub0 = item.subItems[0];
            var total = sub0.duration;
            var controlHtml = (item.subItems.length == 1 && maxDuration - total > 0) ? addSubItemHtml(i, 1, maxDuration - total) : deleteSubItemHtml(i, 0);
            if (item.subItems.length == 1 && item.subItems[0].duration > 0 && maxDuration - total > 0) controlHtml = deleteSubItemHtml(i, 0) + " or " + controlHtml;
            $('<tr><td rowspan="' + item.subItems.length + '">' + (i + 1) + '</td>' +
					'<td>' + convertTime(total) +
                    '</td><td><input value="' + sub0.messages +
					'" /></td><td><input value="' + sub0.winks +
					'" /></td><td><input value="' + sub0.photorequest +
					'" /></td><td><input value="' + sub0.view +
					'" /></td>' + '<td>' + controlHtml + '</td>').appendTo($('#tableitems tbody'));
            for (var j = 1; j < item.subItems.length; j++) {
                var subj = item.subItems[j];
                total += subj.duration;
                var controlHtml = (item.subItems.length == j + 1 && maxDuration - total > 0) ? addSubItemHtml(i, 1, maxDuration - total) : deleteSubItemHtml(i, j);
                $('<tr>' +
					'<td>' + convertTime(total) +
                    '</td><td><input value="' + subj.messages +
					'" /></td><td><input value="' + subj.winks +
					'" /></td><td><input value="' + subj.photorequest +
					'" /></td><td><input value="' + subj.view +
					'" /></td>' + '<td>' + controlHtml + '</td>').appendTo($('#tableitems tbody'));
            }
        }
        $('input[name=\'days\']').val(items.length);
    }

    function addItem(subItems, updateView) {
        if (updateView) {
            updateData();
        }
        var item = {};
        items[items.length] = item;
        if (Array.isArray(subItems)) {
            for (var i = 0; i < subItems.length; i++) {
                addSubItem(item, subItems[i]);
            }
        }
        else if (subItems != null) {
            addSubItem(item, subItems);
        }
        else {
            addSubItem(item, {});
        }
        if (updateView === true) buildView();
        return item;
    }

    function addSubItems(itemIndex, ele) {
        updateData();
        var inputs = $(ele).parent().find('input');
        var timeString = inputs[1].value;
        var number = parseInt(inputs[0].value);
        var arr = timeString.split(":");
        var duration = 0;
        if (arr.length == 3) duration += parseInt(arr[0]) * 3600 + parseInt(arr[1]) * 60 + parseInt(arr[2]);
        else if (arr.length >= 2) duration += parseInt(arr[0]) * 60 + parseInt(arr[1]);
        else duration += parseInt(arr[0]);
        var item = items[itemIndex];
        updateData();
        for (; number > 0; number--) {
            addSubItem(item, { 'duration': duration },false);
        }
        buildView();
    }

    function addSubItem(item, subItem, updateView) {
        if (updateView) {
            updateData();
        }
        if (!item.subItems) item.subItems = [];
        var total = 0;
        $(item.subItems).each(function (index, obj) { total += obj.duration; });
        if (total >= maxDuration) return;
        if (subItem.duration === undefined || subItem.duration == null) subItem.duration = 600;
        if (subItem.duration > maxDuration - total) subItem.duration = maxDuration - total;
        var newIndex = item.subItems.length;
        if (newIndex == 1 && item.subItems[0].duration == 0) newIndex = 0;
        item.subItems[newIndex] = {
            'duration': subItem.duration,
            'messages': subItem.messages ? subItem.message : 0,
            'winks': subItem.winks ? subItem.winks : 0,
            'photorequest': subItem.photorequest ? subItem.photorequest : 0,
            'view': subItem.view ? subItem.view : 0
        };
        if (updateView === true) buildView();
    }

    function removeSubItem(index, subItemIndex, updateView) {
        if (updateView) {
            updateData();
        }
        if (index < 0 || items.length <= index) return;
        var item = items[index];
        var subItems = item.subItems;
        if (subItemIndex < 0 || subItems.length <= subItemIndex) return;
        if (subItems.length == 1) {
            item.subItems[0] = {
                'duration': 0,
                'messages': 0,
                'winks': 0,
                'photorequest': 0,
                'view': 0
            };
        }
        else {
            subItems.splice(subItemIndex, 1);
        }
        if (updateView === true) buildView();
    }

    /* Sample end!
    var item1 = addItem([{ duration: 600 }, { duration: 1200 }, { duration: 1800}]);
    addSubItem(item1, { duration: 3600 });
    addSubItem(item1, { duration: 4 * 3600 });
    addSubItem(item1, { duration: 6 * 3600 });
    addSubItem(item1, { duration: 12 * 3600 });
    var item2 = addItem([{ duration: 12 * 3600 }, { duration: 12 * 3600 }, { duration: 1800}]);
    var item3 = addItem([{ duration: 12 * 3600 }, { duration: 12 * 3600}]);
    var item4 = addItem([{ duration: 0}]);
    for (i = 5; i <= 14; i++) {
        addItem([{ duration: 0}]);
    }
    //*/
</script>
