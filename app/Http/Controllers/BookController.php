<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;


class BookController extends Controller
{
    //This method will show books listing page
    public function index(Request $request){
        $books=Book::OrderBY('created_at','DESC');
        if (!empty($request->keyword)) {
            $books->where('title','like','%'.$request->keyword.'%');
        }
        $books=$books->paginate(10);
     return view('books.list',['books'=>$books]);
    }

    //This method will show create book page
    public function create(){
    return view('books.create');
    }

    //This method will store a book data
    public function store(Request $request){
        $rules=[
            'title'=>'required',
            'author'=>'required',
            'status'=>'required'
        ];
        if (!empty($request->image)) {
            $rules['image']='image';
        }

     $validator=Validator::make($request->all(),$rules);
     if ($validator->fails()) {
        return redirect()->route('books.create')->withInput()->withErrors($validator);
     }
     //Store book data in db
     $book=new Book();
     $book->title=$request->title;
     $book->author=$request->author;
     $book->description=$request->description;
     $book->status=$request->status;
     $book->save();

     //upload book image here
     if (!empty($request->image)) {
        $image=$request->image;
        $ext=$image->getClientOriginalExtension();
        $imageName=time().'.'.$ext;
        $image->move(public_path('uploads/books/'),$imageName);
        $book->image=$imageName;
        $book->save();

        $manager=new ImageManager(Driver::class);
        $img = $manager->read(public_path('uploads/books/'.$imageName));
        $img->resize(999);
        $img->save(public_path('uploads/books/thumb/'.$imageName));
     }
     return redirect()->route('books.index')->with('success','Book added successfully!');
    }

    //This method will edit book page
    public function edit($id){
        $book=Book::findOrFail($id);
        return view('books.edit',['book'=>$book]);
    }

    //This method will update the book data
    public function update($id,Request $request){
        $book=Book::findOrFail($id);
        $rules=[
            'title'=>'required',
            'author'=>'required',
            'status'=>'required'
        ];
        if (!empty($request->image)) {
            $rules['image']='image';
        }

     $validator=Validator::make($request->all(),$rules);
     if ($validator->fails()) {
        return redirect()->route('books.edit',$book->id)->withInput()->withErrors($validator);
     }
     //update book data in db
     $book->title=$request->title;
     $book->author=$request->author;
     $book->description=$request->description;
     $book->status=$request->status;
     $book->save();

     //upload book image here
     if (!empty($request->image)) {
        //This code will delete old image
        File::delete(public_path('uploads/books/'.$book->image));
        File::delete(public_path('uploads/books/thumb/'.$book->image));

        $image=$request->image;
        $ext=$image->getClientOriginalExtension();
        $imageName=time().'.'.$ext;
        $image->move(public_path('uploads/books/'),$imageName);
        $book->image=$imageName;
        $book->save();

        $manager=new ImageManager(Driver::class);
        $img = $manager->read(public_path('uploads/books/'.$imageName));
        $img->resize(999);
        $img->save(public_path('uploads/books/thumb/'.$imageName));
     }
     return redirect()->route('books.index')->with('success','Book updated successfully!');

    }

    //This method will delete the book
    public function destroy(){

    }
}
