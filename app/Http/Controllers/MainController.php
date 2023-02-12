<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use App\Models\StudentReportCard;
use App\Models\Utils;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

class MainController extends Controller
{

    public function password_forget_email()
    {
        session_start();
        return view('password-forget-email');
    }

    public function password_forget_code()
    {
        session_start();
        $email = "";
        if (isset($_SESSION['reset_email'])) {
            if ($_SESSION['reset_email'] != null) {
                if (strlen($_SESSION['reset_email']) > 3) {
                    $email = $_SESSION['reset_email'];
                }
            }
        }


        return view('password-forget-code', ['email' => $email]);
    }




    public function doChangePassword(Request $r)
    {

        session_start();
        if ($r->username == null) {
            return Redirect::back()->withErrors(['username' => ['Email address is required.']])->withInput();
        }
        if ($r->code == null) {
            return Redirect::back()->withErrors(['code' => ['Code is required.']])->withInput();
        }
        if ($r->password == null) {
            return Redirect::back()->withErrors(['password' => ['Password is required.']])->withInput();
        }

        if ($r->password_2 == null) {
            return Redirect::back()->withErrors(['password_2' => ['Password is required.']])->withInput();
        }

        if ($r->password_2 != $r->password) {
            return Redirect::back()->withErrors(['password' => ['Passwords is did not match.']])->withInput();
        }

        $u = Administrator::where('email', $r->username)
            ->orWhere('username', $r->username)
            ->first();

        if ($u == null) {
            return $this->error('Account with  provided email not found.');
        }


        if ($u->code != $r->code) {
            return Redirect::back()->withErrors(['code' => ['Verification code did not match. Copy the code we sent to your email correctly and try again.']])->withInput();
        }

        $u->password = password_hash(trim($r->password), PASSWORD_DEFAULT);
        $u->save();

        $_SESSION['reset_message'] = 'Password changed successfully. You can now login with your new password.';
        return redirect(admin_url('auth/login'));
    }


    public function password_forget_email_post(Request $r)
    {

        if ($r->username == null) {
            return Redirect::back()->withErrors(['username' => ['Email or Phone number is required.']])->withInput();
        }

        $u = Administrator::where('email', $r->username)
            ->orWhere('username', $r->username)
            ->first();

        if ($u == null) {
            return Redirect::back()->withErrors(['username' => ['Account with provided email address was not found.']])->withInput();
        }

        session_start();
        $_SESSION['reset_email'] = $r->username;
        $_SESSION['reset_message'] = $r->username;


        $success = false;
        $message = "";
        try {
            $u->sendPasswordResetCode();
            $message = "We have sent a secret code to your email address {$u->email}. Check your email  inbox or spam and use that code to reset your password.";
            $success = true;
        } catch (\Throwable $th) {
            $message = 'Failed to send you email addresss.';
            $success = false;
        }

        if ($success) {
            return redirect(url('password-forget-code'));
        }

        $_SESSION['reset_failed'] =  $message;
        return Redirect::back()->withInput();
    }
}
