<?php namespace Lovata\DiscountsShopaholic\Tests\Unit\Models;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class OfferTest
 * @package Lovata\DiscountsShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class OfferTest extends CommonTest
{
    protected $sModelClass;

    /**
     * OfferTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Offer::class;
        parent::__construct();
    }

    /**
     * Check model "discount" relation config
     */
    public function testHasDiscountRelation()
    {
        /** @var Offer $obModel */
        $obModel = new Offer();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('discount', $obModel->belongsToMany);
        self::assertEquals(Discount::class, array_shift($obModel->belongsToMany['discount']));
    }
}