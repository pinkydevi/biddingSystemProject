<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home() {
        $view = view('home', [
            'app_name' => config('app.name'),
        ]);
        return $this->loadLayout($view, 'Auctions');
    }
}
