import ShopaholicCartRemove from '@lovata/shopaholic-cart/shopaholic-cart-remove';
import ShopaholicCartUpdate from '@lovata/shopaholic-cart/shopaholic-cart-update';

const obShopaholicCartUpdate = new ShopaholicCartUpdate();
obShopaholicCartUpdate.setAjaxRequestCallback((obRequestData, obInput) => {
  obRequestData.update = {
    'header/cart-info': '._basket_wrapper',
    'cart-page/cart-position': '._cart_list_wrapper',
  }

  obRequestData.complete = () => {
    $.oc.flashMsg({text: 'Обновлено', class: 'success', })

  }
  obRequestData.loading = $.oc.stripeLoadIndicator;
  return obRequestData;
}).init();


const obShopaholicCartRemove = new ShopaholicCartRemove();
obShopaholicCartRemove.setAjaxRequestCallback((obRequestData, obButton) => {

  obRequestData.update = {
    'header/cart-info': '._basket_wrapper',
    'cart-page/cart-position': '._cart_list_wrapper',
    'cart-page/create-order': '._create_order_wrapper',
  }

  obRequestData.loading = $.oc.stripeLoadIndicator;
  return obRequestData;
}).init();


$(document).on('click', '._clear_basket', function() {
  event.preventDefault();

  $.request('Cart::onClear', {

    update: {
      'header/cart-info': '._basket_wrapper',
      'cart-page/cart-position': '._cart_list_wrapper',
      'cart-page/create-order': '._create_order_wrapper',
    },
    success: function(obResponse) {
      this.success(obResponse);
    },
    loading: $.oc.stripeLoadIndicator
  });
});

// создание заказа
$(document).on("submit", '._shopaholic-order-form', (function() {
  const isEmpty = function(str) {
    if (str.trim() == '') {
      return true;
    } else {
      return false;
    }
  }

  const userName = $('[name="name"]').val();
  const userPhone = $('[name="phone"]').val();
  const userEmail = $('[name="email"]').val();
  const userAddress = $('[name="address"]').val();

  const paymentID = $('[name="payment_method"]').val();
  const shippingID = $('[name="shipping_type"]').val();

  const regEx = /^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*)/;
  let validEmail = regEx.test(userEmail);
  if(isEmpty(userName) || isEmpty(userPhone) || isEmpty(userEmail) || !validEmail) {

  } else {
    event.preventDefault();
    const data = {
      'order': {
        'payment_method_id': paymentID,
        'shipping_type_id': shippingID,
        'property': {
          'shipping_address1': userAddress,
        }
      },
      'user': {
        'name': userName,
        'phone': userPhone,
        'email': userEmail,
      }
    };

    $.request('MakeOrder::onCreate', {
      data: data,
      success: function(obResponse){
        this.success(obResponse);
      },
      loading: $.oc.stripeLoadIndicator,
    });
  }

}));
