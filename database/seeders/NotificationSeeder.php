<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Filament\Notifications\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Create a welcome notification
            Notification::make()
                ->title('Welcome to our platform!')
                ->body('We\'re glad to have you here. Explore our features and get started.')
                ->icon('heroicon-o-bell')
                ->sendToDatabase($user);

            // Create a sample task notification
            Notification::make()
                ->title('New task assigned')
                ->body('You have been assigned a new task. Please check your dashboard.')
                ->icon('heroicon-o-clipboard-document-list')
                ->sendToDatabase($user);
        }
    }
}
