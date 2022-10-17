<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $movie = new Movie();
        $movie->name = fake()->realText(20);
        $movie->rate = "R15+";
        $movie->type = "Action";
        $movie->length_time = fake()->numberBetween(75, 210);
        $movie-> save();

        $movie = new Movie();
        $movie->name = fake()->realText(20);
        $movie->rate = "R18+";
        $movie->type = "Horror";
        $movie->length_time = fake()->numberBetween(75, 210);
        $movie->save();

        $movie = new Movie();
        $movie->name = fake()->realText(20);
        $movie->rate = "G";
        $movie->type = "Cartoon";
        $movie->length_time = fake()->numberBetween(75, 210);
        $movie->save();

    }
}
