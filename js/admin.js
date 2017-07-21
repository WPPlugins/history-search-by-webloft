// Shortcode generator

jQuery(document).ready(function() {

jQuery("#shortcodeform").change(function() {

var basene = jQuery.map(jQuery(':checkbox[name=baser\\[\\]]:checked'), function(n, i){
      return n.value;
}).join(', ');

var visning = jQuery('select[name="visning"]').val();
var makstreff = jQuery('select[name="makstreff"]').val();
var wltreffperside = jQuery('select[name="wltreffperside"]').val();
var sortering = jQuery('select[name="sortering"]').val();
var wlgjemverktoyover = jQuery('#wltoolbarhideover').prop('checked') ? 1 : 0;
var wlgjemverktoyunder = jQuery('#wltoolbarhideunder').prop('checked') ? 1 : 0;

jQuery("#ferdigshortcode").html('[wl-kultursok baser="' + basene + '" visning="' + visning + '" makstreff="' + makstreff + '" sortering="' + sortering + '" wlgjemverktoyover="' + wlgjemverktoyover + '" wlgjemverktoyunder="' + wlgjemverktoyunder + '" wltreffperside="' + wltreffperside + '"]');

document.getElementById('ferdigshortcode').style.display = "block";

})
})

jQuery.fn.selectText = function(){
    var doc = document
        , element = this[0]
        , range, selection
    ;
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();        
        range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
};

jQuery(function() {
    jQuery('#ferdigshortcode').click(function() {
        jQuery('#ferdigshortcode').selectText();
    });
});


