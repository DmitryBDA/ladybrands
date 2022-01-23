<?php namespace Lovata\DiscountsShopaholic\Tests\Unit\Models;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Brand;
use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class BrandTest
 * @package Lovata\DiscountsShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class BrandTest extends CommonTest
{
    protected $sModelClass;

    /**
     * BrandTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Brand::class;
        parent::__construct();
    }

    /**
     * Check model "discount" relation config
     */
    public function testHasDiscountRelation()
    {
        /** @var Brand $obModel */
        $obModel = new Brand();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('discount', $obModel->belongsToMany);
        self::assertEquals(Discount::class, array_shift($obModel->belongsToMany['discount']));
    }
}