<?php namespace Lovata\WishListShopaholic\Tests\Unit\Collection;

use System\Classes\PluginManager;
use Lovata\Toolbox\Tests\CommonTest;
use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Collection\ProductCollection;
use Lovata\WishListShopaholic\Classes\Helper\WishListHelper;

/**
 * Class ProductCollectionTest
 * @package Lovata\WishListShopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ProductCollectionTest extends CommonTest
{
    /** @var  Product */
    protected $obElement;

    /** @var  \Lovata\Buddies\Models\User */
    protected $obUser;

    protected $arCreateData = [
        'active' => true,
        'name'   => 'name',
        'slug'   => 'slug',
    ];

    protected $arUserData = [
        'email'                 => 'email@email.com',
        'password'              => 'test',
        'password_confirmation' => 'test',
    ];

    /**
     * Check "add" wishList method
     */
    public function testAddToWishList()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        if (!empty($this->obUser)) {
            \Lovata\Buddies\Facades\AuthHelper::logout();
        }

        $sErrorMessage = 'Add to wish list method is not correct';

        //Check collection
        $obProductList = ProductCollection::make()->wishList();

        self::assertInstanceOf(ProductCollection::class, $obProductList);
        self::assertEquals(true, $obProductList->isEmpty(), $sErrorMessage);

        //Add product to wish list
        /** @var WishListHelper $obWishListHelper */
        $obWishListHelper = app(WishListHelper::class);
        $obWishListHelper->add($this->obElement->id - 1);

        //Check collection
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id - 1], $obProductList->getIDList(), $sErrorMessage);

        //Check uniques
        $obWishListHelper->add($this->obElement->id - 1);
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id - 1], $obProductList->getIDList(), $sErrorMessage);

        if (empty($this->obUser)) {
            return;
        }

        $this->checkAddToWishListWithLogin();
    }

    /**
     * Check "add" wishList method (with login)
     */
    public function checkAddToWishListWithLogin()
    {
        $sErrorMessage = 'Add to wish list method is not correct';

        \Lovata\Buddies\Facades\AuthHelper::login($this->obUser);

        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id - 1], $obProductList->getIDList(), $sErrorMessage);

        \Lovata\Buddies\Facades\AuthHelper::logout();

        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals(true, $obProductList->isEmpty(), $sErrorMessage);

        //Add product to wish list
        /** @var WishListHelper $obWishListHelper */
        $obWishListHelper = app(WishListHelper::class);
        $obWishListHelper->add($this->obElement->id);

        //Check collection
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id], $obProductList->getIDList(), $sErrorMessage);

        \Lovata\Buddies\Facades\AuthHelper::login($this->obUser);

        //Check collection
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id, $this->obElement->id - 1], $obProductList->getIDList(), $sErrorMessage);
    }

    /**
     * Check "remove" wishList method
     */
    public function testRemoveFromWishList()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        if (!empty($this->obUser)) {
            \Lovata\Buddies\Facades\AuthHelper::logout();
        }

        PluginManager::instance()->refreshPlugin('Lovata.WishListShopaholic');

        /** @var WishListHelper $obWishListHelper */
        $obWishListHelper = app(WishListHelper::class);
        $obWishListHelper->clear();

        $sErrorMessage = 'Remove from wish list method is not correct';

        //Check collection
        $obProductList = ProductCollection::make()->wishList();

        self::assertInstanceOf(ProductCollection::class, $obProductList);
        self::assertEquals(true, $obProductList->isEmpty(), $sErrorMessage);

        //Add product to wish list
        $obWishListHelper->add($this->obElement->id - 1);
        $obWishListHelper->add($this->obElement->id);

        //Check collection
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id, $this->obElement->id - 1], $obProductList->getIDList(), $sErrorMessage);

        $obWishListHelper->remove($this->obElement->id - 1);
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id], $obProductList->getIDList(), $sErrorMessage);

        if (empty($this->obUser)) {
            return;
        }

        $this->checkRemoveFromWishListWithLogin();
    }

    /**
     * Check "remove" wishList method (with login)
     */
    public function checkRemoveFromWishListWithLogin()
    {
        $sErrorMessage = 'Add to wish list method is not correct';

        \Lovata\Buddies\Facades\AuthHelper::login($this->obUser);

        //Add product to wish list
        /** @var WishListHelper $obWishListHelper */
        $obWishListHelper = app(WishListHelper::class);
        $obWishListHelper->add($this->obElement->id - 1);

        //Check collection
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id - 1, $this->obElement->id], $obProductList->getIDList(), $sErrorMessage);

        $obWishListHelper->remove($this->obElement->id);

        //Check collection
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id - 1], $obProductList->getIDList(), $sErrorMessage);
    }

    /**
     * Check "clear" wishList method
     */
    public function testClearWishListList()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        if (!empty($this->obUser)) {
            \Lovata\Buddies\Facades\AuthHelper::logout();
        }

        PluginManager::instance()->refreshPlugin('Lovata.WishListShopaholic');

        /** @var WishListHelper $obWishListHelper */
        $obWishListHelper = app(WishListHelper::class);
        $obWishListHelper->clear();

        $sErrorMessage = 'Remove from wish list method is not correct';

        //Check collection
        $obProductList = ProductCollection::make()->wishList();

        self::assertInstanceOf(ProductCollection::class, $obProductList);
        self::assertEquals(true, $obProductList->isEmpty(), $sErrorMessage);

        //Add product to wish list
        $obWishListHelper->add($this->obElement->id - 1);
        $obWishListHelper->add($this->obElement->id);

        //Check collection
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id, $this->obElement->id - 1], $obProductList->getIDList(), $sErrorMessage);

        $obWishListHelper->clear();
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals(true, $obProductList->isEmpty(), $sErrorMessage);

        if (empty($this->obUser)) {
            return;
        }

        $this->checkClearWishListWithLogin();
    }

    /**
     * Check "clear" wishList method (with login)
     */
    public function checkClearWishListWithLogin()
    {
        $sErrorMessage = 'Add to wish list method is not correct';

        \Lovata\Buddies\Facades\AuthHelper::login($this->obUser);

        //Add product to wish list
        /** @var WishListHelper $obWishListHelper */
        $obWishListHelper = app(WishListHelper::class);
        $obWishListHelper->add($this->obElement->id - 1);
        $obWishListHelper->add($this->obElement->id);

        //Check collection
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals([$this->obElement->id, $this->obElement->id - 1], $obProductList->getIDList(), $sErrorMessage);

        $obWishListHelper->clear();

        //Check collection
        $obProductList = ProductCollection::make()->wishList();
        self::assertEquals(true, $obProductList->isEmpty(), $sErrorMessage);
    }

    /**
     * Create test data
     */
    protected function createTestData()
    {
        //Create product data
        $arCreateData = $this->arCreateData;
        Product::create($arCreateData);

        $arCreateData['slug'] = $arCreateData['slug'].'1';
        $this->obElement = Product::create($arCreateData);

        $sUserPluginName = UserHelper::instance()->getPluginName();
        if ($sUserPluginName != 'Lovata.Buddies') {
            return;
        }

        $this->obUser = \Lovata\Buddies\Models\User::create($this->arUserData);
        $this->obUser = \Lovata\Buddies\Models\User::find($this->obUser->id);
    }
}
