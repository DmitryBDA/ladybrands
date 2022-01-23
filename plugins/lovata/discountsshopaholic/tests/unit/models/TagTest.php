<?php namespace Lovata\DiscountsShopaholic\Tests\Unit\Models;

use System\Classes\PluginManager;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\DiscountsShopaholic\Models\Discount;

/**
 * Class TagTest
 * @package Lovata\DiscountsShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class TagTest extends CommonTest
{
    /**
     * Check model "discount" relation config
     */
    public function testHasDiscountRelation()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        /** @var \Lovata\TagsShopaholic\Models\Tag $obModel */
        $obModel = new \Lovata\TagsShopaholic\Models\Tag();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('discount', $obModel->belongsToMany);
        self::assertEquals(Discount::class, array_shift($obModel->belongsToMany['discount']));
    }
}