<?php

namespace App\Http\Controllers;

use App\Mail\ChangePassword;
use App\Mail\ForgotPassword;
use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login() {
        $view = view('login');

        return $this->loadLayout($view, 'Login');
    }

    public function API_Login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.max' => 'Email is too long',
            'email.exists' => 'Email does not exist',
            'password.required' => 'Password is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Login failed',
        ]);
    }

    public function logout() {
        Auth::logout();

        return redirect()->route('home');
    }

    public function register() {
        $view = view('register');

        return $this->loadLayout($view, 'Register');
    }

    public function API_Register(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:3',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.max' => 'Email is too long',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.confirmed' => 'Passwords do not match',
            'password.min' => 'Password is too short',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
        ]);
    }

    public function forgotPassword() {
        $view = view('forgotPassword');

        return $this->loadLayout($view, 'Forgot Password');
    }

    public function API_SendCode(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users,email',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.max' => 'Email is too long',
            'email.exists' => 'Email does not exist',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->code_expires_at != null && $user->code_expires_at > now()->addMinutes(4)) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait 1 minute before sending another code'
            ]);
        }

        $user->code = rand(100000, 999999);
        $user->code_expires_at = now()->addMinutes(5);
        $user->save();

        $this->sendMail(new ForgotPassword($user->code), $user->email);

        return response()->json([
            'success' => true,
            'message' => 'Code sent successfully',
        ]);
    }

    public function API_ResetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users,email',
            'code' => 'required',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.max' => 'Email is too long',
            'email.exists' => 'Email does not exist',
            'code.required' => 'Code is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->code_expires_at == null) {
            return response()->json([
                'success' => false,
                'message' => 'Code has not been sent',
            ]);
        }

        if ($user->code != $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Code is invalid',
            ]);
        }

        if ($user->code_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Code has expired',
            ]);
        }

        $newPassword = rand(100000, 999999);
        $user->password = bcrypt($newPassword);
        $user->code = null;
        $user->code_expires_at = null;
        $user->save();

        $this->sendMail(new ResetPassword($newPassword), $user->email);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. Check your email for your new password',
        ]);
    }

    public function changePassword() {
        $view = view('changePassword');

        return $this->loadLayout($view, 'Change Password');
    }

    public function API_ChangePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|confirmed|min:3',
        ], [
            'oldPassword.required' => 'Old password is required',
            'newPassword.required' => 'New password is required',
            'newPassword.confirmed' => 'Passwords do not match',
            'newPassword.min' => 'Password is too short',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        if ($request->oldPassword == $request->newPassword) {
            return response()->json([
                'success' => false,
                'message' => 'New password cannot be the same as old password',
            ]);
        }

        if (Hash::check($request->oldPassword, Auth::user()->password)) {
            $user = User::find(Auth::user()->id);
            $user->password = bcrypt($request->newPassword);
            $user->save();

            $this->sendMail(new ChangePassword(), Auth::user()->email);
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Old password is incorrect',
        ]);
    }
}
