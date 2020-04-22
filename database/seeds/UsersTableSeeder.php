<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $app_seed_with_admin = env('APP_SEED_WITH_ADMIN', true);
        if ($app_seed_with_admin) {
            $this->createAdminUser();
        }
        factory(App\Models\User::class, 20)->create();
    }

    private function createAdminUser()
    {
        try {
            $user = User::create([
                'first_name' => env('APP_ADMIN_FIRST_NAME', 'Admin'),
                'last_name' => env('APP_ADMIN_LAST_NAME', null),
                'email' => env('APP_ADMIN_EMAIL', 'admin@example.com'),
                'password' => Hash::make(env('APP_ADMIN_PASSWORD', 'admin123'))
            ]);
        } catch (Exception $error) {

            $this->command->error('Admin user could not be created. Please check if you have env variable for admin user');
        }

        if (isset($user)) {
            $user->allowToDoEverything();
        }
    }
}
