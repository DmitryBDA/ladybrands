<?php namespace Lovata\LabelsShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../toolbox/vendor/autoload.php';
include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\Shopaholic\Models\Product;
use Lovata\LabelsShopaholic\Models\Label;

/**
 * Class ProductTest
 * @package Lovata\Shopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ProductTest extends PluginTestCase
{
    protected $sModelClass;

    /**
     * LabelTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Product::class;
        parent::__construct();
    }

    /**
     * Check model "label" relation config
     */
    public function testHasLabelRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "label" relation config';

        /** @var Product $obModel */
        $obModel = new Product();
        self::assertNotEmpty($obModel->belongsToMany, $sErrorMessage);
        self::assertArrayHasKey('label', $obModel->belongsToMany, $sErrorMessage);
        self::assertEquals(Label::class, array_shift($obModel->belongsToMany['label']), $sErrorMessage);
    }
}