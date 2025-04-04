<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return response(['status' => 422, 'message' => $validator->errors()->first()]);
        }
        $email = $request->email;

        if (!Str::endsWith($email, '.com')) {
            return response(['status' => 422, 'message' => 'Only users with a .com email address can register.']);
        }
    
        DB::beginTransaction();
        try {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            DB::commit();
            return response(['data' => $user,'status' => 200, 'message' => 'Register successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response(['data' => $user, 'status' => 422, 'message' => 'Failed to Register.']);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['status' => 422, 'message' => 'User Unauthenticated']);
        }
        $apiToken = $user->createToken('API Token')->plainTextToken; // Use Sanctum's createToken method
        $user->api_token = $apiToken;
        $user->save();
    
        return response()->json([
            'data' => $user,
            'status' => 200,
            'message' => 'Login successfully',
            'api_token' => $apiToken  // Include token in the response
        ]);;
    }
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>422,'message' => $validator->errors()]);
        }

        $user = User::where('id', $request->user_id)->where('api_token', $request->api_token)->first();
        if (!empty($user)) {
            $user->update([
                'api_token' => '',
            ]);
          return response(['status' => 200, 'message' => 'Logout successfully.']);

        }
        return response(['status' => 422, 'message' => 'User not found']);
    }


}
