<?php namespace VojtaSvoboda\Extend\Classes\Event\Review;

use VojtaSvoboda\Reviews\Components\Reviews;
use VojtaSvoboda\Reviews\Models\Review;

class ReviewComponentsHandler
{

  public function subscribe()
  {
    Reviews::extend(function ($obHandler){
      /** @var Reviews $obHandler */
      $obHandler->addDynamicMethod('getAllReviewsByProductId', function ($productId){
        $reviews =  Review::where([
          ['product_id', '=', $productId],
          ['approved', '=', 1],
        ])->orderBy('created_at', 'desc')->get();
        return $reviews;
      });
    });

    Reviews::extend(function ($obHandler){
      /** @var Reviews $obHandler */
      $obHandler->addDynamicMethod('onAddReview', function (){
        $data = [];
        foreach ($_POST as $item)
        {
          $data[$item['name']] = $item['value'];
        }

        $Review = Review::create([
          'product_id' => $data['productId'],
          'rating' => $data['rating'],
          'content' => $data['textReview'],
          'approved' => 1,
        ]);

        return response()->json($Review->id);
      });
    });
  }
}
