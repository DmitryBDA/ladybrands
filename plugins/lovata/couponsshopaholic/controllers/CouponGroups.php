<?php namespace Lovata\CouponsShopaholic\Controllers;

use Lang;
use Flash;
use Backend;
use Input;
use Redirect;
use BackendMenu;
use ApplicationException;
use Backend\Classes\Controller;

use Lovata\CouponsShopaholic\Classes\Helper\GenerateCoupon;

/**
 * Class CouponGroups
 * @package Lovata\CouponGroupsShopaholic\Controllers
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CouponGroups extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.RelationController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    protected $obGenerateFormWidget;

    /**
     * CouponGroups constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.Shopaholic', 'shopaholic-menu-promo', 'shopaholic-menu-promo-coupon-group');
    }

    /**
     * Generate coupons form
     * @param int         $iRecordID
     * @param null|string $sContext
     * @throws \SystemException
     */
    public function generate($iRecordID, $sContext = null)
    {
        $obModel = $this->formFindModelObject($iRecordID);

        $config = $this->makeConfig('$/lovata/couponsshopaholic/models/coupon/generate.yaml');

        $this->pageTitle = Lang::get('lovata.couponsshopaholic::lang.message.generate_coupon');

        $config->model = $obModel;
        $config->arrayName = class_basename($obModel);
        $config->context = $sContext;

        $this->obGenerateFormWidget = $this->makeWidget('Backend\Widgets\Form', $config);
    }

    /**
     * Render generate coupons form
     * @param array $arOptionList
     * @return mixed
     * @throws ApplicationException
     */
    public function formGenerateRender($arOptionList = [])
    {
        if (!$this->obGenerateFormWidget) {
            throw new ApplicationException(Lang::get('backend::lang.form.behavior_not_ready'));
        }

        return $this->obGenerateFormWidget->render($arOptionList);
    }

    /**
     * Generate new coupons for group
     * @param int $iRecordID
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function onGenerate($iRecordID)
    {
        /** @var \Lovata\CouponsShopaholic\Models\CouponGroup $obModel */
        $obModel = $this->formFindModelObject($iRecordID);
        if (empty($obModel)) {
            $sURL = Backend::url('lovata/couponsshopaholic/coupongroups');
            return Redirect::to($sURL);
        }

        $iCount = Input::get('CouponGroup.count');
        $sUserList = Input::get('CouponGroup.user_list');
        $arUserList = explode(',', $sUserList);
        $iLength = Input::get('CouponGroup.length');
        $bHidden = Input::get('CouponGroup.hidden');
        $bLowercase = Input::get('CouponGroup.use_lowercase');
        $bUppercase = Input::get('CouponGroup.use_uppercase');
        $bNumber = Input::get('CouponGroup.use_number');

        $iPartCount = Input::get('CouponGroup.part_count');
        $sPartSeparator = Input::get('CouponGroup.part_separator');

        $obGenerateCoupon = new GenerateCoupon($obModel->id, $iCount, $arUserList);
        $obGenerateCoupon->init($iLength, $bHidden, $bLowercase, $bUppercase, $bNumber, $iPartCount, $sPartSeparator);

        $iCouponCount = $obGenerateCoupon->run();

        $sMessage = Lang::get('lovata.couponsshopaholic::lang.message.generate_coupon_success', ['count' => $iCouponCount, 'max' => $iCount]);
        Flash::success($sMessage);

        $sURL = Backend::url('lovata/couponsshopaholic/coupongroups/update/'.$obModel->id);
        return Redirect::to($sURL);
    }
}
