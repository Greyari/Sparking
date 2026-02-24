<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'identitas' => fake()->numerify('########'),
            'email' => fake()->unique()->safeEmail(),
            'jenis_pengguna' => fake()->randomElement(['mahasiswa', 'dosen', 'karyawan', 'tamu']),
            'nama' => fake()->name(),
            'password' => static::$password ??= Hash::make('password'),
            'jenis_kendaraan' => fake()->randomElement(['mobil', 'motor']),
            'no_plat' => fake()->bothify('BP #### ??'),
            'role' => fake()->randomElement(['admin', 'pengguna']),
            'status' => fake()->randomElement(['aktif', 'nonAktif', 'ditolak']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
