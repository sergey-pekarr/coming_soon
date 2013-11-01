

<style type="text/css">
    #distance
    {
        width: 63px;
        text-align: right;
    }
    #threshold
    {
        width: 63px;
        text-align: right;
    }
    fieldset
    {
        padding: 5px 5px 5px 10px;
        margin: 10px 5px 5px 5px;
        border: solid 1px #555;
        width: auto;
    }
    fieldset legend
    {
        width: auto;
    }
    table#tblrank tr td:first-child
    {
        width: 140px;
    }
    table#tblrank tr td:nth-child(2) input
    {
        width: 60px;
        text-align: right;
    }
    table#tblrank tr td:last-child
    {
        width: 200px;
    }
</style>
<h3>
    Settings</h3>
<fieldset style="width: 500px;">
    <legend>Location setting</legend>
    <label>
        Risk if distance between two locations less than:<br />
    </label>
    <input type="textbox" name="distance" id="distance" value="<?php echo round($range,2); ?>" />
    miles (~<?php echo round($range * 1.62 * 1000, 0); ?> meters)
</fieldset>
<fieldset style="width: 500px;">
    <legend>Figure print setting</legend>
    <label>
        Risk if they are likely greater than:<br />
    </label>
    <input type="text" name="threshold" id="threshold" value="<?php echo round($threshold,2); ?>" />
    <fieldset>
        <legend>Rank</legend>
        <table id="tblrank">
            <tr>
                <td>
                    Screen Size
                </td>
                <td>
                    <input type="text" id="screen" name="screen" value="<?php echo round($ranks['screen'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    Available Screen Size
                </td>
                <td>
                    <input type="text" id="availscreen" name="availscreen" value="<?php echo round($ranks['availscreen'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    Adjusted Screen Size
                </td>
                <td>
                    <input type="text" id="screen_nor" name="screen_nor" value="<?php echo round($ranks['screen_nor'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    Adjusted Available Screen Size
                </td>
                <td>
                    <input type="text" id="availscreen_nor" name="availscreen_nor" value="<?php echo round($ranks['availscreen_nor'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    Platform
                </td>
                <td>
                    <input type="text" id="platform" name="platform" value="<?php echo round($ranks['platform'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    Browser
                </td>
                <td>
                    <input type="text" id="browser" name="browser" value="<?php echo round($ranks['browser'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    Agent
                </td>
                <td>
                    <input type="text" id="agent" name="agent" value="<?php echo round($ranks['agent'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    Plugins
                </td>
                <td>
                    <input type="text" id="plugins" name="plugins" value="<?php echo round($ranks['plugins'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    Plugin without version
                </td>
                <td>
                    <input type="text" id="plugins_pur" name="plugins_pur" value="<?php echo round($ranks['plugins_pur'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    Fonts
                </td>
                <td>
                    <input type="text" id="fonts" name="fonts" value="<?php echo round($ranks['fonts'],2); ?>" />
                </td>
                <td>
                </td>
            </tr>
        </table>
        <label>
            <b>Rank: </b>How important each variable relate to other variable</label>
    </fieldset>
</fieldset>
<input type="button" value="Save" style="margin: 5px;" onclick="saveSetting();"/>
<span id="savestatus"></span>

<script>
    function saveSetting() {

        var data = { 'screen': $('#screen').val(),
            'availscreen': $('#availscreen').val(),
            'browser': $('#browser').val(),
            'agent': $('#agent').val(),
            'platform': $('#platform').val(),
            'fonts': $('#fonts').val(),
            'plugins': $('#plugins').val(),
            'screen_nor': $('#screen_nor').val(),
            'availscreen_nor': $('#availscreen_nor').val(),
            'plugins_pur': $('#plugins_pur').val(),
            'threshold': $('#threshold').val(),
            'distance': $('#distance').val(),
        };

        var url = '/admin/risk/save';

        $.post(url, data)
        .success(function () {
            $('#savestatus').html('OK');
        })
        .fail(function () {
            $('#savestatus').html('Failed');
        });
        
    }
</script>
