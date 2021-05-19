<?php

namespace App\Controllers;

use CodeIgniter\Config\Factory;

class Pages extends BaseController
{
	public function index()
	{
		$data = [
			'title' => 'Home | Alvinardsn'
		];
		return view('pages/home', $data);
	}

	public function about()
	{
		$data = [
			'title' => 'About | Alvinardsn'
		];
		return view('pages/about', $data);
	}

	public function contact()
	{
		$data = [
			'title' => 'Contact Us',
			'alamat' => [
				[
					'tipe' => 'Rumah',
					'alamat' => 'Jl. Pahlawan no 123',
					'kota' => 'Bekasi'
				],
				[
					'tipe' => 'Apartememt',
					'alamat' => 'Jl. Imam bonjol no 44',
					'kota' => 'Jakarta'
				]
			]
		];
		return view('pages/contact', $data);
	}
}
