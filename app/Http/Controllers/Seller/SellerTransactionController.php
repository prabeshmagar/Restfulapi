<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\APiController;

class SellerTransactionController extends ApiController
{
    public function __construct()
    {
        $this->middleware('scope:read-general')->only('index');
        $this->middleware('can:view,seller')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $transactions = $seller->products()
                    ->whereHas('transactions')
                    ->with('transactions')
                     ->get()
                     ->pluck('transactions')
                     ->collapse();
        return $this->showAll($transactions);
    }

  
}
