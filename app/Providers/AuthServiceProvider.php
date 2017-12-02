<?php

namespace App\Providers;

use App\User;
use App\Buyer;
use App\Seller;
use App\Product;
use Carbon\Carbon;
use App\Transaction;
use App\Policies\UserPolicy;
use App\Policies\BuyerPolicy;
use App\Policies\SellerPolicy;
use Laravel\Passport\Passport;
use App\policies\ProductPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Buyer::class =>BuyerPolicy::class,
        Seller::class=>SellerPolicy::class,
        User::class=>UserPolicy::class,
        Transaction::class=>TransactionPolicy::class,
        Product::class=>ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        Gate::define('admin-action', function($user)
        {
            return $user->isAdmin();
        });

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::enableImplicitGrant();

            //scopes
        Passport::tokensCan([
            'purchase-product' => 'Create a new transaction for a specific product',
            'manage-products'=> 'create, read, and delete products(CRUD)',
            'manage-account' =>'Read your account data, id , name, email, if verified, and if admin(cannont read password). Modify ypur account data
            (email, and password) Cannot delete your account',
            'read-general' => 'Read general information like purchasing categries, purchased, products, selling product, selling categories, your transactions
            (purchase and sales)'
        ]);
    }
}
