<?php namespace Lovata\DiscountsShopaholic\Classes\Console;

use Illuminate\Console\Command;

use Lovata\DiscountsShopaholic\Classes\Processor\CatalogPriceProcessor;

/**
 * Class UpdateCatalogPrice
 * @package Lovata\DiscountsShopaholic\Classes\Console
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class UpdateCatalogPrice extends Command
{
    /**
     * @var string command name.
     */
    protected $name = 'shopaholic:discount.update_catalog_price';

    /**
     * @var string The console command description.
     */
    protected $description = 'Update catalog prices, apply active discounts, restore old prices';

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle()
    {
        CatalogPriceProcessor::instance()->run();
    }
}