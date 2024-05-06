<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    public function transactions() {
        $view = view('transactions');

        return $this->loadLayout($view, 'Transactions');
    }

    public function API_Transactions(Request $request) {
        $validator = Validator::make($request->all(), [
            'money' => 'required|integer|not_in:0',
        ], [
            'money.required' => 'Please enter an amount',
            'money.integer' => 'Please enter a valid amount',
            'money.not_in' => 'Please enter a valid amount',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::find(auth()->user()->id);

        if ($request->balance_money < 0 && $user->balance_money < abs($request->money)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot withdraw more than current balance',
            ]);
        }

        $user->balance_money += $request->money;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $request->money > 0 ? 'Successfully deposited $' . $request->money : 'Successfully withdrawn $' . abs($request->money),
            'data' => [
                'balance_money' => $user->balance_money,
                'reserved_money' => $user->reserve_money,
            ],
        ]);
    }
}
