<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use  App\Http\Algo\Convertissur;

class convertisseurController extends Controller
{
     
    public function index()
    {
        return view('convertisseur/index');
    }

    public function argent( Request $request)
    {
        $conv =new Convertissur();

          $s=$request['sy'];
          $mnt=$request['mnt'];
         
            $res=$conv->argent($mnt,$s);
      

            return $res;
    }

    public function temperature()
    {
    }
}
