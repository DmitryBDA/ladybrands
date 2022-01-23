<?php namespace Zprimegroup\Basecode\Components;

use Cms\Classes\ComponentBase;
use Zprimegroup\Basecode\Models\Settings;

/**
 * Class SiteSettings
 * @package Zprimegroup\Basecode\Components
 */
class SiteSettings extends ComponentBase
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'zprimegroup.basecode::lang.component.site_settings_title',
            'description' => 'zprimegroup.basecode::lang.component.site_settings_description'
        ];
    }

    /**
     * Get value by field code
     * @param $sCode
     * @return string|array
     */
    public function get($sCode)
    {
        return Settings::getValue($sCode);
    }

    /**
     * Get phone number by field code
     * @param $sCode
     * @return mixed
     */
    public function getPhone($sCode)
    {
        $sValue = $this->get($sCode);
        return $this->getPhoneValue($sValue);
    }

    /**
     * @param string $sValue
     * @return null|string|string[]
     */
    public function getPhoneValue($sValue)
    {
        return preg_replace('%\D\+%', '', $sValue);
    }

    /**
     * Получить данные изображения
     * @param $sCode
     * @return \System\Models\File|null
     */
    public function getImage($sCode)
    {
        return Settings::getImageData($sCode);
    }
}
