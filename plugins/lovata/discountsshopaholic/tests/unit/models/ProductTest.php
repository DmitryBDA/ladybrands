<?php namespace Lovata\DiscountsShopaholic\Tests\Unit\Models;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Product;
use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class ProductTest
 * @package Lovata\DiscountsShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ProductTest extends CommonTest
{
    protected $sModelClass;

    /**
     * ProductTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Product::class;
        parent::__construct();
    }

    /**
     * Check model "discount" relation config
     */
    public function testHasDiscountRelation()
    {
        /** @var Product $obModel */
        $obModel = new Product();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('discount', $obModel->belongsToMany);
        self::assertEquals(Discount::class, array_shift($obModel->belongsToMany['discount']));
    }
}