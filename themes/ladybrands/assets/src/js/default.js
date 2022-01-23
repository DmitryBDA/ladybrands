import { Fancybox } from "@fancyapps/ui/src/Fancybox/Fancybox.js";

//jQuery-плагин для установки курсора в определенной позиции pos:
$.fn.setCursorPosition = function(pos) {
  if ($(this).get(0).setSelectionRange) {
    $(this).get(0).setSelectionRange(pos, pos);
  } else if ($(this).get(0).createTextRange) {
    var range = $(this).get(0).createTextRange();
    range.collapse(true);
    range.moveEnd('character', pos);
    range.moveStart('character', pos);
    range.select();
  }
};
//маска для телефона
$("._input_phone").click(function(){
  $(this).setCursorPosition(2);
}).mask("+79999999999");

$(window).on('ajaxErrorMessage', function(event, message){

  // This can be any custom JavaScript you want
  $.oc.flashMsg({text: message, class: 'error', })
  // This will stop the default alert() message
  event.preventDefault();

})

/*$(document).on('click', '._add_to_wishlist', function (event){
  event.preventDefault()
  const productId = $(this).attr('data-product-id')
  $.request('ProductList::onAddToWishList', {
    data: {'product_id': productId},
    update: {
      'header/wishlist': '._wishlist_wrapper',
    },
    success: function(obResponse) {
      this.success(obResponse)
    },
    loading: $.oc.stripeLoadIndicator
  });

})

$(document).on('click', '._delete_from_wishlist', function (event){
  event.preventDefault()
  const productId = $(this).attr('data-product-id')
  $.request('ProductList::onRemoveFromWishList', {
    data: {'product_id': productId},
    update: {
      'header/wishlist': '._wishlist_wrapper',
    },
    success: function(obResponse) {
      this.success(obResponse)
    },
    loading: $.oc.stripeLoadIndicator
  });

})*/

$(document).on('click', '._btn_wishlist', function (event){
  event.preventDefault()
  const btnClick = $(this)
  const productId = btnClick.attr('data-product-id')
  let action = ''
  let elemLi
  elemLi = btnClick.find('.fa-plus');
  if(!elemLi.hasClass('fa-plus'))
  {
    elemLi = btnClick.find('.fa-heart-o');
  }

  if(elemLi.length)
  {
    action = 'ProductList::onAddToWishList'
  } else {
    action = 'ProductList::onRemoveFromWishList'

    elemLi = btnClick.find('.fa-minus');
    if(!elemLi.hasClass('fa-minus'))
    {
      elemLi = btnClick.find('.fa-heart');
    }
  }
  $.request(action, {
    data: {'product_id': productId},
    update: {
      'header/wishlist': '._wishlist_wrapper',
    },
    success: function(obResponse) {
      this.success(obResponse)
      if(elemLi.hasClass('fa-plus')){
        btnClick.html('<i class="fa fa-minus"></i> Удалить из избранного')
        $.oc.flashMsg({text: 'Товар добавлен в избранное.', class: 'success', })
      }
      if(elemLi.hasClass('fa-minus')) {
        btnClick.html('<i class="fa fa-plus"></i> Добавить в избранное')
        $.oc.flashMsg({text: 'Товар удален из избранного.', class: 'success', })
      }
      if(elemLi.hasClass('fa-heart')) {
        btnClick.html('<i class="fa fa-heart-o"></i>')
        $.oc.flashMsg({text: 'Товар удален из избранного.', class: 'success', })
      }
      if(elemLi.hasClass('fa-heart-o')) {
        btnClick.html('<i class="fa fa-heart"></i>')
        $.oc.flashMsg({text: 'Товар добавлен в избранное.', class: 'success', })
      }
    },
    loading: $.oc.stripeLoadIndicator
  });

})

$(document).on('click', '._wishlist_remove', function (event){
  event.preventDefault()
  const productId = $(this).attr('data-product-id')

  $.request('ProductList::onRemoveFromWishList', {
    data: {'product_id': productId},
    update: {
      'header/wishlist': '._wishlist_wrapper',
      'wishlist-page/position': '._wishlist_list_wrapper',
    },
    success: function(obResponse) {
      this.success(obResponse)
    },
    loading: $.oc.stripeLoadIndicator
  });
})

$(document).on('click', '._wishlist_clear', function (event){
  event.preventDefault()
  $.request('ProductList::onClearWishList', {
    update: {
      'header/wishlist': '._wishlist_wrapper',
      'wishlist-page/position': '._wishlist_list_wrapper',
    },
    success: function(obResponse) {
      this.success(obResponse)
    },
    loading: $.oc.stripeLoadIndicator
  });
})

$(document).on('submit', '._add_reviews', function (e){

  e.preventDefault();

  const data = $(this).serializeArray()

  $.request('reviews::onAddReview', {
    data: data,
    method:'post',
    update: {

    },
    success: function(obResponse) {
      this.success(obResponse);

      $.oc.flashMsg({text: 'Отзыв добавлен', class: 'success', })


    },
    loading: $.oc.stripeLoadIndicator
  })

});


$('._rating_mini').on('click', function (){
  let rating = $(this).attr('data-num-rating');
  $('#input_num_rating').attr('value',rating);

  let allZv = $("[data-num-rating]");
  allZv.removeClass('active');
  let i = 0;
  for (; i < rating; i++){
    $(allZv[i]).addClass('active');
  }
});
