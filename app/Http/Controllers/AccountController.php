<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class AccountController extends Controller
{
    public function register(){
        return view('account.register');
    }
    public function processRegister(request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required|min:5',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed|min:5',
            'password_confirmation'=>'required'
        ]);
        if ($validator->fails()) {
            return redirect()->route('account.register')->withInput()->withErrors($validator);
        }
        $user=new User();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=Hash::make($request->password);
        $user->save();
        return redirect()->route('account.login')->with('success','You have log-in successfully!');
    }
    public function login(){
        return view('account.login');
    }
    public function authenticate(Request $request){
       $validator=Validator::make($request->all(),[
           'email'=>'required|email',
           'password'=>'required'
       ]);
       if ($validator->fails()) {
        return redirect()->route('account.login')->withInput()->withErrors($validator);
       }
       if (Auth::attempt(['email'=>$request->email,'password'=>$request->password])) {
        return redirect()->route('account.profile');
       }
       else{
        return redirect()->route('account.login')->with('error','Either email/password is incorrect.');
       }
    }
    public function profile(){
        $user=User::find(Auth::user()->id);
        return view('account.profile',['user'=>$user]);
    }
    public function updateProfile(Request $request){
        $rules=[
            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.Auth::user()->id.',id'
        ];
        if (!empty($request->image)) {
            $rules['image']='image';
        }
         $validator=Validator::make($request->all(),$rules);
         if ($validator->fails()) {
            return redirect()->route('account.profile')->withInput()->withErrors($validator);
         }
         $user=User::find(Auth::user()->id);
         $user->name=$request->name;
         $user->email=$request->email;
         $user->save();

         if (!empty($request->image)) {
            //Delete Old Image when new updated
            File::delete(public_path('uploads/profile/'.$user->image));
            File::delete(public_path('uploads/profile/thumb/'.$user->image));
         $image=$request->image;
         $ext=$image->getClientOriginalExtension();
         $imageName=time().'.'.$ext;
         $image->move(public_path('uploads/profile'),$imageName);
         $user->image=$imageName;
         $user->save();
         $manager=new ImageManager(Driver::class);
         $img = $manager->read(public_path('uploads/profile/'.$imageName));
         $img->cover(150,150);
         $img->save(public_path('uploads/profile/thumb/'.$imageName));

        }

         return redirect()->route('account.profile')->with('success','Profile updated Successfully!');
    }
    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }
    public function myReviews(Request $request){
        $reviews=Review::with('Book')->where('user_id',Auth::user()->id);
        $reviews=$reviews->orderBy('created_at','DESC');
        if (!empty($request->keyword)) {
            $reviews=$reviews->where('review','like','%'.$request->keyword.'%');
        }
        $reviews=$reviews->paginate(10);
       return view('account.my-reviews.myReviews',['reviews'=>$reviews]);
    }
    public function editReviews($id){
       $review=Review::where([
          'id'=>$id,
          'user_id'=>Auth::user()->id
       ])->with('Book')->first();
       return view('account.my-reviews.my-Reviews-edit',['review'=>$review]);
    }

      //This method will update the my-review
      public function updateReviews($id,Request $request){
        $review=Review::findOrFail($id);
      $validator=Validator::make($request->all(),[
         'review'=>'required',
         'rating'=>'required'
      ]);
      if ($validator->fails()) {
        return redirect()->route('account.myReviews.edit',$id)->withInput()->withErrors($validator);
      }

      $review->review=$request->review;
      $review->rating=$request->rating;
      $review->save();
      session()->flash('success','Review updated Successfully.');
      return redirect()->route('account.myReviews');
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
