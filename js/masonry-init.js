jQuery(window).load(function(){

wlhidespinner();
jQuery("#gridcontainer").fadeIn(2000);
//document.getElementById('gridcontainer').style.display = "initial";
jQuery('.grid').masonry({
  // options
	itemSelector: '.grid-item',
	gutter: 0
});



});
