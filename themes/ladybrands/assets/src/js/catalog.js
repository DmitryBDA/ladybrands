
class ShopaholicProductList {
  constructor() {
    this.sComponentMethod = 'onAjax';
    this.obAjaxRequestCallback = null;
  }

  /**
   * Add product to wish list
   * @param {int} iProductID
   * @param obButton
   */
  send(obRequestData = {}) {

    if (this.obAjaxRequestCallback !== null) {
      obRequestData = this.obAjaxRequestCallback(obRequestData);
    }

    $.request(this.sComponentMethod, obRequestData);
  }

  /**
   * Set ajax request callback
   *
   * @param {function} obCallback
   * @returns {ShopaholicProductList}
   */
  setAjaxRequestCallback(obCallback) {
    this.obAjaxRequestCallback = obCallback;

    return this;
  }
}

class UrlGeneration {
  constructor() {
    this.sBaseURL = `${location.origin}${location.pathname}`;
    this.init();
  }

  init() {
    this.sSearchString = window.location.search.substring(1);
    this.obParamList = {};
    let arPartList = this.sSearchString.split('&');
    arPartList.forEach((sParam) => {
      let iPosition = sParam.indexOf("=");
      if (iPosition < 0) {
        return;
      }

      let sFiled = sParam.substring(0, iPosition),
        sValue = sParam.substring(iPosition + 1);
      if (!sFiled && !sValue) {
        return;
      }

      this.obParamList[sFiled] = sValue.split('|');
    });
  }

  clear() {
    this.obParamList = {};

    history.pushState(null, null, `${this.sBaseURL}`);
  }

  update() {
    this.generateSearchString();

    if (Object.keys(this.obParamList).length > 0) {
      history.pushState(null, null, `${this.sBaseURL}?${this.sSearchString}`);
    } else {
      history.pushState(null, null, `${this.sBaseURL}`);
    }
  }

  generateSearchString() {
    let arFieldList = Object.keys(this.obParamList);

    this.sSearchString = '';
    arFieldList.forEach((sField) => {
      if (this.sSearchString.length > 0) {
        this.sSearchString += '&'
      }

      this.sSearchString += `${sField}=${this.obParamList[sField].join('|')}`;
    });
  }

  /**
   * Set field value in URL
   * @param sFiled
   * @param obValue
   */
  set(sFiled, obValue) {
    if (!sFiled || !obValue) {
      return;
    }

    if (typeof obValue == 'string') {
      obValue = [obValue];
    }

    this.obParamList[sFiled] = obValue;
  }

  /**
   * Remove field value from URL
   * @param {string} sFiled
   */
  remove(sFiled) {
    if (!sFiled || !this.obParamList.hasOwnProperty(sFiled)) {
      return;
    }

    delete this.obParamList[sFiled];
  }
}

const urlGenerationHelper = new UrlGeneration()

class ShopaholicFilterPrice {
  /**
   * @param {ShopaholicProductList} obProductListHelper
   */
  constructor(obProductListHelper = null) {
    this.obProductListHelper = obProductListHelper;
    this.sEventType = 'change';
    this.sFiledName = 'price';

    this.sInputMinPriceName = 'filter-min-price';
    this.sInputMaxPriceName = 'filter-max-price';

    this.sDefaultInputClass = '_shopaholic-price-filter';
    this.sInputSelector = `.${this.sDefaultInputClass}`;

    this.iCallBackDelay = 400;
  }

  /**
   * Init event handlers
   */
  init() {
    $(document).on(this.sEventType, this.sInputSelector, () => {
      if (this.sEventType === 'input') {
        clearTimeout(this.timer);

        this.timer = setTimeout(() => {
          this.priceChangeCallBack();
        }, this.iCallBackDelay);
      } else {
        this.priceChangeCallBack();
      }
    });

    $(document).on('input', this.sInputSelector, ({ currentTarget }) => {
      const { value } = currentTarget;
      const correctValue = value.replace(/[^\d.]/g, '');

      currentTarget.value = correctValue; // eslint-disable-line no-param-reassign
    });
  }

  priceChangeCallBack() {
    urlGenerationHelper.init();
    this.prepareRequestData();

    urlGenerationHelper.remove('page');
    urlGenerationHelper.update();
    if (!this.obProductListHelper) {
      return;
    }

    this.obProductListHelper.send();
  }

  prepareRequestData() {
    // Get min price from filter input
    const obInputList = $(this.setInputSelector);
    const obMinInput = obInputList.find(`[name=${this.sInputMinPriceName}]`);
    const obMaxInput = obInputList.find(`[name=${this.sInputMaxPriceName}]`);
    const fMinLimit = parseFloat(obMinInput.attr('min'));
    const fMaxLimit = parseFloat(obMinInput.attr('max'));

    let fMinPrice = obMinInput.val();
    let fMaxPrice = obMaxInput.val();

    if (fMinPrice > 0 && fMinPrice < fMinLimit) {
      fMinPrice = fMinLimit;
      obMinInput.val(fMinLimit);
    }

    if (fMaxPrice > 0 && fMaxPrice > fMaxLimit) {
      fMaxPrice = fMaxLimit;
      obMaxInput.val(fMaxLimit);
    }

    if (fMinPrice === 0 && fMaxPrice === 0) {
      urlGenerationHelper.remove(this.sFiledName);
    } else {
      urlGenerationHelper.set(this.sFiledName, [fMinPrice, fMaxPrice]);
    }
  }

  /**
   * Redeclare default selector of filter input
   * Default value is "_shopaholic-price-filter"
   *
   * @param {string} sInputSelector
   * @returns {ShopaholicFilterPrice}
   */
  setInputSelector(sInputSelector) {
    this.sInputSelector = sInputSelector;

    return this;
  }

  /**
   * Redeclare default event type
   * Default value is "change"
   *
   * @param {string} sEventType
   * @returns {ShopaholicFilterPrice}
   */
  setEventType(sEventType) {
    this.sEventType = sEventType;

    return this;
  }

  /**
   * Redeclare default input name with min price
   * Default value is "filter-min-price"
   *
   * @param {string} sInputName
   * @returns {ShopaholicFilterPrice}
   */
  setInputMinPriceName(sInputName) {
    this.sInputMinPriceName = sInputName;

    return this;
  }

  /**
   * Redeclare default input name with max price
   * Default value is "filter-max-price"
   *
   * @param {string} sInputName
   * @returns {ShopaholicFilterPrice}
   */
  setInputMaxPriceName(sInputName) {
    this.sInputMaxPriceName = sInputName;

    return this;
  }

  /**
   * Redeclare default URL filed name
   * Default value is "price"
   *
   * @param {string} sFieldName
   * @returns {ShopaholicFilterPrice}
   */
  setFieldName(sFieldName) {
    this.sFiledName = sFieldName;

    return this;
  }
}

class ShopaholicFilterPanel {
  /**
   * @param {ShopaholicProductList} obProductListHelper
   */
  constructor(obProductListHelper = null) {
    this.obProductListHelper = obProductListHelper;
    this.sEventType = 'change';
    this.sFiledName = 'property';
    this.sFilterType = 'data-filter-type';
    this.sPropertyIDAttribute = 'data-property-id';

    this.sDefaultWrapperClass = '_shopaholic-filter-wrapper';
    this.sWrapperSelector = `.${this.sDefaultWrapperClass}`;
  }

  /**
   * Init event handlers
   */
  init() {
    $(document).on(this.sEventType, this.sWrapperSelector, () => {
      urlGenerationHelper.init();
      this.prepareRequestData();

      urlGenerationHelper.remove('page');
      urlGenerationHelper.update();
      if (!this.obProductListHelper) {
        return;
      }

      this.obProductListHelper.send();
    });
  }

  prepareRequestData() {
    const obFilterList = $(this.sWrapperSelector);
    if (obFilterList.length == 0) {
      return;
    }

    obFilterList.each((iNumber) => {
      //Get filter type
      const obWrapper = $(obFilterList[iNumber]),
        sFilterType = obWrapper.attr(this.sFilterType),
        iPropertyID = obWrapper.attr(this.sPropertyIDAttribute);

      let sFieldName = `${this.sFiledName}`;
      if (!sFilterType) {
        return;
      }

      if (iPropertyID) {
        sFieldName += `[${iPropertyID}]`;
      }

      let obInputList = null,
        arValueList = [];

      if (sFilterType == 'between') {
        obInputList = obWrapper.find('input');
      } else if (sFilterType == 'checkbox' || sFilterType == 'switch') {
        obInputList = obWrapper.find('input[type="checkbox"]:checked');
      } else if (sFilterType == 'select' || sFilterType == 'select_between') {
        obInputList = obWrapper.find('select');
      } else if (sFilterType == 'radio') {
        obInputList = obWrapper.find('input[type="radio"]:checked');
      }

      if (!obInputList || obInputList.length == 0) {
        urlGenerationHelper.remove(sFieldName);
        return;
      }

      obInputList.each((iInputNumber) => {
        const sValue = $(obInputList[iInputNumber]).val();
        if (!sValue) {
          return;
        }

        arValueList.push(sValue);
      });

      if (!arValueList || arValueList.length == 0) {
        urlGenerationHelper.remove(sFieldName);
      } else {
        urlGenerationHelper.set(sFieldName, arValueList);
      }
    });
  }

  /**
   * Redeclare default selector of filter input
   * Default value is "_shopaholic-filter-wrapper"
   *
   * @param {string} sWrapperSelector
   * @returns {ShopaholicFilterPanel}
   */
  setWrapperSelector(sWrapperSelector) {
    this.sWrapperSelector = sWrapperSelector;

    return this;
  }

  /**
   * Redeclare default event type
   * Default value is "change"
   *
   * @param {string} sEventType
   * @returns {ShopaholicFilterPanel}
   */
  setEventType(sEventType) {
    this.sEventType = sEventType;

    return this;
  }

  /**
   * Redeclare default URL filed name
   * Default value is "property"
   *
   * @param {string} sFieldName
   * @returns {ShopaholicFilterPanel}
   */
  setFieldName(sFieldName) {
    this.sFiledName = sFieldName;

    return this;
  }
}

class ShopaholicPagination {
  /**
   * @param {ShopaholicProductList} obProductListHelper
   */
  constructor(obProductListHelper = null) {
    this.obProductListHelper = obProductListHelper;
    this.sEventType = 'click';
    this.sFiledName = 'page';
    this.sAttributeName = 'data-page';

    this.sDefaultButtonClass = '_shopaholic-pagination';
    this.sButtonSelector = `.${this.sDefaultButtonClass}`;
  }

  /**
   * Init event handlers
   */
  init() {
    $(document).on(this.sEventType, this.sButtonSelector, (obEvent) => {
      obEvent.preventDefault();
      obEvent.stopPropagation();

      const obButton = $(obEvent.currentTarget),
        iPage = obButton.attr(this.sAttributeName);

      urlGenerationHelper.init();
      if (iPage == 1) {
        urlGenerationHelper.remove(this.sFiledName);
      } else {
        urlGenerationHelper.set(this.sFiledName, [iPage]);
      }

      urlGenerationHelper.update();
      if (!this.obProductListHelper) {
        return;
      }

      this.obProductListHelper.send();
    });
  }

  /**
   * Redeclare default selector of pagination button
   * Default value is "_shopaholic-pagination"
   *
   * @param {string} sButtonSelector
   * @returns {ShopaholicPagination}
   */
  setButtonSelector(sButtonSelector) {
    this.sButtonSelector = sButtonSelector;

    return this;
  }
}

class ShopaholicLoadMore {
  /**
   * @param {ShopaholicProductList} obProductListHelper
   */
  constructor(obProductListHelper = null) {
    this.obProductListHelper = obProductListHelper;
    this.sEventType = 'click';
    this.sFiledName = 'page';
    this.sAttributeName = 'data-page';

    this.sDefaultButtonClass = '_shopaholic-load-more';
    this.sButtonSelector = `.${this.sDefaultButtonClass}`;
  }

  /**
   * Init event handlers
   */
  init() {
    $(document).on(this.sEventType, this.sButtonSelector, (obEvent) => {
      obEvent.preventDefault();
      obEvent.stopPropagation();

      const obButton = $(obEvent.currentTarget),
        iPage = Number(obButton.attr(this.sAttributeName)) + 1;
      urlGenerationHelper.init();
      if (iPage == 1) {
        urlGenerationHelper.remove(this.sFiledName);
      } else {
        urlGenerationHelper.set(this.sFiledName, [iPage]);
      }

      urlGenerationHelper.update();
      if (!this.obProductListHelper) {
        return;
      }

      this.obProductListHelper.send();
    });
  }

  /**
   * Redeclare default selector of pagination button
   * Default value is "_shopaholic-pagination"
   *
   * @param {string} sButtonSelector
   * @returns {ShopaholicPagination}
   */
  setButtonSelector(sButtonSelector) {
    this.sButtonSelector = sButtonSelector;

    return this;
  }
}

class ShopaholicSorting {
  /**
   * @param {ShopaholicProductList} obProductListHelper
   */
  constructor(obProductListHelper = null) {
    this.obProductListHelper = obProductListHelper;
    this.sEventType = 'click';
    this.sFiledName = 'sort';

    this.sDefaultSelectClass = '_shopaholic-sorting';
    this.sSelectSelector = `.${this.sDefaultSelectClass}`;
  }

  /**
   * Init event handlers
   */
  init() {
    $(document).on(this.sEventType, this.sSelectSelector, (obEvent) => {
      obEvent.preventDefault()

      const obSelect = $(obEvent.currentTarget),
        sSorting = obSelect.attr('data-sort');

      urlGenerationHelper.init();
      urlGenerationHelper.set(this.sFiledName, [sSorting]);
      urlGenerationHelper.update();
      if (!this.obProductListHelper) {
        return;
      }

      this.obProductListHelper.send();
    });
  }
}


//********* СОРТИРОВКА ************
const obListHelper = new ShopaholicProductList();
obListHelper.setAjaxRequestCallback((obRequestData) => {
  obRequestData.update = {
    'components/catalog/productsViewGrid': `._grid_view_wrapper`,
    'components/catalog/productsViewList': `._list_view_wrapper`,
    'components/catalog/btnLoadMore': `._load_more-holder`,
    'components/pagination': `._pagination_wrapper`,
  };
  obRequestData.loading = $.oc.stripeLoadIndicator;
  return obRequestData;
});
const obSortingHelper = new ShopaholicSorting(obListHelper);
obSortingHelper.init();

//*********ЗАГРУЗИТЬ ЕЩЕ************
const obListHelperLoad = new ShopaholicProductList();
obListHelperLoad.setAjaxRequestCallback((obRequestData) => {
  obRequestData.update = {
    'components/catalog/productsViewGrid': `@._grid_view_wrapper`,
    'components/catalog/productsViewList': `@._list_view_wrapper`,
    'components/catalog/btnLoadMore': `._load_more-holder`,
    'components/pagination': `._pagination_wrapper`,
  };
  obRequestData.loading = $.oc.stripeLoadIndicator;
  return obRequestData;
});
const obLoadMoreHelper = new ShopaholicLoadMore(obListHelperLoad);
obLoadMoreHelper.init();


//********* Пагинация ************
const obPaginationHelper = new ShopaholicPagination(obListHelper);
obPaginationHelper.init();

//************* Фильтр по цене ************

obListHelper.setAjaxRequestCallback((obRequestData) => {
  obRequestData.update = {
    'components/catalog/productsViewGrid': `._grid_view_wrapper`,
    'components/catalog/productsViewList': `._list_view_wrapper`,
    'components/catalog/btnLoadMore': `._load_more-holder`,
    'components/pagination': `._pagination_wrapper`,
  };
  obRequestData.loading = $.oc.stripeLoadIndicator;
  return obRequestData;
});

const obFilterPrice = new ShopaholicFilterPrice(obListHelper);
obFilterPrice.setEventType('click').init();

$(document).on('click', '._filter_start', function (){
  $('._input_price_event').click()
  $(this).css('display', 'none')
})

$(document).on('input', '._input_price', function (){
  $('._filter_start').css('top', $('.price-range').position().top)
  $('._filter_start').css('display', 'block')
})



//************* Фильтр по Бренду ************
obListHelper.setAjaxRequestCallback((obRequestData) => {
  obRequestData.update = {
    'components/catalog/productsViewGrid': `._grid_view_wrapper`,
    'components/catalog/productsViewList': `._list_view_wrapper`,
    'components/catalog/btnLoadMore': `._load_more-holder`,
    'components/pagination': `._pagination_wrapper`,
  };
  obRequestData.loading = $.oc.stripeLoadIndicator;
  return obRequestData;
});

const obBrandFilterPanel = new ShopaholicFilterPanel(obListHelper);
obBrandFilterPanel.setFieldName('brand').setWrapperSelector(`.${'_shopaholic-brand-filter-wrapper'}`).init();

const obFilterPanel = new ShopaholicFilterPanel(obListHelper);
obFilterPanel.init();
