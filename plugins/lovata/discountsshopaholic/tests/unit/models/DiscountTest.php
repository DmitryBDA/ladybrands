<?php namespace Lovata\DiscountsShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../toolbox/vendor/autoload.php';
include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use System\Classes\PluginManager;
use Lovata\Toolbox\Traits\Tests\TestModelHasImages;
use Lovata\Toolbox\Traits\Tests\TestModelHasPreviewImage;

use Lovata\Shopaholic\Models\Brand;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Category;
use Lovata\DiscountsShopaholic\Models\Discount;
use Lovata\Toolbox\Traits\Tests\TestModelValidationNameField;
use Lovata\Toolbox\Traits\Tests\TestModelValidationSlugField;

/**
 * Class DiscountTest
 * @package Lovata\DiscountsShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class DiscountTest extends PluginTestCase
{
    use TestModelValidationNameField;
    use TestModelValidationSlugField;
    use TestModelHasPreviewImage;
    use TestModelHasImages;

    protected $sModelClass;

    /**
     * DiscountTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Discount::class;
        parent::__construct();
    }

    /**
     * Check model "product" relation config
     */
    public function testHasProductRelation()
    {
        $obModel = new Discount();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('product', $obModel->belongsToMany);
        self::assertEquals(Product::class, array_shift($obModel->belongsToMany['product']));
    }

    /**
     * Check model "offer" relation config
     */
    public function testHasOfferRelation()
    {
        $obModel = new Discount();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('offer', $obModel->belongsToMany);
        self::assertEquals(Offer::class, array_shift($obModel->belongsToMany['offer']));
    }

    /**
     * Check model "brand" relation config
     */
    public function testHasBrandRelation()
    {
        $obModel = new Discount();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('brand', $obModel->belongsToMany);
        self::assertEquals(Brand::class, array_shift($obModel->belongsToMany['brand']));
    }

    /**
     * Check model "category" relation config
     */
    public function testHasCategoryRelation()
    {
        $obModel = new Discount();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('category', $obModel->belongsToMany);
        self::assertEquals(Category::class, array_shift($obModel->belongsToMany['category']));
    }

    /**
     * Check model "tag" relation config
     */
    public function testHasTagRelation()
    {
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }

        $obModel = new Discount();

        self::assertNotEmpty($obModel->belongsToMany);
        self::assertArrayHasKey('tag', $obModel->belongsToMany);
        self::assertEquals(\Lovata\TagsShopaholic\Models\Tag::class, array_shift($obModel->belongsToMany['tag']));
    }
}