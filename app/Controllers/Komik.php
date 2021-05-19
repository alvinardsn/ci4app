<?php

namespace App\Controllers;

use  App\Models\KomikModel;

class Komik extends BaseController

{
    protected $komikModel;

    public function __construct()
    {
        $this->komikModel = new KomikModel();
    }

    public function index()
    {
        // $komik = $this->komikModel->findAll();
        $data = [
            'title' => 'Daftar Komik',
            'komik' => $this->komikModel->getKomik()
        ];

        return view('komik/index', $data);
    }

    public function detail($slug)
    {
        $data = [
            'title' => 'Detail Komik',
            'komik' => $this->komikModel->getKomik($slug)
        ];

        // jika komik tidak ada di tabel
        if (empty($data['komik'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul Komik ' . $slug . ' tidak ditemukan');
        }

        return view('komik/detail', $data);
    }

    public function create()
    {
        // session(); // untuk mengambil session dari validation. namun session() sudah ada di base controller
        $data = [
            'title' => 'Form Tambah Data',
            'validation' => \Config\Services::validation() // validation berfungsi untuk mendapatkan validasi di method save
        ];
        return view('komik/create', $data);
    }

    // mengelola data dari create ke database
    public function save()
    {
        // validasi input
        if (!$this->validate([
            // 'judul' => 'required|is_unique[komik.judul]'
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required' => '{field} komik harus diisi.',
                    'is_unique' => '{field} komik sudah terdaftar'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'File yang anda upload bukan gambar',
                    'mime_in' => 'File yang anda upload bukan gambar'
                ]
            ],
            'penulis' => 'required[komik.penulis]',
            'penerbit' => 'required[komik.penerbit]'
        ])) {
            // $validation = \Config\Services::validation();

            // // fungsi with input untuk mengirim semua data untuk dikirim ke session, dan with untuk mengirim data validation
            // return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
            return redirect()->to('/komik/create')->withInput()->withInput();
        }

        // ambil gambar
        $fileSampul = $this->request->getFile('sampul');
        // cek apakah tidak ada gambar yang di upload
        if ($fileSampul->getError() == 4) {
            $namaSampul = 'default.png';
        } else {
            // generate nama sampul random
            $namaSampul = $fileSampul->getRandomName();
            // pindahkan file gambar ke folder img
            $fileSampul->move('img', $namaSampul);
            // // ambil nama file sampul untuk insert ke database
            // $namaSampul = $fileSampul->getName();
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'ditambahkan!');

        return redirect()->to('/komik');
    }

    public function delete($id)
    {
        // cari gambar berdasarkan id
        $komik = $this->komikModel->find($id);

        // cek jika file gambarnya default.png
        if ($komik['sampul'] != 'default.png') {
            // menghapus gambar berdasarkan id
            unlink('img/' . $komik['sampul']);
        } // jika data yg memiliki file gambar default.png maka data tersebut akan dihapus namun tidak gambar default.png

        $this->komikModel->delete($id); // untuk menghapus data di model dan model menghapus di dalam table

        session()->setFlashdata('pesan', 'dihapus!');
        return redirect()->to('/komik');
    }

    public function edit($slug)
    {
        $data = [
            'title' => 'Form Ubah Data',
            'validation' => \Config\Services::validation(),
            'komik' => $this->komikModel->getKomik($slug)
        ];
        return view('komik/edit', $data);
    }

    public function update($id)
    {
        // cek judul
        $komikLama = $this->komikModel->getKomik($this->request->getVar('slug'));
        if ($komikLama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }

        if (!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '{field} komik harus diisi.',
                    'is_unique' => '{field} komik sudah terdaftar'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'File yang anda upload bukan gambar',
                    'mime_in' => 'File yang anda upload bukan gambar'
                ]
            ],
            'penulis' => 'required[komik.penulis]',
            'penerbit' => 'required[komik.penerbit]'
        ])) {
            // $validation = \Config\Services::validation();
            // return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput()->with('validation', $validation);
            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput();
        }

        $fileSampul = $this->request->getFile('sampul');

        // cek gambar, apakah tetap gambar lama
        if ($fileSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampulLama');
        } else {
            // untuk file baru
            // generate nama file random
            $namaSampul = $fileSampul->getRandomName();
            // pindahkan gambar ke folder img
            $fileSampul->move('img', $namaSampul);

            // hapus file yang lama
            // jika sampul lama bukan default.png maka hapus file yg gambar yg lama. jika file gambar default.png maka langsung update
            if ($this->request->getVar('sampulLama') != 'default.png') {
                unlink('img/' . $this->request->getVar('sampulLama'));
            }
        }


        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'diubah!');

        return redirect()->to('/komik');
    }
}
