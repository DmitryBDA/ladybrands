<?php namespace Lovata\CouponsShopaholic\Classes\Helper;

use Lovata\CouponsShopaholic\Models\Coupon;
use Lovata\Toolbox\Classes\Helper\UserHelper;

/**
 * Class GenerateCoupon
 * @package Lovata\CouponsShopaholic\Classes\Helper
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class GenerateCoupon
{
    protected $iGroupID;
    protected $iCount;
    protected $arUserList = [];
    protected $bHidden = false;
    protected $bLowercase = true;
    protected $bUppercase = true;
    protected $bNumber = true;
    protected $iLength = 8;
    protected $iPartCount = 1;
    protected $sPartSeparator = '-';
    protected $arCharList = [];

    /**
     * GenerateCoupon constructor.
     * @param int   $iGroupID
     * @param int   $iCount
     * @param array $arUserList
     */
    public function __construct($iGroupID, $iCount, $arUserList = [])
    {
        $this->iGroupID = (int) $iGroupID;
        $this->iCount = (int) $iCount;
        $this->arUserList = (array) $arUserList;

        if ($this->iCount < 1) {
            $this->iCount = 1;
        }
    }

    /**
     * Init generation params
     * @param int    $iLength
     * @param bool   $bHidden
     * @param bool   $bLowercase
     * @param bool   $bUppercase
     * @param bool   $bNumber
     * @param int    $iPartCount
     * @param string $sPartSeparator
     */
    public function init($iLength, $bHidden = false, $bLowercase = true, $bUppercase = true, $bNumber = true, $iPartCount = 1, $sPartSeparator = '-')
    {
        $this->iLength = (int) $iLength;
        if ($this->iLength < 1) {
            $this->iLength = 8;
        }

        $this->bHidden = (bool) $bHidden;
        $this->bLowercase = (bool) $bLowercase;
        $this->bUppercase = (bool) $bUppercase;
        $this->bNumber = (bool) $bNumber;

        if (!$this->bLowercase && !$this->bUppercase && !$this->bNumber) {
            $this->bLowercase = true;
            $this->bUppercase = true;
            $this->bNumber = true;
        }

        if ($this->bNumber) {
            $this->arCharList[] = [48, 57];
        }

        if ($this->bLowercase) {
            $this->arCharList[] = [97, 122];
        }

        if ($this->bUppercase) {
            $this->arCharList[] = [65, 90];
        }

        $this->iPartCount = (int) $iPartCount;
        if ($this->iPartCount < 1) {
            $this->iPartCount = 1;
        }

        if (!empty($sPartSeparator)) {
            $this->sPartSeparator = $sPartSeparator;
        }
    }

    /**
     * Generate coupons
     * @return int
     * @throws \Exception
     */
    public function run()
    {
        if (empty($this->iGroupID)) {
            return 0;
        }

        $iResultCount = 0;
        for ($i = 0; $i < $this->iCount; $i++) {
            for ($j = 0; $j < 1000; $j++) {
                $sCode = $this->generateCode();
                $obCoupon = $this->createCoupon($sCode);
                if (empty($obCoupon)) {
                    continue;
                }

                $iResultCount++;
                break;
            }
        }

        return $iResultCount;
    }

    /**
     * Generate unique code
     * @return string
     * @throws \Exception
     */
    protected function generateCode()
    {
        $sResult = '';

        for ($j = 0; $j < $this->iPartCount; $j++) {
            for ($i = 0; $i < $this->iLength; $i++) {
                $sResult .= $this->generateChar();
            }

            if ($j < $this->iPartCount - 1) {
                $sResult .= $this->sPartSeparator;
            }
        }

        return $sResult;
    }

    /**
     * Generate char
     * @return string
     * @throws \Exception
     */
    protected function generateChar()
    {
        if (count($this->arCharList) == 1) {
            $arRandomList = $this->arCharList[0];
        } else {
            $iRandom = random_int(0, count($this->arCharList) - 1);
            $arRandomList = $this->arCharList[$iRandom];
        }

        $iCharCode = random_int($arRandomList[0], $arRandomList[1]);

        return chr($iCharCode);
    }

    /**
     * Create new coupon
     * @param string $sCode
     * @return Coupon
     */
    protected function createCoupon($sCode)
    {
        try {
            $obCoupon = Coupon::create([
                'group_id' => $this->iGroupID,
                'code'     => $sCode,
                'hidden'   => $this->bHidden,
                'user_id'  => $this->getUserID(),
            ]);
        } catch (\Exception $obException) {
            return null;
        }

        return $obCoupon;
    }

    /**
     * Find User by ID or email
     * @return null|int
     */
    protected function getUserID()
    {
        if (empty($this->arUserList)) {
            return null;
        }

        $sUserModel = UserHelper::instance()->getUserModel();
        if (empty($sUserModel)) {
            return null;
        }

        $sValue = array_shift($this->arUserList);
        $sValue = trim($sValue);
        if (empty($sValue)) {
            return null;
        }

        //Find user by email
        $obUser = UserHelper::instance()->findUserByEmail($sValue);
        if (!empty($obUser)) {
            return $obUser->id;
        }

        $iUserID = (int) $sValue;
        if ((string) $iUserID !== $sValue) {
            return null;
        }

        //Find user by ID
        /** @var \Lovata\Buddies\Models\User $obUser */
        $obUser = $sUserModel::find($iUserID);
        if (!empty($obUser)) {
            return $obUser->id;
        }

        return null;
    }
}