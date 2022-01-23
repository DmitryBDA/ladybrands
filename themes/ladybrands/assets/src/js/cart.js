import ShopaholicCartAdd from '@lovata/shopaholic-cart/shopaholic-cart-add';

const obShopaholicCartAdd = new ShopaholicCartAdd();
obShopaholicCartAdd.setAjaxRequestCallback((obRequestData, obButton) => {

  obRequestData.update = {
    'header/cart-info': '._basket_wrapper',
    'components/catalog/productsViewGrid': `._grid_view_wrapper`,
    'components/catalog/productsViewList': `._list_view_wrapper`,
  };
  obRequestData.complete = () => {
    $.oc.flashMsg({text: 'Товар добавлен в корзину. <br> <a class="text-white" href="/cart" style="text-decoration: underline">Перейти в корзину</a>', class: 'success', })
    $(obButton).removeClass('_shopaholic-cart-add')
    $(obButton).attr('href', '/cart')
    $(obButton).text('Добавлено')


  }

  obRequestData.loading = $.oc.stripeLoadIndicator;
  return obRequestData;
}).init();

