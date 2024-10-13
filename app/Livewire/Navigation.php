<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\Product;

class Navigation extends Component
{
    public $cartCount = 0;
    public $notifications = [];
    public $unreadNotificationsCount = 0;
    public $search = '';
    public $searchResults = [];

    protected $listeners = ['cartUpdated' => 'updateCartCount'];

    public function mount()
    {
        $this->updateCartCount();
        $this->loadNotifications();
    }

    public function updateCartCount()
    {
        $this->cartCount = count(session('cart', []));
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);
        if(isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            $this->updateCartCount();
            $this->dispatch('swal:success', [
                'title' => 'Success!',
                'text' => 'Item removed from cart successfully!',
                'icon' => 'success',
                'timer' => 3000,
            ]);
        }
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function loadNotifications()
    {
        if (Auth::check()) {
            $this->notifications = Auth::user()->notifications()->latest()->take(5)->get();
            $this->unreadNotificationsCount = Auth::user()->unreadNotifications()->count();
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === Auth::id()) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
            $this->loadNotifications();
        }
    }

    public function clearNotification($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === Auth::id()) {
            $notification->delete();
            $this->loadNotifications();
            $this->dispatch('showAlert', [
                ['type' => 'success', 'message' => 'Notification cleared successfully.']
            ]);
        }
    }

    public function clearAllNotifications()
    {
        if (Auth::check()) {
            Auth::user()->notifications()->delete();
            $this->loadNotifications();
            $this->dispatch('showAlert', [
                ['type' => 'success', 'message' => 'All notifications cleared successfully.']
            ]);
        }
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 3) {
            $this->searchResults = Product::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->take(10)  // Increased to show more results
                ->get(['id', 'name', 'slug', 'price', 'images']);  // Select only necessary fields
        } else {
            $this->searchResults = [];
        }
    }

    public function render()
    {
        return view('livewire.navigation');
    }
}
