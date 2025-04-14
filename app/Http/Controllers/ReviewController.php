<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    //This method will show review page to admin
    public function index(Request $request){
        $reviews=Review::with('book')->orderBy('created_at','DESC');
     if (!empty($request->keyword)) {
        $reviews=$reviews->where('review','like','%'.$request->keyword.'%');
     }
        $reviews=$reviews->paginate(10);
      return view('account.Reviews.list',['reviews'=>$reviews]);
    }
    //This method will show edit review page
    public function edit($id){
      $review=Review::findOrFail($id);
      return view('account.Reviews.edit',['review'=>$review]);
    }
    //This method will update the review
    public function updateReview($id,Request $request){
        $review=Review::findOrFail($id);
      $validator=Validator::make($request->all(),[
         'review'=>'required',
         'status'=>'required'
      ]);
      if ($validator->fails()) {
        return redirect()->route('account.reviews.edit',$id)->withInput()->withErrors($validator);
      }
      $review->review=$request->review;
      $review->status=$request->status;
      $review->save();
      session()->flash('success','Review updated Successfully.');
      return redirect()->route('account.reviews');
    }
    public function deleteReview(Request $request){
      $id=$request->id;
      $review=Review::find($id);
      if($review==null){
        session()->flash('error','Review not found!');
        return response()->json([
          'status'=>false
        ]);
      }else{
        $review->delete();
        session()->flash('success','Review deleted successfully!');
        return response()->json([
           'status'=>true
        ]);
      }
    }
}
