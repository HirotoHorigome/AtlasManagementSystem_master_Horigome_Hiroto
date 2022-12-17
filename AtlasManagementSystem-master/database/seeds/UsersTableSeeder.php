<?php

use Illuminate\Database\Seeder;
use App\Models\Users\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'over_name' => '堀込',
            'under_name' => '宏斗',
            'over_name_kana' => 'ホリゴメ',
            'under_name_kana' => 'ヒロト',
            'mail_address' => 'hiroto@gmail.com',
            'sex' => '1',
            'birth_day' => '1995-05-28',
            'role' => '1',
            'password' => bcrypt('0273442654'),
        ]);

        User::create([
            'over_name' => '堀込',
            'under_name' => '一',
            'over_name_kana' => 'ホリゴメ',
            'under_name_kana' => 'イチ',
            'mail_address' => 'horigome1@gmail.com',
            'sex' => '1',
            'birth_day' => '1995-05-28',
            'role' => '4',
            'password' => bcrypt('0273442654'),
        ]);
    }
}
