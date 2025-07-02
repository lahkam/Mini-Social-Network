<?php
namespace App\Http\Algo;

class Convertissur
{

    function argent($mnt,$monaie)
    {
        $res=0;
        switch ($monaie) {
            case 'dollar':
                # code...
                $res= $mnt*10;
                break;
            case 'euro':
                # code...
                $res= $mnt*11.2;
                break;
            default:
                # code...
                break;
        }
        return $res;
    }
}


?>