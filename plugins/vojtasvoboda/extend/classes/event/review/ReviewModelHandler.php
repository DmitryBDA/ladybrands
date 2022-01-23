<?php namespace VojtaSvoboda\Extend\Classes\Event\Review;


use VojtaSvoboda\Reviews\Models\Review;

class ReviewModelHandler
{

  public function subscribe()
  {
    Review::extend(function ($obReview){
      /** @var Review $obReview */
      $obReview->addFillable(['product_id']);
    });
  }
}
