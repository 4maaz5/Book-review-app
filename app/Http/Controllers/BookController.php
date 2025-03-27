<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;

class BookController extends Controller
{
    //This method will show books listing page
    public function index(){
     return view('books.list');
    }

    //This method will show create book page
    public function create(){
    return view('books.create');
    }

    //This method will store a book data
    public function store(Request $request){
        $rules=[
            'title'=>'required|min:5',
            'author'=>'required|min:5',
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
     return redirect()->route('books.list')->with('success','Book added successfully!');
    }

    //This method will edit book page
    public function edit(){

    }

    //This method will update the book data
    public function update(){

    }

    //This method will delete the book
    public function destroy(){

    }
}
