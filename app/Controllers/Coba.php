<?php

namespace App\Controllers;

class Coba extends BaseController
{
    public function index()
    {
        echo "ini adalah menu index coba.";
    }

    public function about($nama = '', $umur = '')
    {
        echo "saya adalah $nama dan saya berumur $umur tahun.";
        echo '<br>';
        echo "ini nama default dari Basecontroller ($this->nama)";
    }
}
