<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Cart;
use Session;
class PaymentController extends Controller
{
	 public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function paymentProcess(Request $request)
    {
    	 $data=array();
    	 $data['name']=$request->name;
    	 $data['email']=$request->email;
    	 $data['phone']=$request->phone;
    	 $data['address']=$request->address;
    	 $data['city']=$request->city;
    	 $data['payment']=$request->payment;

    	 if ($request->payment == 'bkash') {

    	 	  //stripe payment pages
           
            
    	 	 return view('pages.payment.bkash',compact('data'));

    	 }else{

    	 	 return view('pages.payment.cod',compact('data'));
    	 }

    }



    public function bkashCharge(Request $request)
    {
    	   $total=$request->total;
           $userData = Auth::user();
         

			$data=array();
			$data['user_id']=$userData->id;
           // $data['user']=Auth::user();
           

        
			//$data['payment_id']=$charge->payment_method;
			$data['paying_amount']=$total;
			//$data['blnc_transection']=$charge->balance_transaction;
			// $data['stripe_order_id']=$charge->metadata->order_id;
			$data['shipping']=$request->shipping;
			$data['vat']=$request->vat;
			$data['total']=$request->total;
            $data['payment_type']=$request->payment_type;
            $data['bkash_trx_no']=$request->bkash_trx_no;

            if($this->checkBkashExitance($request->bkash_trx_no))
            {
                $notification=array(
                          'messege'=>'Transaction ID Already Exist or Invalid',
                           'alert-type'=>'error'
                     );
                return Redirect()->to('payment/page')->with($notification);
            }


			 if (Session::has('coupon')) {
			 	 $data['subtotal']=Session::get('coupon')['balance'];
    	     }else{
    	  	      $data['subtotal']=Cart::Subtotal() ;
    	    }
    	    $data['status']=0;
    	    $data['date']=date('d-m-y');
    	    $data['month']=date('F');
    	    $data['year']=date('Y');
            $data['status_code']=mt_rand(100000,999999); 
    	    $order_id=DB::table('orders')->insertGetId($data);

    	    // insert shipping details table

    	    	$shipping=array();
    	    	$shipping['order_id']=$order_id;
    	    	$shipping['ship_name']=$request->ship_name;
    	    	$shipping['ship_email']=$request->ship_email;
    	    	$shipping['ship_phone']=$request->ship_phone;
    	    	$shipping['ship_address']=$request->ship_address;
    	    	$shipping['ship_city']=$request->ship_city;
    	    	DB::table('shipping')->insert($shipping);

    	    	//insert data into orderdeatils
    	    	$content=Cart::content();
    	    	$details=array();
    	    	foreach ($content as $row) {
    	    		$details['order_id']= $order_id;
    	    		$details['product_id']=$row->id;
    	    		$details['product_name']=$row->name;
    	    		$details['color']=$row->options->color;
    	    		$details['size']=$row->options->size;
    	    		$details['quantity']=$row->qty;
    	    		$details['singleprice']=$row->price;
    	    		$details['totalprice']=$row->qty * $row->price;
    	    		DB::table('order_details')->insert($details);
    	    	}

    	    	Cart::destroy();
    	    	 if (Session::has('coupon')) {
			 	 Session::forget('coupon');
    	     }

    	       $notification=array(
                              'messege'=>'Successfully Done',
                               'alert-type'=>'success'
                         );

        $singleOrder = DB::table('orders')
                             ->orderBy('orders.id','desc')
                             ->first();
         $allinfo = [
           
            'customer_details' => $userData,
            'orderSingle'     => $singleOrder ,
            'orderDetails' => DB::table('order_details')
                             ->where([
                                'order_details.order_id'=>$singleOrder->id,
                            ])
                             ->orderBy('id','asc')
                             ->get(),
            'bkash_trx_no' =>$request->bkash_trx_no,
            'total'        =>$request->total,
            'payment_type' =>$request->payment_type,
           
        ];

        try {
            
            \Mail::to(env('ADMIN_EMAIL'))->send(new \App\Mail\OrderMail($allinfo));
            \Mail::to(Auth::user()->email)->send(new \App\Mail\OrderMail($allinfo));
            //throw new \Exception("failed");
            return redirect('/#success-block-mail')->with($notification); 

        } catch (Exception $e) {
            echo $e->getMessage();
        
        }

        return Redirect()->to('/')->with($notification);
			
    }


    public function cashCharge(Request $request)
    {

           $total=$request->total;
           $userData = Auth::user();
         

            $data=array();
            $data['user_id']=$userData->id;
           // $data['user']=Auth::user();
           

        
            //$data['payment_id']=$charge->payment_method;
            $data['paying_amount']=$total;
            //$data['blnc_transection']=$charge->balance_transaction;
            // $data['stripe_order_id']=$charge->metadata->order_id;
            $data['shipping']=$request->shipping;
            $data['vat']=$request->vat;
            $data['total']=$request->total;
            $data['payment_type']=$request->payment_type;


             if (Session::has('coupon')) {
                 $data['subtotal']=Session::get('coupon')['balance'];
             }else{
                  $data['subtotal']=Cart::Subtotal() ;
            }
            $data['status']=0;
            $data['date']=date('d-m-y');
            $data['month']=date('F');
            $data['year']=date('Y');
            $data['status_code']=mt_rand(100000,999999); 
            $order_id=DB::table('orders')->insertGetId($data);

            // insert shipping details table

                $shipping=array();
                $shipping['order_id']=$order_id;
                $shipping['ship_name']=$request->ship_name;
                $shipping['ship_email']=$request->ship_email;
                $shipping['ship_phone']=$request->ship_phone;
                $shipping['ship_address']=$request->ship_address;
                $shipping['ship_city']=$request->ship_city;
                DB::table('shipping')->insert($shipping);

                //insert data into orderdeatils
                $content=Cart::content();
                $details=array();
                foreach ($content as $row) {
                    $details['order_id']= $order_id;
                    $details['product_id']=$row->id;
                    $details['product_name']=$row->name;
                    $details['color']=$row->options->color;
                    $details['size']=$row->options->size;
                    $details['quantity']=$row->qty;
                    $details['singleprice']=$row->price;
                    $details['totalprice']=$row->qty * $row->price;
                    DB::table('order_details')->insert($details);
                }

                Cart::destroy();
                 if (Session::has('coupon')) {
                 Session::forget('coupon');
             }

               $notification=array(
                              'messege'=>'Successfully Done',
                               'alert-type'=>'success'
                         );

        $singleOrder = DB::table('orders')
                             ->orderBy('orders.id','desc')
                             ->first();
         $allinfo = [
           
            'customer_details' => $userData,
            'orderSingle'     => $singleOrder ,
            'orderDetails' => DB::table('order_details')
                             ->where([
                                'order_details.order_id'=>$singleOrder->id,
                            ])
                             ->orderBy('id','asc')
                             ->get(),
            'total'        =>$request->total,
            'payment_type' =>$request->payment_type,
           
        ];

        try {
            
            \Mail::to(env('ADMIN_EMAIL'))->send(new \App\Mail\OrderMail($allinfo));
            \Mail::to(Auth::user()->email)->send(new \App\Mail\OrderMail($allinfo));
            //throw new \Exception("failed");
            return redirect('/#success-block-mail')->with($notification); 

        } catch (Exception $e) {
            echo $e->getMessage();
        
        }

        return Redirect()->to('/')->with($notification);
            
    }

    public function SuccessList()
    {
         $order=DB::table('orders')->where('user_id',Auth::id())->where('status',3)->orderBy('id','DESC')->limit(10)->get();
         return view('pages.returnorder',compact('order'));
    }

    public function RequestReturn($id)
    {
        DB::table('orders')->where('id',$id)->update(['return_order'=>1]);
         $notification=array(
                              'messege'=>'Order Return request done please wait for our confirmation email',
                               'alert-type'=>'success'
                         );
        return Redirect()->back()->with($notification);
    }


    /**
    Check Bkash Trx Existance
    */
    private function checkBkashExitance($trxId)
    {
        $row = DB::table('orders')
                ->where(array(
                    'bkash_trx_no'=> $trxId,
                ))->get()->count();
        if ($row > 0) {
            return true;
        }else{
            return false;
        }

    }


}
