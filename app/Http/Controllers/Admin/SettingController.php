<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function SiteSetting()
    {
    	 $setting=DB::table('sitesetting')->first();

      // return response()->json($setting);
    	 return view('admin.setting.site_setting',compact('setting'));
    }

    public function UpdateSetting(Request $request)
    {

        $oldlogo=$request->old_logo;
    	 $id=$request->id;
    	 $data=array();
    	 $data['phone_one']=$request->phone_one;
    	 $data['phone_two']=$request->phone_two;
    	 $data['email']=$request->email;
    	 $data['company_name']=$request->company_name;
    	 $data['company_address']=$request->company_address;
    	 $data['facebook']=$request->facebook;
    	 $data['youtube']=$request->youtube;
    	 $data['instagram']=$request->instagram;
    	 $data['twitter']=$request->twitter; 


             $image=$request->file('logo');
            if ($image) {
                unlink($oldlogo);
                $image_name= date('dmy_H_s_i');
                $ext=strtolower($image->getClientOriginalExtension());
                $image_full_name=$image_name.'.'.$ext;
                $upload_path='public/setting/';
                $image_url=$upload_path.$image_full_name;
                $success=$image->move($upload_path,$image_full_name);
                $data['logo']=$image_url;
                $setting=DB::table('sitesetting')->where('id',$id)->update($data);
                    $notification=array(
                     'messege'=>'Successfully Setting Updated ',
                     'alert-type'=>'success'
                    );
                return Redirect()->back()->with($notification);                 
            }else{
              $setting=DB::table('sitesetting')->where('id',$id)->update($data);
                 $notification=array(
                     'messege'=>'Update without image!',
                     'alert-type'=>'success'
                      );
                return Redirect()->back()->with($notification);
            }


    	 // DB::table('sitesetting')->where('id',$id)->update($data);
    	 // $notification=array(
      //            'messege'=>'Setting Update',
      //            'alert-type'=>'success'
      //                  );
      //   return Redirect()->back()->with($notification);
    }


}
