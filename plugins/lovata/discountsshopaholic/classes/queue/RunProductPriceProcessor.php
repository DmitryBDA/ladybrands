<?php namespace Lovata\DiscountsShopaholic\Classes\Queue;

use Lovata\Shopaholic\Models\Product;
use Lovata\DiscountsShopaholic\Classes\Processor\ProductPriceProcessor;

/**
 * Class RunProductPriceProcessor
 * @package Lovata\DiscountsShopaholic\Classes\Queue
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class RunProductPriceProcessor
{
    /**
     * Execute the command.
     * @param \Illuminate\Queue\Jobs\Job $obJob
     * @param array $iProductID
     */
    public function fire($obJob, $iProductID)
    {
        //Get product object
        $obProduct = Product::find($iProductID);
        if (!empty($obProduct)) {
            /** @var ProductPriceProcessor $obProductPriceProcessor */
            $obProductPriceProcessor = app(ProductPriceProcessor::class, [$obProduct]);
            $obProductPriceProcessor->run();
        }

        $obJob->delete();
    }
}