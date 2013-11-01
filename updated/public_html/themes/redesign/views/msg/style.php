<script>
    $(document).ready(function () {
        $('table.messagerow td.message-row').click(function (evt) {
            var id = $('table.messagerow').has(evt.target).attr('id');
            window.location = '/thread/' + id;
        });
    });
    function archiveThread(msgid, ele) {
        var url = '/msg/archivethread/' + msgid;
        $.get(url, function (res) {
        })
        .success(function (res) {
            var jq = $('li.message-row').has(ele);
            jq.prev().hide('fast', function () { $(this).remove(); });
            jq.hide('fast', function () { $(this).remove(); });
        });
    }
    function deleteThread(msgid, ele) {
	    if (!confirm('Are you sure you delete the message?')) {
            return;
        }
        var url = '/msg/deletethread/' + msgid;
        $.get(url, function (res) {
        })
        .success(function (res) {
            var jq = $('li.message-row').has(ele);
            jq.prev().hide('fast', function () { $(this).remove(); });
            jq.hide('fast', function () { $(this).remove(); });
        });
    }
</script>