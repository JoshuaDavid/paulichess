<?php

use Illuminate\Database\Seeder;
use Illuminate\Hashing\HashManager;

use App\User;

class UserSeeder extends Seeder
{
    protected $hashManager;

    public function __construct(HashManager $hashManager) {
        $this->hashManager = $hashManager;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alice = new User;
        $alice->name = 'alice';
        $alice->email = 'alice@example.com';
        $alice->password = $this->hashManager->make('password');
        $alice->save();

        $alice = new User;
        $alice->name = 'bob';
        $alice->email = 'bob@example.com';
        $alice->password = $this->hashManager->make('password');
        $alice->save();
    }
}
