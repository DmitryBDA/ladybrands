<?php namespace Lovata\SearchShopaholic\Classes\Helper;

use DB;
use October\Rain\Support\Collection;
use Lovata\Toolbox\Traits\Helpers\TraitInitActiveLang;

/**
 * Class SearchHelper
 * @package Lovata\SearchShopaholic\Classes\Helper
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SearchHelper
{
    use TraitInitActiveLang;

    const MIN_LENGTH = 3;
    const DEFAULT_WEIGHT = 100;
    const DEFAULT_WORD_WEIGHT = 1;

    const TYPE_DEFAULT = 'default';
    const TYPE_FULL = 'full';
    const TYPE_ALL_WORDS = 'all_words';

    /** @var array array */
    protected $arResult = [];

    /** @var array */
    protected $arResultIDList = [];

    /** @var string */
    protected $sModel;

    /** @var string */
    protected $sSearch;

    /** @var array */
    protected $arWordList = [];

    /** @var array */
    protected $arSettings;

    /** @var Collection */
    protected $obFieldSearch;

    /** @var array */
    protected static $arTranslateField = [];

    /**
     * SearchHelper constructor.
     * @param string $sModel
     */
    public function __construct($sModel)
    {
        $this->sModel = $sModel;
        $this->initActiveLang();

        $obModel = new $sModel();
        self::$arTranslateField = $obModel->translatable;
    }

    /**
     * Get ID's array with search result
     * @param string $sSearch
     * @param array  $arSettings
     *
     * @return null|array
     */
    public function result($sSearch, $arSettings)
    {
        $this->sSearch = trim($sSearch);
        $this->arSettings = $arSettings;

        if (!$this->validate()) {
            return null;
        }

        //Get word list from search string
        $this->arWordList = explode(' ', $this->sSearch);
        $this->arWordList = array_filter($this->arWordList);
        $this->arWordList = array_values($this->arWordList);

        $this->run();

        return $this->arResultIDList;
    }

    /**
     * Search elements
     */
    protected function run()
    {
        //Process settings and search element by fields
        foreach ($this->arSettings as $arSearchData) {
            if (empty($arSearchData) || !is_array($arSearchData)) {
                continue;
            }

            $this->obFieldSearch = Collection::make($arSearchData);
            $this->searchByField();
        }

        if (empty($this->arResult)) {
            return;
        }

        arsort($this->arResult);

        $this->arResultIDList = array_keys($this->arResult);
    }

    /**
     * Search element by field
     */
    protected function searchByField()
    {
        //Get field name
        $sFieldName = $this->obFieldSearch->get('field');
        if (empty($sFieldName)) {
            return;
        }

        //Get min string length
        $iMinLength = (int) $this->obFieldSearch->get('min', self::MIN_LENGTH);
        if (empty($iMinLength) || $iMinLength < 0) {
            $iMinLength = self::MIN_LENGTH;
        }

        //Get weight
        $iWeight = (int) $this->obFieldSearch->get('weight', self::DEFAULT_WEIGHT);
        if (empty($iWeight) || $iWeight < 0) {
            $iWeight = self::DEFAULT_WEIGHT;
        }

        $this->search($this->sSearch, $sFieldName, $iMinLength, $iWeight);

        $sSearchType = $this->obFieldSearch->get('type');

        //Search by words
        if ($sSearchType == self::TYPE_FULL || count($this->arWordList) < 2) {
            return;
        }

        //Get word weight
        $iWordWeight = (int) $this->obFieldSearch->get('word_weight', self::DEFAULT_WORD_WEIGHT);
        if (empty($iWordWeight) || $iWordWeight < 0) {
            $iWordWeight = self::DEFAULT_WORD_WEIGHT;
        }

        $this->searchByWords($sFieldName, $iMinLength, $iWordWeight, $sSearchType);
    }

    /**
     * Send search query and proves search results
     * @param string $sSearch
     * @param string $sFieldName
     * @param int    $iMinLength
     * @param int    $iWeight
     */
    protected function search($sSearch, $sFieldName, $iMinLength, $iWeight)
    {
        if (empty($sFieldName) || empty($sSearch) || mb_strlen($sSearch) < $iMinLength) {
            return;
        }

        if (!empty(self::$sActiveLang) && in_array($sFieldName, self::$arTranslateField)) {
            $arElementIDList = DB::table('rainlab_translate_attributes')
                ->where('locale', self::$sActiveLang)
                ->where('model_type', $this->sModel)
                ->whereRaw("JSON_EXTRACT(`attribute_data`, '$.{$sFieldName}') LIKE '%".$sSearch."%'")
                ->lists('model_id');
        } else {
            $sModelName = $this->sModel;
            $arElementIDList = $sModelName::where($sFieldName, 'like', '%'.$sSearch.'%')->lists('id');
        }
        if (empty($arElementIDList)) {
            return;
        }

        $this->addSearchResults($arElementIDList, $iWeight);
    }

    /**
     * Add element ID list to search result
     * @param array $arElementIDList
     * @param int   $iWeight
     */
    protected function addSearchResults($arElementIDList, $iWeight)
    {
        if (empty($arElementIDList)) {
            return;
        }

        foreach ($arElementIDList as $iElementID) {
            if (!isset($this->arResult[$iElementID])) {
                $this->arResult[$iElementID] = 0;
            }

            $this->arResult[$iElementID] += $iWeight;
        }
    }

    /**
     * Send search query and proves search results
     * @param string $sFieldName
     * @param int    $iMinLength
     * @param int    $iWeight
     * @param string $sSearchType
     */
    protected function searchByWords($sFieldName, $iMinLength, $iWeight, $sSearchType)
    {
        $arWordList = $this->arWordList;
        foreach ($arWordList as $iKey => $sWord) {
            if (mb_strlen($sWord) < $iMinLength) {
                unset($arWordList[$sWord]);
            }
        }

        if (empty($sFieldName) || empty($arWordList)) {
            return;
        }

        $arResult = [];
        /** @var \October\Rain\Database\Builder $obQuery */
        $obElementList = null;
        if (!empty(self::$sActiveLang) && in_array($sFieldName, self::$arTranslateField) && $sSearchType == SearchHelper::TYPE_DEFAULT) {
            $obQuery = DB::table('rainlab_translate_attributes')
                ->selectRaw("model_id, JSON_EXTRACT(`attribute_data`, '$.{$sFieldName}')")
                ->where('locale', self::$sActiveLang)
                ->where('model_type', $this->sModel);

            if ($sSearchType == SearchHelper::TYPE_ALL_WORDS) {
                foreach ($arWordList as $sWord) {
                    $obQuery->whereRaw("JSON_EXTRACT(`attribute_data`, '$.{$sFieldName}') LIKE '%".$sWord."%'");
                }
            } else {
                $obQuery->where(function ($obQuery) use ($sSearchType, $arWordList, $sFieldName) {
                    /** @var \October\Rain\Database\Builder $obQuery */
                    foreach ($arWordList as $sWord) {
                        $obQuery->orWhereRaw("JSON_EXTRACT(`attribute_data`, '$.{$sFieldName}') LIKE '%".$sWord."%'");
                    }
                });
            }

            $obElementList = $obQuery->get();
            foreach ($obElementList as $obElement) {
                $arElementData = (array) $obElement;
                $arResult[] = [
                    'id'    => array_shift($arElementData),
                    'field' => array_shift($arElementData),
                ];
            }
        } else {
            if ($sSearchType == SearchHelper::TYPE_DEFAULT) {
                $sModelName = $this->sModel;
                $obElementList = $sModelName::select(['id', $sFieldName])->where(function ($obQuery) use ($sSearchType, $arWordList, $sFieldName) {
                    /** @var \October\Rain\Database\Builder $obQuery */
                    foreach ($arWordList as $sWord) {
                        $obQuery->orWhere($sFieldName, 'like', '%'.$sWord.'%');
                    }
                })->get();
            } else {
                $sModelName = $this->sModel;
                $obQuery = $sModelName::select(['id', $sFieldName]);
                foreach ($arWordList as $sWord) {
                    $obQuery->where($sFieldName, 'like', '%'.$sWord.'%');
                }

                $obElementList = $obQuery->get();
            }

            foreach ($obElementList as $obElement) {
                $arResult[] = [
                    'id'    => $obElement->id,
                    'field' => $obElement->$sFieldName,
                ];
            }
        }

        /** @var Collection $obElementList */
        if (empty($arResult)) {
            return;
        }

        $this->addSearchResultByWord($arResult, $iWeight, $iMinLength);
    }

    /**
     * Add element ID list to search result
     * @param array $arSearchResult
     * @param int   $iWeight
     * @param int   $iMinLength
     */
    protected function addSearchResultByWord($arSearchResult, $iWeight, $iMinLength)
    {
        if (empty($arSearchResult)) {
            return;
        }

        $iMaxLength = 1;
        foreach ($arSearchResult as $iKey => $arElementData) {
            $sValue = $arElementData['field'];

            $arValueWordList = explode(' ', $sValue);
            $arValueWordList = array_filter($arValueWordList);
            $arValueWordList = array_values($arValueWordList);

            $arSearchResult[$iKey]['field'] = $arValueWordList;
            if (count($arValueWordList) > $iMaxLength) {
                $iMaxLength = count($arValueWordList);
            }
        }

        foreach ($arSearchResult as $arElementData) {
            $iElementID = $arElementData['id'];
            $arValueWordList = $arElementData['field'];

            $iElementWeight = $iWeight * $this->calculateWeight($arValueWordList, $iMaxLength, $iMinLength);
            if (!isset($this->arResult[$iElementID])) {
                $this->arResult[$iElementID] = 0;
            }

            $this->arResult[$iElementID] += $iElementWeight;
        }
    }

    /**
     * Calculate weight by words
     * @param array $arValueWordList
     * @param int   $iMaxLength
     * @param int   $iMinLength
     * @return int
     */
    protected function calculateWeight($arValueWordList, $iMaxLength, $iMinLength)
    {
        $iWeight = 0;
        $iPrevWordWeight = 0;
        $arMatchedWordList = [];
        foreach ($arValueWordList as $iKey => $sWord) {
            $iWordWeight = 0;
            foreach ($this->arWordList as $iSearchKey => $sSearchWord) {
                if (!preg_match("%{$sSearchWord}%i", $sWord) || mb_strlen($sSearchWord) < $iMinLength) {
                    continue;
                }

                $iWordWeight = $iMaxLength - $iKey;
                $iWordWeight = $iWordWeight * (count($this->arWordList) - $iSearchKey);
                if (!in_array($iSearchKey, $arMatchedWordList)) {
                    $arMatchedWordList[] = $iSearchKey;
                    $iWordWeight = $iWordWeight * count($this->arWordList);
                }

                if ($iPrevWordWeight > 0) {
                    $iWordWeight = $iWordWeight * $iPrevWordWeight;
                }

                break;
            }

            $iWeight += $iWordWeight;
            $iPrevWordWeight = $iWordWeight;
        }

        return $iWeight;
    }

    /**
     * Validate search model, search string, search settings
     * @return bool
     */
    protected function validate()
    {
        //Check model class
        if (empty($this->sModel) || !class_exists($this->sModel)) {
            return false;
        }

        //Check search string and search settings
        if (empty($this->sSearch) || empty($this->arSettings) || !is_array($this->arSettings)) {
            return false;
        }

        return true;
    }
}
