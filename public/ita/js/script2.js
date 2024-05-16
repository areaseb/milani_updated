// JavaScript Document
$(document).ready(function() {
 "use strict";
  $("#about").owlCarousel({
 	autoPlay: 3000, //Set AutoPlay to 3 seconds
 	items : 1,
	itemsDesktop : [1199,2],
	itemsDesktopSmall : [979,2]
});
});

// taxi datepick script
$(document).ready(function() {
 "use strict";
$('#movedate').datepick({dateFormat: 'dd-mm-yyyy'}); 
});

// form valiation script
$(document).ready(function(e) {
    "use strict";
	$("#booking-form").validate();
})
// form valiation script
$(document).ready(function(e) {
    "use strict";
	$("#newsletter").validate();
});