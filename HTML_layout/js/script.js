var zoom = 15;
var latitude = 41.040585;

var longitude = 28.970257;






$(document).ready(function() {
    
    $('select.top-cat-menu').change(function() {
        var loc = ($(this).find('option:selected').val());
 window.location = loc;
    
    });
     
     
    $('[data-hover="dropdown"]').dropdownHover();
    $('.basket .close-btn').click(function() {
        $(this).parent().parent().fadeOut(function() {
            $(this).remove();
            checkBasketDropdown(true);
        });
    });

    if ($('.price-slider').length > 0) {
        $('.price-slider').slider({
            min: 100,
            max: 700,
            step: 10,
            value: [100, 400],
            handle: "square"

        })
    }
    
    
    if ($('.accordion-widget').length > 0) {
        $('.category-accordions .accordion-body').parent().find('.accordion-toggle').toggleClass('collapsed');

        $('.category-accordions .accordion-body').collapse("hide");


        $('.accordion-body').on('hidden', function() {



        });

        $('.accordion-body').on('shown', function() {

        });

    }
   $(".shipping-form-area input[type='checkbox']").on('ifChecked', function(event){
   
        $(".shipping-form-area .cusmo-input").attr('disabled','disabled');
    
     
    
});

    $(".shipping-form-area input[type='checkbox']").on('ifUnchecked', function(event){
   
       
          $(".shipping-form-area .cusmo-input").removeAttr('disabled');
    
});


if($('#clients-carousel').length>0){
   $("#clients-carousel").carouFredSel({
	circular: true,
	infinite: false,
     
	auto : false, 
	prev	: {	
		button	: ".carousel_prev",
		key		: "left",
                 easing:"easeInOutExpo",
                  duration:1000
	},
	next	: { 
		button	: ".carousel_next",
		key		: "right",
                 easing:"easeInOutExpo",
                  duration:1000
	}
	
});
}  
       
        if($('.map-holder').length>0){
          
            $('.map').gmap3({
                map:{
                    options:{
                        center:[latitude, longitude],
     
                        zoom:zoom,
                        mapTypeControl: true,
   
                        navigationControl: true,
                        scrollwheel: false,
                        streetViewControl: true
                    }
                },
                marker:{
                    latLng:[latitude, longitude]
                }
            });
          
        }
    
    
    
 $('.delete .close-btn').on('click', function(e) {
       e.preventDefault();
       $(this).parent().parent().parent().fadeOut(function(){
           $(this).remove();
       });
 });
 if($('.iCheck').length>0){
 $('.iCheck').iCheck({
    checkboxClass: 'icheckbox_minimal-yellow',
    radioClass: 'iradio_minimal-yellow',
   
  });
 }
    checkBasketDropdown();
    function checkBasketDropdown(remove) {
        if (remove) {
            cn = parseInt($('.basket-item-count').text());
            nn = cn - 1;
            $('.basket-item-count').text(nn);
        }
        if ($('.basket .basket-item').length <= 0) {
            $('.basket .dropdown-menu').prepend("<li class='empty'>Empty</li>");
        }
    }

    $('.active.tab-pane').fadeIn();

    $('.nav-tabs li a').on('click', function(e) {
        e.preventDefault();

        var dest = $($(this).attr('href'));
        $('.products-holder.active').stop(true, true).fadeOut('slow', function() {
            dest.stop(true, true).fadeIn('slow');
        });

    });

    $('.grid-list-buttons a').on('click', function(e) {
        e.preventDefault();

        var dest = $($(this).attr('href'));
        $('.products-holder.active').stop(true, true).fadeOut(function() {
            dest.stop(true, true).fadeIn();
        });

    });

    $('a.load-more').click(function(e) {
        e.preventDefault();
    });
    if($(".chosen-select").length>0){
        $(".chosen-select").chosen({disable_search_threshold: 10});
    }
    
    checkReviewField();
function textCounter(field,field2,maxlimit){
 

 if ( field.val().length > maxlimit ) {
  field.val(field.val().substring( 0, maxlimit )) ;
  return false;
 } else {
  field2.text(maxlimit - field.val().length);
 }
 
}
function checkReviewField(){
if($('#write-review-text').length>0){
    $('#write-review-text').keyup(function(){ 
        textCounter($('#write-review-text'),$('#counter'),210);
    })
}
}
    if ($('.product-gallery').length > 0) {
        $('.product-gallery').flexslider({
            animation: "slide",
            pauseOnAction: false,
            directionNav: false,
            
            controlNav: "thumbnails"
        });
    }

    if ($('.star').length > 0) {
        $('.star').raty({
            starOff: 'images/star-off.png',
            starOn: 'images/star-on.png',
            score: function() {
                return $(this).attr('data-score');
            }
        });
    }



    setupHomeSlider();

    
    


    if ($('.gallery').length > 0) {
        if ($(".group-holder.active").length == 0) {

            gr = $('.gallery-controls  li.active').attr('data-group');
            $(".group-holder[data-group=" + gr + "]").addClass('active');

        }

        w = $('.gallery-item').width();
        h = $('.gallery-item').width();
        $('.group-block .thumb').append('<img src="images/blank.png" alt="" width="' + w + '" height="' + h + '">');
    }
    $('.gallery-controls  li').click(function() {
        gr = $(this).attr('data-group');
        el = $(this);
        $('.gallery-controls  li').removeClass('active');
        el.addClass('active');
        $(".group-holder").not(".group-holder[data-group=" + gr + "]").fadeOut(function() {
            old = $(this);
            old.removeClass('active');
            $(".group-holder[data-group=" + gr + "]").fadeIn(function() {

                $(this).addClass('active');

            });
        });

    });

    function setupHomeSlider() {


        if ($('.home-slider .flexslider').length > 0) {

            $('.home-slider .flexslider').flexslider({
                prevText: "",
                nextText: "",
                slideshow: true,
                controlNav: false



            });

        }

    }

    $('[data-placeholder]').focus(function() {
        var input = $(this);
        if (input.val() == input.attr('data-placeholder')) {
            input.val('');
            // input.removeClass('placeholder');
        }
    }).blur(function() {
        var input = $(this);
        if (input.val() == '' || input.val() == input.attr('data-placeholder')) {
            input.addClass('placeholder');
            input.val(input.attr('data-placeholder'));
        }
    }).blur();

    $('[data-placeholder]').parents('form').submit(function() {
        $(this).find('[data-placeholder]').each(function() {
            var input = $(this);
            if (input.val() == input.attr('data-placeholder')) {
                input.val('');
            }
        })
    });





    checkContactForm();
    function checkContactForm() {
        if ($(".contact-form").length > 0) {


            //  triggers contact form validation
            var formStatus = $(".contact-form").validate();
            //   ===================================================== 
            //sending contact form
            $(".contact-form").submit(function(e) {
                e.preventDefault();

                if (formStatus.errorList.length === 0)
                {
                    $(".contact-form .submit").fadeOut(function() {
                        $('#loading').css('visibility', 'visible');
                        $.post('submit.php', $(".contact-form").serialize(),
                                function(data) {
                                    $(".contact-form input,.contact-form textarea").not('.submit').val('');

                                    $('.message-box').html(data);


                                    $('#loading').css('visibility', 'hidden');
                                    $(".contact-form .submit").removeClass('disabled').css('display', 'block');
                                }

                        );
                    });


                }

            });
        }
    }
    
    

    

});


