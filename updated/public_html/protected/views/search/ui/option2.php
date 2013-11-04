
<div class="left">
    Height: <span class="height_display">4&rsquo;7&rdquo; - more than 6&rsquo;7&rdquo;</span>
</div>
<div class="right">
    <input type="hidden" name="height" value="0" class="height_base" autocomplete="off">
    <input type="hidden" name="maxheight" value="27" class="height_max" autocomplete="off">
    <div class="slider_wrap">
        <div class="height_slider">
        </div>
    </div>
</div>
<hr />

<script type="text/javascript" charset="utf-8">
var height = ['I\'d rather not say','less than 4&rsquo;7&rdquo;','4&rsquo;7&rdquo;','4&rsquo;8&rdquo;',
'4&rsquo;9&rdquo;','4&rsquo;10&rdquo;','4&rsquo;11&rdquo;','5&rsquo;0&rdquo;',
'5&rsquo;1&rdquo;','5&rsquo;2&rdquo;','5&rsquo;3&rdquo;','5&rsquo;4&rdquo;',
'5&rsquo;5&rdquo;','5&rsquo;6&rdquo;','5&rsquo;7&rdquo;','5&rsquo;8&rdquo;',
'5&rsquo;9&rdquo;','5&rsquo;10&rdquo;','5&rsquo;11&rdquo;','6&rsquo;0&rdquo;',
'6&rsquo;1&rdquo;','6&rsquo;2&rdquo;','6&rsquo;3&rdquo;','6&rsquo;4&rdquo;',
'6&rsquo;5&rdquo;','6&rsquo;6&rdquo;','6&rsquo;7&rdquo;','more than 6&rsquo;7&rdquo;'];

//var weight = new Array();
//weight[0]='I\'d rather not say';weight[1]='below 84lbs';weight[2]='84lbs';weight[3]='98lbs';weight[4]='112lbs';weight[5]='126lbs';
//weight[6]='140lbs';weight[7]='154lbs';weight[8]='168lbs';weight[9]='182lbs';weight[10]='196lbs';weight[11]='210lbs';weight[12]='224lbs';
//weight[13]='238lbs';weight[14]='252lbs';weight[15]='above 252lbs';

$(document).ready(function () {        
    $("#advanced_search .height_slider").slider({
        range: true,
        min: 1,
        max: height.length - 1,
        values: [$("#advanced_search .height_base").val(), $("#advanced_search .height_max").val()],
        animate: true,
        slide: function (event, ui) {
            $("#advanced_search .height_display").html(height[ui.values[0]] + ' - ' + height[ui.values[1]]);
            $("#advanced_search .height_base").val(ui.values[0]);
            $("#advanced_search .height_max").val(ui.values[1]);
        },
        change: function (event, ui) {
            $("#advanced_search .height_display").html(height[ui.values[0]] + ' - ' + height[ui.values[1]]);
            $("#advanced_search .height_base").val(ui.values[0]);
            $("#advanced_search .height_max").val(ui.values[1]);
        }
	});
	
	    //        $("#advanced_search .weight_slider").slider({
        //            range: true,
        //            min: 2,
        //            max: ((typeof weight != 'undefined') ? weight.length - 1 : 0),
        //            values: [$("#advanced_search .weight_base").val(), $("#advanced_search .weight_max").val()],
        //            animate: true,
        //            slide: function (event, ui) {
        //                $("#advanced_search .weight_display").html(weight[ui.values[0]] + ' - ' + weight[ui.values[1]]);
        //                $("#advanced_search .weight_base").val(ui.values[0]);
        //                $("#advanced_search .weight_max").val(ui.values[1]);
        //            },
        //            change: function (event, ui) {
        //                $("#advanced_search .weight_display").html(weight[ui.values[0]] + ' - ' + weight[ui.values[1]]);
        //                $("#advanced_search .weight_base").val(ui.values[0]);
        //                $("#advanced_search .weight_max").val(ui.values[1]);
        //            }
        //        });
});
</script>


<!--
                <div class="left">
                    Weight: <span class="weight_display">84lbs - above 252lbs</span>
                </div>
                <div class="right">
                    <input type="hidden" name="weight_base" value="0" class="weight_base" autocomplete="off">
                    <input type="hidden" name="weight_max" value="15" class="weight_max" autocomplete="off">
                    <div class="slider_wrap">
                        <div class="weight_slider">
                        </div>
                    </div>
                </div>
                <hr /> -->
<div class="left">
    Hair Color:
</div>
<div class="right">
    <label class="hair_colour[1]" for="hair_colour[1]">
        <input id="hair_colour[1]" class="checkbox " name="hair_color" value="1" type="checkbox">&nbsp;Black&nbsp;<span></span></label>
    <label class="hair_colour[2]" for="hair_colour[2]">
        <input id="hair_colour[2]" class="checkbox " name="hair_color" value="2" type="checkbox">&nbsp;Dark
        Brown&nbsp;<span></span></label>
    <label class="hair_colour[3]" for="hair_colour[3]">
        <input id="hair_colour[3]" class="checkbox " name="hair_color" value="3" type="checkbox">&nbsp;Light
        Brown&nbsp;<span></span></label>
    <label class="hair_colour[4]" for="hair_colour[4]">
        <input id="hair_colour[4]" class="checkbox " name="hair_color" value="4" type="checkbox">&nbsp;Brown&nbsp;<span></span></label>
    <label class="hair_colour[5]" for="hair_colour[5]">
        <input id="hair_colour[5]" class="checkbox " name="hair_color" value="5" type="checkbox">&nbsp;Dark
        Blonde&nbsp;<span></span></label>
    <label class="hair_colour[6]" for="hair_colour[6]">
        <input id="hair_colour[6]" class="checkbox " name="hair_color" value="6" type="checkbox">&nbsp;Blonde&nbsp;<span></span></label>
    <label class="hair_colour[7]" for="hair_colour[7]">
        <input id="hair_colour[7]" class="checkbox " name="hair_color" value="7" type="checkbox">&nbsp;Light
        Blonde&nbsp;<span></span></label>
    <label class="hair_colour[8]" for="hair_colour[8]">
        <input id="hair_colour[8]" class="checkbox " name="hair_color" value="8" type="checkbox">&nbsp;Auburn/Red&nbsp;<span></span></label>
    <label class="hair_colour[9]" for="hair_colour[9]">
        <input id="hair_colour[9]" class="checkbox " name="hair_color" value="9" type="checkbox">&nbsp;Salt
        And Pepper&nbsp;<span></span></label>
    <label class="hair_colour[10]" for="hair_colour[10]">
        <input id="hair_colour[10]" class="checkbox " name="hair_color" value="10" type="checkbox">&nbsp;Silver&nbsp;<span></span></label>
    <label class="hair_colour[11]" for="hair_colour[11]">
        <input id="hair_colour[11]" class="checkbox " name="hair_color" value="11" type="checkbox">&nbsp;Platinum&nbsp;<span></span></label>
    <label class="hair_colour[12]" for="hair_colour[12]">
        <input id="hair_colour[12]" class="checkbox " name="hair_color" value="12" type="checkbox">&nbsp;Grey&nbsp;<span></span></label>
    <label class="hair_colour[13]" for="hair_colour[13]">
        <input id="hair_colour[13]" class="checkbox " name="hair_color" value="13" type="checkbox">&nbsp;Other&nbsp;<span></span></label>
</div>
<hr />
<div class="left">
    Hair Length:
</div>
<div class="right">
    <label class="hair_length[1]" for="hair_length[1]">
        <input id="hair_length[1]" class="checkbox " name="hair_length" value="1" type="checkbox">&nbsp;Shaved&nbsp;<span></span></label>
    <label class="hair_length[2]" for="hair_length[2]">
        <input id="hair_length[2]" class="checkbox " name="hair_length" value="2" type="checkbox">&nbsp;Very
        Short&nbsp;<span></span></label>
    <label class="hair_length[3]" for="hair_length[3]">
        <input id="hair_length[3]" class="checkbox " name="hair_length" value="3" type="checkbox">&nbsp;Short&nbsp;<span></span></label>
    <label class="hair_length[4]" for="hair_length[4]">
        <input id="hair_length[4]" class="checkbox " name="hair_length" value="4" type="checkbox">&nbsp;Shoulder-length&nbsp;<span></span></label>
    <label class="hair_length[5]" for="hair_length[5]">
        <input id="hair_length[5]" class="checkbox " name="hair_length" value="5" type="checkbox">&nbsp;Long&nbsp;<span></span></label>
    <label class="hair_length[6]" for="hair_length[6]">
        <input id="hair_length[6]" class="checkbox " name="hair_length" value="6" type="checkbox">&nbsp;Balding&nbsp;<span></span></label>
</div>
<hr />
<div class="left" style="display: none;">
    Pubic Hair:
</div>
<div class="right" style="display: none;">
</div>
<hr style="display: none;" />
<div class="left">
    Ethnic Origin:
</div>
<div class="right">
    <label class="ethnicity[1]" for="ethnicity[1]">
        <input id="ethnicity[1]" class="checkbox " name="ethnicity" value="1" type="checkbox">&nbsp;White
        / Caucasian&nbsp;<span></span></label>
    <label class="ethnicity[2]" for="ethnicity[2]">
        <input id="ethnicity[2]" class="checkbox " name="ethnicity" value="2" type="checkbox">&nbsp;Black
        / African Descent&nbsp;<span></span></label>
    <label class="ethnicity[3]" for="ethnicity[3]">
        <input id="ethnicity[3]" class="checkbox " name="ethnicity" value="3" type="checkbox">&nbsp;Middle
        Eastern&nbsp;<span></span></label>
    <label class="ethnicity[4]" for="ethnicity[4]">
        <input id="ethnicity[4]" class="checkbox " name="ethnicity" value="4" type="checkbox">&nbsp;Asian&nbsp;<span></span></label>
    <label class="ethnicity[5]" for="ethnicity[5]">
        <input id="ethnicity[5]" class="checkbox " name="ethnicity" value="5" type="checkbox">&nbsp;Latino
        / Hispanic&nbsp;<span></span></label>
    <label class="ethnicity[6]" for="ethnicity[6]">
        <input id="ethnicity[6]" class="checkbox " name="ethnicity" value="6" type="checkbox">&nbsp;Other&nbsp;<span></span></label>
    <label class="ethnicity[7]" for="ethnicity[7]">
        <input id="ethnicity[7]" class="checkbox " name="ethnicity" value="7" type="checkbox">&nbsp;East
        Indian&nbsp;<span></span></label>
    <label class="ethnicity[8]" for="ethnicity[8]">
        <input id="ethnicity[8]" class="checkbox " name="ethnicity" value="8" type="checkbox">&nbsp;Native
        American&nbsp;<span></span></label>
    <label class="ethnicity[9]" for="ethnicity[9]">
        <input id="ethnicity[9]" class="checkbox " name="ethnicity" value="9" type="checkbox">&nbsp;Mixed
        Race&nbsp;<span></span></label>
    <label class="ethnicity[10]" for="ethnicity[10]">
        <input id="ethnicity[10]" class="checkbox " name="ethnicity" value="10" type="checkbox">&nbsp;Mediterranean&nbsp;<span></span></label>
    <label class="ethnicity[11]" for="ethnicity[11]">
        <input id="ethnicity[11]" class="checkbox " name="ethnicity" value="11" type="checkbox">&nbsp;Latin-american&nbsp;<span></span></label>
    <label class="ethnicity[12]" for="ethnicity[12]">
        <input id="ethnicity[12]" class="checkbox " name="ethnicity" value="12" type="checkbox">&nbsp;Pacific
        Islander &nbsp;<span></span></label>
</div>
<hr />
<div class="left">
    Appearance:
</div>
<div class="right">
    <label class="appearance[1]" for="appearance[1]">
        <input id="appearance[1]" class="checkbox " name="appearance" value="1" type="checkbox">&nbsp;Very
        Attractive&nbsp;<span></span></label>
    <label class="appearance[2]" for="appearance[2]">
        <input id="appearance[2]" class="checkbox " name="appearance" value="2" type="checkbox">&nbsp;Attractive&nbsp;<span></span></label>
    <label class="appearance[3]" for="appearance[3]">
        <input id="appearance[3]" class="checkbox " name="appearance" value="3" type="checkbox">&nbsp;Average&nbsp;<span></span></label>
</div>
<hr />
<div class="left">
    Piercings &amp; Tattoos:
</div>
<div class="right">
    <label class="my_piercings___tattoos[1]" for="my_piercings___tattoos[1]">
        <input id="my_piercings___tattoos[1]" class="checkbox " name="piercings_tattoos"
            value="1" type="checkbox">&nbsp;None&nbsp;<span></span></label>
    <label class="my_piercings___tattoos[2]" for="my_piercings___tattoos[2]">
        <input id="my_piercings___tattoos[2]" class="checkbox " name="piercings_tattoos"
            value="2" type="checkbox">&nbsp;Visible Tattoo&nbsp;<span></span></label>
    <label class="my_piercings___tattoos[3]" for="my_piercings___tattoos[3]">
        <input id="my_piercings___tattoos[3]" class="checkbox " name="piercings_tattoos"
            value="3" type="checkbox">&nbsp;Strategically Placed Tattoo&nbsp;<span></span></label>
    <label class="my_piercings___tattoos[4]" for="my_piercings___tattoos[4]">
        <input id="my_piercings___tattoos[4]" class="checkbox " name="piercings_tattoos"
            value="4" type="checkbox">&nbsp;Pierced Ear(s)&nbsp;<span></span></label>
    <label class="my_piercings___tattoos[5]" for="my_piercings___tattoos[5]">
        <input id="my_piercings___tattoos[5]" class="checkbox " name="piercings_tattoos"
            value="5" type="checkbox">&nbsp;Belly Button Ring&nbsp;<span></span></label>
    <label class="my_piercings___tattoos[6]" for="my_piercings___tattoos[6]">
        <input id="my_piercings___tattoos[6]" class="checkbox " name="piercings_tattoos"
            value="6" type="checkbox">&nbsp;Genital Piercing&nbsp;<span></span></label>
    <label class="my_piercings___tattoos[7]" for="my_piercings___tattoos[7]">
        <input id="my_piercings___tattoos[7]" class="checkbox " name="piercings_tattoos"
            value="7" type="checkbox">&nbsp;Other&nbsp;<span></span></label>
</div>
<hr />
<div class="left">
    Style:
</div>
<div class="right">
    <label class="style[1]" for="style[1]">
        <input id="style[1]" class="checkbox " name="style" value="1" type="checkbox">&nbsp;Bohemian&nbsp;<span></span></label>
    <label class="style[2]" for="style[2]">
        <input id="style[2]" class="checkbox " name="style" value="2" type="checkbox">&nbsp;Classical&nbsp;<span></span></label>
    <label class="style[3]" for="style[3]">
        <input id="style[3]" class="checkbox " name="style" value="3" type="checkbox">&nbsp;Cool&nbsp;<span></span></label>
    <label class="style[4]" for="style[4]">
        <input id="style[4]" class="checkbox " name="style" value="4" type="checkbox">&nbsp;Ethnic&nbsp;<span></span></label>
    <label class="style[5]" for="style[5]">
        <input id="style[5]" class="checkbox " name="style" value="5" type="checkbox">&nbsp;Rock&nbsp;<span></span></label>
    <label class="style[6]" for="style[6]">
        <input id="style[6]" class="checkbox " name="style" value="6" type="checkbox">&nbsp;Sophisticated&nbsp;<span></span></label>
    <label class="style[7]" for="style[7]">
        <input id="style[7]" class="checkbox " name="style" value="7" type="checkbox">&nbsp;Sporty&nbsp;<span></span></label>
    <label class="style[8]" for="style[8]">
        <input id="style[8]" class="checkbox " name="style" value="8" type="checkbox">&nbsp;Trendy&nbsp;<span></span></label>
    <label class="style[9]" for="style[9]">
        <input id="style[9]" class="checkbox " name="style" value="9" type="checkbox">&nbsp;Other&nbsp;<span></span></label>
</div>
<hr />
<a href="#" onclick="changeTab('a[title=\'option3\']'); return false;" class="content_button"
    style="width: auto;">Search Character<span><img class="iconForward" src="/images/img/blank.gif"
        alt="" /></span></a>