<?php

namespace App\Http\Controllers;

use App\Models\Auctions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuctionsController extends Controller
{
    public function API_GetAuctions(Request $request) {
        $validator = Validator::make($request->all(), [
            'orderBy' => 'required|in:start_time desc,start_time asc,title asc,title desc,current_price asc,current_price desc',
            'keyword' => 'nullable',
        ], [
            'orderBy.required' => 'Please select an option',
            'orderBy.in' => 'Invalid selection',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $orderBy = $request->orderBy;
        $orderBy = explode(' ', $orderBy);

        $keyword = $request->keyword;

        if ($orderBy[0] == 'current_price') {
            $auctions = Auctions::where('status', 'active')
                                ->where('title', 'like', '%'.$keyword.'%')
                                ->orWhere('description', 'like', '%'.$keyword.'%')
                                ->orderByRaw('CASE WHEN current_price = 0 THEN min_price ELSE current_price END '. $orderBy[1])
                                ->with('owner', 'winner')
                                ->get();
        } else {
            $auctions = Auctions::where('status', 'active')
                                ->where('title', 'like', '%'.$keyword.'%')
                                ->orWhere('description', 'like', '%'.$keyword.'%')
                                ->orderBy($orderBy[0], $orderBy[1])
                                ->with('owner', 'winner')
                                ->get();
        }

        foreach ($auctions as $auction) {
            $auction->start_time = date('H:i:s d/m/Y', strtotime($auction->start_time));
            $auction->belongToMe = auth()->check() && ($auction->owner_id == auth()->user()->id) ? true : false;
            $auction->winning = auth()->check() && ($auction->winner_id == auth()->user()->id) ? true : false;
        }

        return response()->json([
            'success' => true,
            'message' => 'Auctions retrieved',
            'data' => $auctions,
        ]);
    }

    public function API_Bidding(Request $request) {
        $validator = Validator::make($request->all(), [
            'auction_id' => 'required|exists:auctions,id',
            'bid' => 'required|integer|min:1',
        ], [
            'auction_id.required' => 'Please select an auction',
            'auction_id.exists' => 'Invalid auction',
            'bid.required' => 'Please enter a bid price',
            'bid.integer' => 'Invalid bid price',
            'bid.min' => 'Bid price must be at least 1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $auction = Auctions::find($request->auction_id);

        if ($auction->status != 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Auction is not active',
            ]);
        }

        if ($auction->owner_id == auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cant bid your own auctions',
            ]);
        }

        if ($auction->winner_id == auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Current auction already bidded with $' . $auction->current_price,
            ]);
        }

        if ($auction->current_price >= $request->bid) {
            return response()->json([
                'success' => false,
                'message' => 'You must bid greater than $' . $auction->current_price,
            ]);
        }

        if ($auction->min_price > $request->bid) {
            return response()->json([
                'success' => false,
                'message' => 'You must bid greater than $' . $auction->min_price,
            ]);
        }

        if ($request->bid > auth()->user()->balance_money) {
            return response()->json([
                'success' => false,
                'message' => 'You dont have enough funds to bid this auction',
            ]);
        }

        if ($auction->winner_id != null) {
            $winner_old = User::find($auction->winner_id);
            $winner_old->reserved_money -= $auction->current_price;
            $winner_old->balance_money += $auction->current_price;
            $winner_old->save();
        }

        $auction->current_price = $request->bid;
        $auction->winner_id = auth()->user()->id;
        $auction->save();

        $user = User::find(auth()->user()->id);
        $user->balance_money -= $request->bid;
        $user->reserved_money += $request->bid;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Auction ' . $auction->title . ' bidded with $' . $auction->current_price,
            'data' => [
                'balance_money' => $user->balance_money,
                'reserved_money' => $user->reserved_money,
            ],
        ]);
    }

    public function myAuctions() {
        $view = view('myAuctions.view');

        return $this->loadLayout($view, 'My Auctions');
    }

    public function API_GetMyAuctions(Request $request) {
        $validator = Validator::make($request->all(), [
            'tab' => 'required|in:all,active,closed,won,bidded',
        ], [
            'tab.required' => 'Please select an option',
            'tab.in' => 'Invalid selection',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $auctions = [];

        if ($request->tab == 'all') {
            $auctions = Auctions::where('owner_id', auth()->user()->id)
                                ->orWhere('winner_id', auth()->user()->id)
                                ->orderBy('updated_at', 'desc')
                                ->with('owner', 'winner')
                                ->get();
        }

        if ($request->tab == 'active') {
            $auctions = Auctions::where('owner_id', auth()->user()->id)
                                ->where('status', 'active')
                                ->orderBy('updated_at', 'desc')
                                ->with('owner', 'winner')
                                ->get();
        }

        if ($request->tab == 'closed') {
            $auctions = Auctions::where('owner_id', auth()->user()->id)
                                ->where('status', 'ended')
                                ->orderBy('updated_at', 'desc')
                                ->with('owner', 'winner')
                                ->get();
        }

        if ($request->tab == 'won') {
            $auctions = Auctions::where('winner_id', auth()->user()->id)
                                ->where('status', 'ended')
                                ->orderBy('updated_at', 'desc')
                                ->with('owner', 'winner')
                                ->get();
        }

        if ($request->tab == 'bidded') {
            $auctions = Auctions::where('winner_id', auth()->user()->id)
                                ->where('status', 'active')
                                ->orderBy('updated_at', 'desc')
                                ->with('owner', 'winner')
                                ->get();
        }

        foreach ($auctions as $auction) {
            $auction->start_time = date('H:i:s d/m/Y', strtotime($auction->start_time));
            $auction->updated = date('H:i:s d/m/Y', strtotime($auction->updated_at));
            $auction->belongToMe = auth()->check() && ($auction->owner_id == auth()->user()->id) ? true : false;
            $auction->winning = auth()->check() && ($auction->winner_id == auth()->user()->id) ? true : false;
        }

        return response()->json([
            'success' => true,
            'message' => 'Auctions retrieved',
            'data' => $auctions,
        ]);
    }

    public function API_CloseAuction(Request $request) {
        $validator = Validator::make($request->all(), [
            'auction_id' => 'required|exists:auctions,id',
        ], [
            'auction_id.required' => 'Please select an auction',
            'auction_id.exists' => 'Invalid auction',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $auction = Auctions::find($request->auction_id);

        if ($auction->status != 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Auction is not active',
            ]);
        }

        if ($auction->owner_id != auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not the owner of this auction',
            ]);
        }

        $auction->status = 'ended';
        $auction->save();

        if ($auction->winner_id == null) {
            return response()->json([
                'success' => true,
                'message' => 'Auction ' . $auction->title . ' closed',
            ]);
        }

        $winner = User::find($auction->winner_id);
        $winner->reserved_money -= $auction->current_price;
        $winner->save();

        $user = User::find(auth()->user()->id);
        $user->balance_money += $auction->current_price;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Auction ' . $auction->title . ' closed',
            'data' => [
                'balance_money' => $user->balance_money,
                'reserved_money' => $user->reserved_money,
            ]
        ]);
    }

    public function createAuction() {
        $view = view('myAuctions.create');

        return $this->loadLayout($view, 'Create Auction');
    }

    public function API_CreateAuction(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5|max:255',
            'description' => 'required',
            'min_price' => 'required|integer|min:1',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:51200',
        ], [
            'title.required' => 'Please enter a name',
            'title.min' => 'Name must be at least 5 characters',
            'title.max' => 'Name must be at most 255 characters',
            'description.required' => 'Please enter a description',
            'min_price.required' => 'Please enter a minimum price',
            'min_price.integer' => 'Invalid minimum price',
            'min_price.min' => 'Minimum price must be at least 1',
            'image.required' => 'Please select an image',
            'image.image' => 'Invalid image',
            'image.mimes' => 'Invalid image',
            'image.max' => 'Image must be at most 50MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $image = $request->file('image');
        $imageName = time() . '_' . rand(100000, 999999) . '.' . $image->extension();
        $image->move(public_path('Images'), $imageName);

        $auction = new Auctions();
        $auction->title = $request->title;
        $auction->description = $request->description;
        $auction->min_price = $request->min_price;
        $auction->image = 'Images/' . $imageName;
        $auction->owner_id = auth()->user()->id;
        $auction->start_time = date('Y-m-d H:i:s');
        $auction->save();

        return response()->json([
            'success' => true,
            'message' => 'Auction ' . $auction->title . ' created',
        ]);
    }
}
