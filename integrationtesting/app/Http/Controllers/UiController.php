<?php
declare(strict_types=1);

namespace App\Http\Controllers;

class UiController extends Controller
{
    public function home()
    {
        return redirect()->route('products.index');
    }
}
