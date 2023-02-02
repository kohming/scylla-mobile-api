<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Firebase\JWT\JWT;
use Exception;
use App\Models\User;
use App\Traits\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Notifications\UserMustApproved;
use Illuminate\Support\Facades\Hash;
use App\Notifications\WaitingVerifyUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;


class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request)
    {
        // $validator = $this->validate($request, [
        //     'name' => 'required|min:6|max:255',
        //     'email' => 'required|email|unique:users,email',
        //     'password' => 'required'
        // ]);

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

       
    //    $user = new User();
    //    $user->name = $validated['name'];
    //    $user->email = $validated['email'];
    //    $user->password = Hash::make($validated['password']);
    //    $user->save();
       return response()->json($user, 201);
    }


    public function login(Requesr $request) {

        $validated = $request->validate([
            'email' => 'required|email|exists:users.email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if(!Hash::check($validated['password'], $user->password)) {
            return abort(401, "email or password not valid");
        }

        $payload = [
                'iat' => intval(microtime(true)),
                'exp' => inttval(microtime(true)) + (60 * 60 * 1000),
                'uid' => $user -> id
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'));
        return response()->json(['access_token' => $token]);

    }
    
}
