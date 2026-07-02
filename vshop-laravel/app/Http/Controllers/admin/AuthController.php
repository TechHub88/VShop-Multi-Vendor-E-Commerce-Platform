<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
class AuthController extends Controller
{
     public function changePassword()
    {
        return view('admin.auth.changepassword');
    }
public function updatePassword(Request $request)
{
    $request->validate([
        'old_password' => 'required',
        'new_password' => 'required|min:6',
        'confirm_password' => 'required|same:new_password',
    ]);

    /** @var User $user */
    $user = Auth::user(); // <— Telling Intelephense user is Eloquent

    if (!Hash::check($request->old_password, $user->password)) {
        return back()->with('error', 'Old password is incorrect');
    }

    $user->password = Hash::make($request->new_password);
    $user->save(); // ✅ Now no warning

    return back()->with('success', 'Password changed successfully!');
}

}

