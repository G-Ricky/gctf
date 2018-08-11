<?php

use Illuminate\Database\Seeder;

class ChallengesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0;$i < 50;++$i) {
            $now = time();
            $create = $now - rand(0, 3600 * 24 * 30);
            $update = rand($create, $now);
            DB::table('challenges')->insert([
                'title'       => str_random(20),
                'description' => str_random(500),
                'category'    => array_random(['CRYPTO', 'MISC', 'PWN', 'REVERSE', 'WEB']),
                'poster'      => 1,
                'points'      => 500,
                'flag'        => 'gctf{'.str_random(20).'}',
                'bank'        => 1,
                'is_hidden'   => rand(0, 1),
                'deleted_at'  => null,
                'created_at'  => date('Y-m-d H:i:s', $create),
                'updated_at'  => date('Y-m-d H:i:s', $update)
            ]);
        }
    }
}
