<?php

namespace App\Http\Controllers\Product;

use App\User;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Transformers\TransactionTransformer;


class ProductBuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:' .TransactionTransformer::class)->only(['store']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rule =[
                'quantity' => 'required|integer|min:1'
        ];

        $this->validate($request, $rule);

        if($buyer->id == $product->seler_id)
        {
            return $this->errorResponse('The buyer must be different from the seller', 409);
        }

        if(!$buyer->isVerified())
        {
            return $this->errorResponse('The buyer must be a verified',409);
        }

        if(!$product->seller->isVerified())
        {
            return $this->errorResponse('The seller must be a verified',409);
        }

        if(!$product->isAvailable())
        {
            return $this->errorResponse('The product is not available',409);
        }

        if($product->quantity < $request->quantity)
        {
            return $this->errorResponse('The product doesnot have enough units fot this transaction',409);
        }

        return DB::transaction(function() use($request, $product, $buyer)
        {
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity'=>$request->quantity,
                'buyer_id'=>$buyer->id,
                'product_id'=>$product->id,
            ]);
            return $this->showOne($transaction, 201);
        });
    }

   
}
