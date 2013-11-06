
//Move to main2.js
//function rangeSlider(form, $class, min, max) {
//    $('#' + form + " ." + $class + "_slider").slider({
//        range: true,
//        min: min,
//        max: max,
//        values: [$('#' + form + " ." + $class + "_from").val(), $('#' + form + " ." + $class + "_to").val()],
//        animate: true,
//        slide: function (event, ui) {
//            $('#' + form + " ." + $class + "_display").html(ui.values[0] + ' - ' + ui.values[1]);
//            $('#' + form + " ." + $class + "_from").val(ui.values[0]);
//            $('#' + form + " ." + $class + "_to").val(ui.values[1]);
//        },
//        change: function (event, ui) {
//            $('#' + form + " ." + $class + "_display").html(ui.values[0] + ' - ' + ui.values[1]);
//            $('#' + form + " ." + $class + "_from").val(ui.values[0]);
//            $('#' + form + " ." + $class + "_to").val(ui.values[1]);
//        }
//    });
//}

//function slider(form, $class, min, max, postfix) {
//    $('#' + form + " ." + $class + "_slider").slider({
//        range: "min",
//        min: min,
//        max: max,
//        value: $("#" + form + " ." + $class).val(),
//        animate: true,
//        slide: function (event, ui) {
//            $("#" + form + " ." + $class + "_display").html(ui.value + postfix);
//            $("#" + form + " ." + $class).val(ui.value);
//        },
//        change: function (event, ui) {
//            $("#" + form + " ." + $class + "_display").html(ui.value + postfix);
//            $("#" + form + " ." + $class).val(ui.value);
//        }
//    });
//}

function buildSearchForm(data) {
    if (!data) data = {};
    var values = {};
    if (typeof (data) == 'string') {
        values = $.parseJSON(data);
    }
    else {
        values = data;
    }
    $('#advanced_search input').attr('checked', false);
    $.each(values, function (index, value) {
        if (typeof (value) == 'string') {
            value = value.split(',');
        }
        $.each(value, function (vindex, vvalue) {
            if ($.trim(vvalue) != '') {
                $('#advanced_search [name="' + index + '"][value="' + vvalue + '"]').attr('checked', true);
            }
        });
    });
    var age = 18;
    if (values.age && values.age.length == 1) age = values.age[0];
    else if (values.age && typeof (values.age) == 'string') age = values.age;
    $('#advanced_search .age_slider').slider("values", 0, age);

    var maxage = 99;
    if (values.maxage && values.maxage.length == 1) maxage = values.maxage[0];
    else if (values.maxage && typeof (values.maxage) == 'string') maxage = values.maxage;
    $('#advanced_search .age_slider').slider("values", 1, maxage);

    var radius = 50;
    if (values.radius && values.radius.length == 1) radius = values.radius[0];
    else if (values.radius && typeof (values.radius) == 'string') radius = values.radius;
    $('#advanced_search .radius_slider').slider("value", radius);

    var height = 0;
    if (values.height && values.height.length == 1) height = values.height[0];
    else if (values.height && typeof (values.height) == 'string') height = values.height;
    $('#advanced_search .height_slider').slider("values", 0, height);

    var maxheight = 27;
    if (values.maxheight && values.maxheight.length == 1) maxheight = values.maxheight[0];
    else if (values.maxheight && typeof (values.maxheight) == 'string') maxheight = values.maxheight;
    $('#advanced_search .height_slider').slider("values", 1, maxheight);
}

function loadSearch() {
    var search = $('#searches').val();
    if (search && search != '0') {
        buildSearchForm(search_forms[search]);
        $('#searchName').val(search);
    }
    else {
        $('#searchName').val('');
        buildSearchForm('{}');
    }
}

function loadSearchCondition() {
    $('#searches option').not('[value="0"]').remove();
    $.each(search_forms, function (index, value) {
        $('#searches').append('<option value="' + index + '">' + index + '</option>');
    });
}

//profile.js - serializeProfileForm
function saveSearch() {
    if ($('#searchName').val() != 'Perfect Match') {
        var name = $('#searchName').val();
        var data = {'name':name, 'data': serializeProfileForm('#advanced_search')};
        var url = '/search/save';
        $.post(url, data, function (obj) {
        }).success(function (obj) {

            //Option 1
            //            var result = $.parseJSON(obj);
            //            $.each(result, function (index, value) {
            //                search_forms[index] = value;
            //            });
            //            loadSearchCondition();

            //Option 2
            search_forms[name] = data['data'];
            loadSearchCondition();
            $('#searches').val(name);

            alert('Your Search has been saved');
        }).fail(function (obj) {

        });
    } else {
        alert('Please Update your perfect match from Profile');
    }

}

function removeSearch() {
    if ($('#searchName').val() != 'Perfect Match') {
        var url = '/search/remove';
        var name = $('#searchName').val();
        $.post(url, { 'name': name }, function (obj) {
        }).success(function (obj) {
            var result = $.parseJSON(obj);

            delete search_forms[name];
            loadSearchCondition();

            alert('Your Search has been removed');
        }).fail(function (obj) {

        });
    } else {
        alert('Please Update your perfect match from Profile');
    }
}

function showResult() {
    var data =serializeProfileForm('#advanced_search');
//    $('input[name="searchcondition"]').val( JSON.stringify(data) );
    //    $('form[action="/search/result"]').submit();
    delete data['searchcondition'];
    for(var key in data){
        if(!data[key] || data[key] == '' || data[key] == '0' || data[key] == 0){
            delete data[key];
        }
    }
    window.location = "/search/result?" + $.param(data);
}





var JSON;
if (!JSON) {
    JSON = {};
}

(function () {
    'use strict';
    function f(n) {
        return n < 10 ? '0' + n : n;
    }
    if (typeof Date.prototype.toJSON !== 'function') {
        Date.prototype.toJSON = function (key) {
            return isFinite(this.valueOf())
                ? this.getUTCFullYear() + '-' +
                    f(this.getUTCMonth() + 1) + '-' +
                    f(this.getUTCDate()) + 'T' +
                    f(this.getUTCHours()) + ':' +
                    f(this.getUTCMinutes()) + ':' +
                    f(this.getUTCSeconds()) + 'Z'
                : null;
        };
        String.prototype.toJSON =
            Number.prototype.toJSON =
            Boolean.prototype.toJSON = function (key) {
                return this.valueOf();
            };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"': '\\"',
            '\\': '\\\\'
        },
        rep;

    function quote(string) {
        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === 'string'
                ? c
                : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    }

    function str(key, holder) {
        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

        switch (typeof value) {
            case 'string':
                return quote(value);
            case 'number':
                return isFinite(value) ? String(value) : 'null';
            case 'boolean':
            case 'null':
                return String(value);
            case 'object':

                if (!value) {
                    return 'null';
                }
                gap += indent;
                partial = [];
                if (Object.prototype.toString.apply(value) === '[object Array]') {
                    length = value.length;
                    for (i = 0; i < length; i += 1) {
                        partial[i] = str(i, value) || 'null';
                    }
                    v = partial.length === 0
                    ? '[]'
                    : gap
                    ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
                    : '[' + partial.join(',') + ']';
                    gap = mind;
                    return v;
                }
                if (rep && typeof rep === 'object') {
                    length = rep.length;
                    for (i = 0; i < length; i += 1) {
                        if (typeof rep[i] === 'string') {
                            k = rep[i];
                            v = str(k, value);
                            if (v) {
                                partial.push(quote(k) + (gap ? ': ' : ':') + v);
                            }
                        }
                    }
                } else {
                    for (k in value) {
                        if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = str(k, value);
                            if (v) {
                                partial.push(quote(k) + (gap ? ': ' : ':') + v);
                            }
                        }
                    }
                }
                v = partial.length === 0
                ? '{}'
                : gap
                ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
                : '{' + partial.join(',') + '}';
                gap = mind;
                return v;
        }
    }
    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {
            var i;
            gap = '';
            indent = '';
            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }
                indent = space;
            }
            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                    typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }
            return str('', { '': value });
        };
    }
} ());
