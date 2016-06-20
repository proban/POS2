function UpdateTableHeaders() {
	

   $(".persist-area").each(function() {
   
	   var el             = $(this),
		   offset         = el.offset(),
		   scrollTop      = $(window).scrollTop(),
		   floatingHeader = $(".floatingHeader", this);


		console.log('scrollTop:' + scrollTop);
		console.log('offset.top:' + offset.top);
		console.log('el.height:' + el.height());

	   
	   if ((scrollTop > offset.top) && (scrollTop < offset.top + el.height())) {
	   	   console.log('sudah lewat');
	   	   
		   floatingHeader.css({
			"visibility": "visible"
		   });
	   } else {
		   floatingHeader.css({
			"visibility": "hidden"
		   });      
	   };
   });
}

// DOM Ready      
$(function() {

   var clonedHeaderRow;

   $(".persist-area").each(function() {
	   clonedHeaderRow = $(".persist-header", this);
	   clonedHeaderRow
		 .before(clonedHeaderRow.clone())
		 .css("width", clonedHeaderRow.width())
		 .addClass("floatingHeader");
		 
   });
   
   $(window)
	.scroll(UpdateTableHeaders)
	.trigger("scroll");
   
});