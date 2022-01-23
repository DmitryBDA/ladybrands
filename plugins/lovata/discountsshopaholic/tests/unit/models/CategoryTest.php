<?php namespace Lovata\DiscountsShopaholic\Tests\Unit\Models;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Category;
use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class CategoryTest
 * @package Lovata\DiscountsShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class CategoryTest extends CommonTest
{
    protected $sModelClass;

    /**
     * CategoryTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Category::class;
        parent::__construct();
    }

    /**
     * Check model "discount" relation config
     */
    public function testHasDiscountRelation()
    {
        /** @var Category $obModel */
        $obModel = new Category();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('discount', $obModel->belongsToMany);
        self::assertEquals(Discount::class, array_shift($obModel->belongsToMany['discount']));
    }
}