// JavaScript Document
require(['jquery'], function($){ /* Code... */ 
   $(document).ready(function() {
        $(".vertical-left ul li.dropdown").hover(function(){
        	//console.log($(this).index());
        	var multiplier = $(this).index()+1; 
        	//console.log(multiplier);
        	var hight = multiplier*38;
        	$(this).find('ul.animated').css( 'min-height', hight + 'px' );
		}) 
    });
})