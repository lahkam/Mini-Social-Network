<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Calc extends Controller
{
    
   public function index()
    {
        return view('clac/index');
    }

    public function somme($a,$b)
    {
        
        return $a+$b;
    }
    public function mult(Request $request)
    {
        $res= $request['a']*$request['b'];
        return view('clac/mult',["value"=>$res]);
    }
}
