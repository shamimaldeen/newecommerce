<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Exception;
class BlogController extends Controller
{
     public function blog()
     {
     	$post=DB::table('posts')->join('post_category','posts.category_id','post_category.id')->select('posts.*','post_category.category_name_en','post_category.category_name_bn')->get();
     	 return view('pages.blog',compact('post'));
     	 
     }

     public function contact()
     {
         // $post=DB::table('posts')->join('post_category','posts.category_id','post_category.id')->select('posts.*','post_category.category_name_en','post_category.category_name_bn')->get();
           return view('pages.contact');

           
     }


     public function contactstore(Request $request)
    {
     $data=array();
     $data['name']=$request->name;
     $data['email']=$request->email;
     $data['phone']=$request->phone;
     $data['message']=$request->message;
     DB::table('contacts')->insert($data);
     $notification=array(
                 'messege'=>'Successfully Message Delivery Done',
                 'alert-type'=>'success'
                       );

       // return Redirect()->back()->with($notification);


          $contact = [
            
            'name'     =>$request->name,
            'email'      =>$request->email,
            'phone'      =>$request->phone,
            'message'   =>$request->message
        ];

        try {
            /*$e = FlattenException::create($exception);

            $handler = new SymfonyExceptionHandler();

            $html = $handler->getHtml($e);
            */
         \Mail::to('shamimaldeen@gmail.com')->send(new \App\Mail\MyTestMail($contact));
            //throw new \Exception("failed");
            return redirect('/#success-block-mail')->with($notification); 

        } catch (Exception $e) {
            echo $e->getMessage();
        
        }
    }

     public function Bangla()
     {
     	Session::get('lang');
     	session()->forget('lang');
     	Session::put('lang','bangla');
     	return redirect()->back();


     }

     public function English()
     {
     	Session::get('lang');
     	session()->forget('lang');
     	Session::put('lang','english');
     	return redirect()->back();

     }


}
