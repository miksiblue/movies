<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Dotenv\Util\Str;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Mime\Encoder\Rfc2231Encoder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public $id;
    use VerifiesEmails;
    use ResetsPasswords;


    public function index(User $user)
    {

        $user = auth()->user();


        return view('/user/index', compact('user'));

    }

    public function reset(User $user)
    {
        $user = auth()->user();
        $userToken=$user->token;

        if(is_null($userToken))

            return back();

        else
            return view('/user/reset', compact('user','userToken'));

    }

    public function sendMail(User $user, Request $request)
    {

        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        $token =rand(1, 100);


        $user['token'] = $token;

        $user->save();


        Mail::to($request->email)->send(new ContactFormMail($user->name, $token));

        return back();


    }

    public function update(User $user, Request $request)
    {

        $data = \request()->validate([

            'email' => 'required',
            'name' => 'required',
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);


        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
        User::find(auth()->user()->id)->update(['token'=> null]);


     return redirect('/');

    }


}
