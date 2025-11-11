<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
		$admin = User::firstOrCreate(
			['email' => 'admin@example.com'],
			[
				'name' => 'Admin',
				'password' => Hash::make('admin123'),
				'role' => 'admin',
			]
		);		
		\App\Models\Post::factory(5)->create([
			'user_id' => $admin->id,
		]);


        // Categories with some hierarchy
        $rootCats = Category::factory()->count(3)->create();
        $subCats = Category::factory()->count(5)->make()->each(function($c) use ($rootCats){
            $c->parent_id = $rootCats->random()->id;
            $c->save();
        });

        // Tags
		 Tag::create([
                'name' => 'edited',
                'slug' => Str::slug('edited')
            ]);
        $tags = Tag::factory()->count(5)->create();

        // Posts
        Post::factory()->count(5)->create()->each(function($post) use ($tags, $rootCats) {
            $post->tags()->attach($tags->random(rand(1,4))->pluck('id')->toArray());
            $post->category_id = Category::inRandomOrder()->first()->id;
            $post->save();

            \App\Models\Comment::factory()->count(rand(0,5))->create(['post_id' => $post->id]);
        });
    }
}
