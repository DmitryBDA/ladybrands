<?php namespace Lovata\PropertiesShopaholic\Classes\Item;

use Lovata\PropertiesShopaholic\Models\Group;

use Lovata\Toolbox\Classes\Item\ElementItem;

/**
 * Class GroupItem
 * @package Lovata\PropertiesShopaholic\Classes\Item
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @see \Lovata\PropertiesShopaholic\Tests\Unit\Item\GroupItemTest
 *
 * @property        $id
 * @property string $name
 * @property string $code
 * @property string $description
 */
class GroupItem extends ElementItem
{
    const MODEL_CLASS = Group::class;

    /** @var Group */
    protected $obElement = null;
}