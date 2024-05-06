<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function loadLayout(View $page, $title, $data = []) {

        return view('layout', [
            'title' => $title,
            'page' => $page,
            'data' => $data,
            'app_name' => config('app.name'),
        ]);
    }

    public function sendMail(Mailable $mailable, string $to)
    {
        try {
            Mail::to($to)->send($mailable);
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
        return true;
    }
}
