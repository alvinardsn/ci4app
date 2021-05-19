<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class OrangSeeder extends Seeder
{
    public function run()
    {
        // $data = [
        //     [
        //         'nama' => 'muhammad alvin ardiansayh',
        //         'alamat' => 'Jl. bumi sani permai',
        //         'created_at' => Time::now(),
        //         'updated_at' => Time::now()
        //     ],
        //     [
        //         'nama' => 'Nurmarwah Kurniawan',
        //         'alamat' => 'Jl. seroja',
        //         'created_at' => Time::now(),
        //         'updated_at' => Time::now()
        //     ],
        //     [
        //         'nama' => 'Kyra Nardsn',
        //         'alamat' => 'Jl. Bumi perkemahan',
        //         'created_at' => Time::now(),
        //         'updated_at' => Time::now()
        //     ]
        // ];

        $faker = \Faker\Factory::create('id_ID');
        for ($i = 0; $i < 50; $i++) {
            $data = [
                'nama' => $faker->name,
                'alamat' => $faker->address,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ];
            // Simple Queries
            // $this->db->query("INSERT INTO orang (nama, alamat, created_at, updated_at) VALUES(:nama:, :alamat:, :created_at:, :updated_at:)", $data);

            // Using Query Builder
            $this->db->table('orang')->insert($data); // insert untuk menambah data 1 sedangakn insertBatch untuk menambahkan data yg banyak
        }
    }
}
