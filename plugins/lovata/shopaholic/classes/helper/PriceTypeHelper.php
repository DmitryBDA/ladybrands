<?php namespace Lovata\Shopaholic\Classes\Helper;

use October\Rain\Support\Traits\Singleton;

use Lovata\Shopaholic\Models\PriceType;

/**
 * Class PriceTypeHelper
 * @package Lovata\Shopaholic\Classes\Helper
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PriceTypeHelper
{
    use Singleton;

    /** @var PriceType */
    protected $obActivePriceType;

    /** @var \October\Rain\Database\Collection|PriceType[] */
    protected $obPriceTypeList;

    /** @var string */
    protected $sActivePriceTypeCode;

    /**
     * Get value of active price type
     * @param string $sPriceTypeCode
     * @return PriceType
     */
    public function findByCode($sPriceTypeCode)
    {
        $obPriceType = $this->obPriceTypeList->where('code', $sPriceTypeCode)->first();

        return $obPriceType;
    }

    /**
     * Get value of active price type
     * @return PriceType
     */
    public function getActive()
    {
        return $this->obActivePriceType;
    }

    /**
     * Get active price type code
     * @return null|string
     */
    public function getActivePriceTypeCode()
    {
        $obPriceType = $this->getActive();
        if (empty($obPriceType)) {
            return null;
        }

        return $obPriceType->code;
    }

    /**
     * Get active price type code
     * @return null|string
     */
    public function getActivePriceTypeID()
    {
        $obPriceType = $this->getActive();
        if (empty($obPriceType)) {
            return null;
        }

        return $obPriceType->id;
    }

    /**
     * Clear active currency value
     * @param string $sPriceTypeCode
     */
    public function switchActive($sPriceTypeCode)
    {
        $this->sActivePriceTypeCode = $sPriceTypeCode;

        $this->initActivePriceType();
    }

    /**
     * Init price type data
     */
    protected function init()
    {
        $this->obPriceTypeList = PriceType::active()->get();

        $this->initActivePriceType();
    }

    /**
     * Get active price type code and find active price type object by code
     */
    protected function initActivePriceType()
    {
        $this->obActivePriceType = null;
        if (!empty($this->sActivePriceTypeCode)) {
            $this->obActivePriceType = $this->obPriceTypeList->where('code', $this->sActivePriceTypeCode)->first();
        }
    }
}
