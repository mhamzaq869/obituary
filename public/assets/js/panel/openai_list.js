( () => {

	const filterButtons = [ ...document.querySelectorAll( '[data-filter-trigger]' ) ];
	const items = [ ...document.querySelectorAll( '[data-filter]' ) ];

	if ( !filterButtons.length || !items.length ) return;

	filterButtons.forEach( btn => {
		btn.addEventListener( 'click', event => {
			event.preventDefault();
			const filterVal = btn.getAttribute( 'data-filter-trigger' );
			let itemsToShow = [];
			let itemsToHide = [];
			if ( filterVal === 'all' ) {
				itemsToShow = items;
			} else {
				itemsToShow = items.filter( item => {
					const itemFilters = item.getAttribute( 'data-filter' ).split( ',' ).map( f => f.trim() );
					return itemFilters.includes( filterVal )
				} );
				itemsToHide = items.filter( item => {
					const itemFilters = item.getAttribute( 'data-filter' ).split( ',' ).map( f => f.trim() );
					return !itemFilters.includes( filterVal )
				} );
			}
			itemsToShow.forEach( item => item.style.display = 'block' );
			itemsToHide.forEach( item => item.style.display = 'none' );
			filterButtons.forEach( filterBtn => filterBtn.classList.remove( 'active' ) );
			btn.classList.add( 'active' );
		} );
	} )

} )();

function favoriteTemplate( id ) {
	"use strict";

	var formData = new FormData();
	formData.append( 'id', id );

	$.ajax( {
		type: "post",
		url: "/dashboard/user/openai/favorite   ",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			$( "#favorite_area_" + id ).html( data.html );
		},
	} );
	return false;
}


 /************** Wizard Form ********************/
 var template_id = 0;

 $(document).ready(function() {
     var child = 1;
     var length = $("section").length - 1;
     $("#prev").addClass("disabled");
     $("#submit").addClass("disabled");

     $("section").not("section:nth-of-type(1)").hide();
     $("section").not("section:nth-of-type(1)").css('transform', 'translateX(100px)');

     $(".button").click(function() {

         var id = $(this).attr("id");
         if (id == "next") {
             if(template_id != 0){
                 $("#prev").removeClass("disabled");
                 if (child >= length) {
                     $(this).addClass("disabled");
                     $('#submit').removeClass("disabled");
                 }
                 if (child <= length) {
                     child++;
                 }
             }else{
                $("#errorMessage").text('Please choose template')
             }
         } else if (id == "prev") {
             $("#next").removeClass("disabled");
             $('#submit').addClass("disabled");
             if (child <= 2) {
                 $(this).addClass("disabled");
             }
             if (child > 1) {
                 child--;
             }
         }

         var currentSection = $("section:nth-of-type(" + child + ")");
         currentSection.fadeIn();
         currentSection.css('transform', 'translateX(0)');
         currentSection.prevAll('section').css('transform', 'translateX(-100px)');
         currentSection.nextAll('section').css('transform', 'translateX(100px)');
         $('section').not(currentSection).hide();
     });

 });

 function selectTemplate(id) {
     template_id = id
     $("#errorMessage").text('')
     setCookie("template_id", template_id, 30)

 }

 function setCookie(name,value,days) {
     var expires = "";
     if (days) {
         var date = new Date();
         date.setTime(date.getTime() + (days*24*60*60*1000));
         expires = "; expires=" + date.toUTCString();
     }
     document.cookie = name + "=" + (value || "")  + expires + "; path=/";
 }
