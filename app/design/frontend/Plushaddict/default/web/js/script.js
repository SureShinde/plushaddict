// JavaScript Document
require(['jquery'], function($){ /* Code... */ 
    $('.owl-carousel').owlCarousel({
    loop:true,
    autoplay:true,
    margin:0,
    nav:false,
    pagination:false,
    smartSpeed:5000,
    autoplayTimeout:10000,
    animateIn: 'fadeIn', 
    animateOut: 'fadeOut',
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:1
        }
    }
})

$('.owl-carousel1').owlCarousel({
    loop:true,
    margin:30,
    nav:true,
    pagination:false,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:3
        }
    }
})


$('.product.media a.patten_info_section').click(function(){
    $('html, body').animate({
        scrollTop: $( $(this).attr('href') ).offset().top
    }, 2000);
    return false;
});

})