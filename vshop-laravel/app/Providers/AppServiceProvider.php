<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\Wishlist;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot()
{
    \Illuminate\Pagination\Paginator::useBootstrapFive();

    View::composer('*', function ($view) {

        $orderCount = 0;
        $cartCount = 0;
        $wishlistCount = 0;

        if (Auth::guard('customer')->check()) {
            $customerId = Auth::guard('customer')->id();

            $orderCount = Order::where('customer_id', $customerId)->count();
            $cartCount = CartItem::where('customer_id', $customerId)->count();
            $wishlistCount = Wishlist::where('customer_id', $customerId)->count();
        }

        $view->with(compact('orderCount', 'cartCount', 'wishlistCount'));
    });
}
}
