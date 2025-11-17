<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SiteUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = Site::with('merchant')->take(5)->get();

        if ($sites->isEmpty()) {
            $this->command->warn('No sites found. Run the SiteSeeder before seeding site users.');
            return;
        }

        $roles = Role::select('id', 'name')->whereNotIn('name', [Constants::SUPERADMIN])->get();

        foreach ($sites as $index => $site) {
            $user = new User();
            $user->first_name = 'Site';
            $user->last_name = 'Manager ' . ($index + 1);
            $user->email = 'site.manager' . ($index + 1) . '@uzimate.com';
            $user->phone = '0778900' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            $user->about = 'Demo site manager account';
            $user->password = Hash::make('test123');
            $user->remember_token = Str::random(10);
            $user->email_verified_at = now();
            $user->save();

            if ($roles->isNotEmpty()) {
                $randomRole = $roles->random();
                $user->assignRole($randomRole->name);
            }

            SiteUser::create([
                'merchant_id' => $site->merchant_id,
                'site_id' => $site->id,
                'user_id' => $user->id,
                'status' => true,
            ]);
        }
    }
}

