<?php namespace Lovata\DiscountsShopaholic\Classes\Processor;

use Queue;
use October\Rain\Support\Traits\Singleton;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Settings;
use Lovata\DiscountsShopaholic\Classes\Queue\RunProductPriceProcessor;

/**
 * Class CatalogPriceProcessor
 * @package Lovata\DiscountsShopaholic\Classes\Processor
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CatalogPriceProcessor
{
    use Singleton;

    const SETTING_QUEUE_ON = 'discount_update_price_queue_on';
    const SETTING_QUEUE_NAME = 'discount_update_price_queue_name';

    protected $bQueueOn = false;
    protected $sQueueName;

    /**
     * Update prices for all products in catalog
     * @throws \Throwable
     */
    public function run()
    {
        //Get all products
        $obProductList = Product::all();
        if ($obProductList->isEmpty()) {
            return;
        }

        /** @var Product $obProduct */
        foreach ($obProductList as $obProduct) {
            $this->processProductObject($obProduct);
        }
    }

    /**
     * Process product object and update offer prices
     * @param Product $obProduct
     * @throws \Throwable
     */
    public function processProductObject($obProduct)
    {
        if ($this->bQueueOn && empty($this->sQueueName)) {
            Queue::push(RunProductPriceProcessor::class, $obProduct->id);
        } elseif ($this->bQueueOn && !empty($this->sQueueName)) {
            Queue::pushOn($this->sQueueName, RunProductPriceProcessor::class, $obProduct->id);
        } else {
            /** @var ProductPriceProcessor $obProductPriceProcessor */
            $obProductPriceProcessor = app(ProductPriceProcessor::class, [$obProduct]);
            $obProductPriceProcessor->run();
        }
    }

    /**
     * @return bool
     */
    public function isQueueOn()
    {
        return $this->bQueueOn;
    }

    /**
     * Init processor settings
     */
    protected function init()
    {
        $this->bQueueOn = Settings::getValue(self::SETTING_QUEUE_ON);
        $this->sQueueName = Settings::getValue(self::SETTING_QUEUE_NAME);
    }
}