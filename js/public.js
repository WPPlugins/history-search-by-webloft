// Facebook sharer window

function fbShare(url, winWidth, winHeight) {
	var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
	window.open('http://www.facebook.com/sharer.php?u=' + url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
    }

// hide missing images 
jQuery("img").error(function(){
        $(this).hide();
});

// Masonry

jQuery(document).ready(function(){
	jQuery('img').load(function(){
		jQuery(".grid").masonry();
	});
	jQuery(".grid").masonry();

});

function wlshowspinner() {
   var div = document.getElementById('wlspinner');
     div.style.display = 'block';
}

function wlhidespinner() {
   var div = document.getElementById('wlspinner');
     div.style.display = 'none';
}


