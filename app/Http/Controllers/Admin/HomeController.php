<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;
use File;

class HomeController extends Controller
{
	public function profile()
	{
		return view('admin.home.profile');
	}

    public function update_password(Request $request)
    {
        $request->validate([
            'old_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'confirm_password' => ['required', 'same:new_password'],
        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
        return response()->json(['status' => TRUE, 'msg' => 'Password Updated Successfully!']);

        /*$notification = [
            'message' => 'Password Updated successfully',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);*/
    }



	public function profile_update(Request $request)
	{
    $user = Auth::user();
    $this->validate($request, [
        'name' => 'required',
        'designation' => 'required',
        'bio' => 'required',
    ]);

    $fileName = null;
    if (request()->hasFile('user_img')) 
    {
        $file = request()->file('user_img');
        $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
        $file->move('./uploads/', $fileName); 

        File::delete('./uploads/' . $user->user_img);               
    }

    $data = $request->all();
    $data['user_img'] = $fileName;
    $user->update($data);
    return redirect()->back();
	}
}
