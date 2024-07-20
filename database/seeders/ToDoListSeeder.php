<?php

namespace Database\Seeders;

use App\Models\ToDo;
use Illuminate\Database\Seeder;

class ToDoListSeeder extends Seeder {
    /**
     * Run the database seeder
     * 
     * @return void
    */

    public function run() {
        $data = [];

        $faker = \Faker\Factory::create(); 

        for($x = 0; $x < 5; $x++) {
            $data[] = [
                'name' => $faker->name,
                'completed' => $faker->boolean
            ];
        }

        ToDo::insert($data);
    }
}