<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing users to avoid duplicates
        User::where('email', 'product@admin.com')->delete();
        User::where('email', 'travel@admin.com')->delete();
        User::where('email', 'customer@example.com')->delete();

        // ========== PRODUCT ADMIN ==========
        $productAdmin = User::create([
            'name' => 'Product Admin',
            'email' => 'product@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'), // Proper bcrypt hash
            'type' => 'product',
            'remember_token' => Str::random(10),
        ]);
        
        $this->command->info("Product Admin created:");
        $this->command->info("Email: product@admin.com");
        $this->command->info("Password: admin123");
        $this->command->info("Password Hash: " . $productAdmin->password);

        // ========== TRAVEL ADMIN ==========
        $travelAdmin = User::create([
            'name' => 'Travel Admin',
            'email' => 'travel@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('travel123'), // Proper bcrypt hash
            'type' => 'travel',
            'remember_token' => Str::random(10),
        ]);
        
        $this->command->info("\nTravel Admin created:");
        $this->command->info("Email: travel@admin.com");
        $this->command->info("Password: travel123");
        $this->command->info("Password Hash: " . $travelAdmin->password);

        // ========== CUSTOMER ==========
        $customer = User::create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('customer123'), // Proper bcrypt hash
            'type' => 'customer',
            'remember_token' => Str::random(10),
        ]);

        // ========== ADDITIONAL TEST USERS ==========
        User::create([
            'name' => 'Test Product Admin',
            'email' => 'testproduct@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test123'),
            'type' => 'product',
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Test Travel Admin',
            'email' => 'testtravel@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test123'),
            'type' => 'travel',
            'remember_token' => Str::random(10),
        ]);

        $this->command->info("\n✓ All users created successfully!");
        $this->command->info("------------------------------");
        $this->command->info("Login Credentials:");
        $this->command->info("Product Admin: product@admin.com / admin123");
        $this->command->info("Travel Admin: travel@admin.com / travel123");
        $this->command->info("Customer: customer@example.com / customer123");
        $this->command->info("Test Product Admin: testproduct@admin.com / test123");
        $this->command->info("Test Travel Admin: testtravel@admin.com / test123");
    }
}