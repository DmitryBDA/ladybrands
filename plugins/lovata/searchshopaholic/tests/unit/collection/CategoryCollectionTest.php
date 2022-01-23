<?php namespace Lovata\SearchShopaholic\Tests\Unit\Collection;

use Lovata\Shopaholic\Models\Settings;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Classes\Item\CategoryItem;
use Lovata\Shopaholic\Classes\Collection\CategoryCollection;

/**
 * Class CategoryCollectionTest
 * @package Lovata\SearchShopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class CategoryCollectionTest extends CommonTest
{
    /** @var  Category */
    protected $obElement;

    protected $arCreateData = [
        'name'         => 'name',
        'slug'         => 'slug',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
        'search_synonym'  => 'synonym',
        'search_content'  => 'content',
    ];

    protected $arSearchFieldList = [
        'name',
        'preview_text',
        'description',
        'search_synonym',
        'search_content',
    ];

    /**
     * Check item collection
     */
    public function testCollectionItem()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        foreach ($this->arSearchFieldList as $sField) {
            $this->searchByField($sField);
        }
    }

    /**
     * Test search by field
     * @param string $sField
     */
    protected function searchByField($sField)
    {
        $sErrorMessage = 'Search method is not correct by field ' . $sField;

        Settings::set('category_search_by', [
            [
                'field' => $sField,
            ],
        ]);

        //Check item collection
        $obCollection = CategoryCollection::make()->search($this->arCreateData[$sField]);

        /** @var CategoryItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);
        self::assertEquals(1, $obCollection->count(), $sErrorMessage);
    }

    /**
     * Create category object for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;

        $this->obElement = Category::create($arCreateData);
    }
}