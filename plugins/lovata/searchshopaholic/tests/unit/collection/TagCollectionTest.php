<?php namespace Lovata\SearchShopaholic\Tests\Unit\Collection;

use System\Classes\PluginManager;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Settings;

/**
 * Class \Lovata\TagsShopaholic\Models\TagCollectionTest
 * @package Lovata\SearchShopaholic\Tests\Unit\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class TagCollectionTest extends CommonTest
{
    /** @var  \Lovata\TagsShopaholic\Models\Tag */
    protected $obElement;

    protected $arCreateData = [
        'name'           => 'name',
        'slug'           => 'slug',
        'preview_text'   => 'preview_text',
        'description'    => 'description',
        'search_synonym' => 'synonym',
        'search_content' => 'content',
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
        if (!PluginManager::instance()->hasPlugin('Lovata.TagsShopaholic')) {
            return;
        }
        
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

        Settings::set('tag_search_by', [
            [
                'field' => $sField,
            ],
        ]);

        //Check item collection
        $obCollection = \Lovata\TagsShopaholic\Classes\Collection\TagCollection::make()->search($this->arCreateData[$sField]);

        /** @var \Lovata\TagsShopaholic\Classes\Item\TagItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);
        self::assertEquals(1, $obCollection->count(), $sErrorMessage);
    }

    /**
     * Create product object for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;

        $this->obElement = \Lovata\TagsShopaholic\Models\Tag::create($arCreateData);
    }
}