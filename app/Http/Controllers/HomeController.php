<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderItem;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        dd('asd');
        return view('home');
    }

    public function checkout()
    {
        return view('checkout');
    }
    public function paymentProcess(Request $request){
        try {
               $str = $request->get('stripeToken');
                $total = $request->get('total');
                $cart = $request->get('cart');
                $order = Order::create([
                    'user_id' => 1,
                    'total_amount' => $total,
                    'status' => 'pending'
                ]);
                foreach ($cart as $key => $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'price' => $cartItem['price'],
                        'quantity' => $cartItem['qty'],
                        'product_id' => $cartItem['id'],
                    ]);
                }
        
            $stripe = new \Stripe\StripeClient(config('services.stripe.stripe_sr_key'));
            $stripe->charges->create([
              'amount' => $total * 100,
              'currency' => 'usd',
              'source' => $str['id'],
              'description' => 'Order ID : ' . $order->id,
            ]);

            $order->status = 'completed';
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'payment has been completed!'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
     
    }
}
