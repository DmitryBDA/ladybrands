<?php namespace Zprimegroup\Basecode\Models;

use Lovata\Shopaholic\Components\CategoryData;
use Lovata\Shopaholic\Components\CategoryList;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Product;
use System\Models\File;
use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation;

/**
 * Class Settings
 */
class Settings extends Model
{
    use Validation;

    public $implement = ['System.Behaviors.SettingsModel', '@RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['phone_1', 'phone_2', 'address', 'admin', 'about_text'];

    public $settingsCode = 'zprimegroup';
    public $settingsFields = 'fields.yaml';

    public $jsonable = ['admin'];

    public $attachOne = [
      'about_photo' => 'System\Models\File',
      'banner_image' => 'System\Models\File',
      'banner_video' => 'System\Models\File',
      'banner_image_1' => 'System\Models\File',
      'banner_image_2' => 'System\Models\File',
      'banner_image_3' => 'System\Models\File',
      'banner' => 'System\Models\File',
      'icon' => 'System\Models\File',
      'coop_icon' => 'System\Models\File'
    ];

    public $attachMany = [
      'partners_images' => 'System\Models\File'
    ];
    public $belongsToMany = [
      'category_id_child' => [
        Category::class,
        'table' => 'lovata_shopaholic_additional_categories',
        'key' => 'category_id'
      ],
    ];

    public $rules = [];

    public function getCategoryIdOptions()
    {
      $obCategoryList = Category::active()->orderBy('name', 'asc')->lists('name', 'id');

      return $obCategoryList;
    }

    public function getCategoryIdChildOptions()
    {

      $obCategoryList = Category::active()->orderBy('name', 'asc')->lists('name', 'id');

      return $obCategoryList;
    }

    /**
     * Get setting value from cache
     * @param string $sCode
     * @return null|string
     */
    public static function getValue($sCode) {

        if(empty($sCode)) {
            return null;
        }

        //Get settings object
        $obSettings = self::where('item', 'zprimegroup')->first();
        if(empty($obSettings)) {
            return null;
        }

        $sValue = $obSettings->$sCode;
        return $sValue;
    }

    /**
     * Get Image data
     * @param $sCode
     * @return \System\Models\File|null
     */
    public static function getImageData($sCode) {

        /** @var File $obImage */
        $obImage = File::where('attachment_type', 'Zprimegroup\Basecode\Models\Settings')->where('field', $sCode)->first();
        if(empty($obImage)) {
            return null;
        }

        return $obImage;
    }
}
