<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            "name" => "Master",
            "email" => "master@email",
            "password" => Hash::make('master123'),
            "created_at" => new DateTime(),
            "updated_at" => new DateTime()
        ]);

        $id_user = DB::table('users')->where('email', 'master@email')->first('id_user')->id_user;

        DB::table('addresses')->insert([
            "id_user" => $id_user,
            "postal_code" => "75075510",
            "city" => "Anápolis",
            "state" => "GO",
            "street" => "Rua 23",
            "neighborhood" => "Boa Vista",
            "complement" => "QD20A . Lt 18",
            "created_at" => new DateTime(),
            "updated_at" => new DateTime()
        ]);

    }
}
