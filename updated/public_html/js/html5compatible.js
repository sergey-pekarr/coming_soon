/* Placeholder
 * placeholder attribute fallback
 *
 * using jQuery Placehold plugin by Viget Inspire(http://www.viget.com/inspire/)
 * http://www.viget.com/inspire/a-jquery-placeholder-enabling-plugin/
 */
var initPlaceholder = function() {
    $('input[placeholder]').placehold({
        placeholderClassName: 'placeholder'
    });
    $('textarea[placeholder]').placehold({
        placeholderClassName: 'placeholder'
    });
};

if( !Modernizr.input.placeholder ){
	$(document).ready(initPlaceholder);
};

