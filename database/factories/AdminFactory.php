<?php

namespace Database\Factories;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition()
    {
        $factory->define(Admin::class, function (Faker $faker) {
            return [
                'name' => $faker->name, // name カラムに適切な値を設定
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ];
        });
        // return [
        //     'name' => $this->faker->name,
        //     'email' => $this->faker->unique()->safeEmail,
        //     'password' => bcrypt('password'), // パスワードのデフォルト値
        //     'is_admin' => 1, // 管理者として作成
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     // 'remember_token' => Str::random(10),
        // ];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    }

    
}
