/**
 * @note DO NOT PLACE CODE IN THIS FUNCTION
 * @todo Place this in its own file
 */
jQuery(document).ready(function($) {
    function dropdownMouseover() { $(this).children('ul').slideDown(); }
    function dropdownMouseout() { $(this).children('ul').slideUp(); }
    var config = {
        over: dropdownMouseover,
        timeout: 500,
        out: dropdownMouseout
    }
    $('.dropdown').hoverIntent(config);
});