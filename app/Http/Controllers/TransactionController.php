<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function merchant()
    {
        // dd($this->user->id);
        $data = DB::table('transactions')
            ->selectRaw('merchants.merchant_name, SUM(IF(DATE(transactions.created_at) = DATE(transactions.created_at), transactions.bill_total, 0)) AS omzet, DATE(transactions.created_at) as date')
            ->join('merchants', 'merchants.id', '=', 'transactions.merchant_id')
            ->where('merchants.user_id', '=', $this->user->id)
            ->groupBy('merchants.merchant_name')
            ->groupBy('date')
            ->orderBy('date')
            ->paginate(10);

        return response()->json($data, 200);
    }

    public function merchantShow($id)
    {
        if ($id != $this->user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = DB::table('transactions')
            ->selectRaw('merchants.merchant_name, SUM(IF(DATE(transactions.created_at) = DATE(transactions.created_at), transactions.bill_total, 0)) AS omzet, DATE(transactions.created_at) as date')
            ->join('merchants', 'merchants.id', '=', 'transactions.merchant_id')
            ->where('merchants.user_id', '=', $id)
            ->groupBy('merchants.merchant_name')
            ->groupBy('date')
            ->orderBy('date')
            ->paginate(10);

        return response()->json($data, 200);
    }

    public function outlet()
    {
        $data = DB::table('transactions')
            ->selectRaw('outlets.outlet_name, merchants.merchant_name, SUM(IF(DATE(transactions.created_at) = DATE(transactions.created_at), transactions.bill_total, 0)) AS omzet, DATE(transactions.created_at) as date')
            ->join('merchants', 'merchants.id', '=', 'transactions.merchant_id')
            ->join('outlets', 'outlets.id', '=', 'transactions.outlet_id')
            ->groupBy('merchants.merchant_name')
            ->groupBy('outlets.outlet_name')
            ->groupBy('date')
            ->orderBy('date')
            ->paginate(10);

        return response()->json($data, 200);
    }

    public function outletShow($id)
    {
        if ($id != $this->user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = DB::table('transactions')
            ->selectRaw('outlets.outlet_name, merchants.merchant_name, SUM(IF(DATE(transactions.created_at) = DATE(transactions.created_at), transactions.bill_total, 0)) AS omzet, DATE(transactions.created_at) as date')
            ->join('merchants', 'merchants.id', '=', 'transactions.merchant_id')
            ->join('outlets', 'outlets.id', '=', 'transactions.outlet_id')
            ->where('merchants.user_id', '=', $id)
            ->groupBy('merchants.merchant_name')
            ->groupBy('outlets.outlet_name')
            ->groupBy('date')
            ->orderBy('date')
            ->paginate(10);

        return response()->json($data, 200);
    }
}
