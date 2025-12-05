<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Product;
use Carbon\Carbon; 

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     return view('home');
    // }

     public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $baseInvoiceQuery = Invoice::whereMonth('date', $currentMonth)
                                   ->whereYear('date', $currentYear);

        // 1. Total Pagado (Ingresos efectivos)
        // $totalPaid = Invoice::where('is_cancelled', '0')
        //                     ->sum('total_amount');
        $totalPaid = (clone $baseInvoiceQuery)
                            ->where('is_cancelled', '0') // Facturas no canceladas
                            ->sum('total_amount');

        // 2. Total decuentos otorgados
        $totalDiscount = (clone $baseInvoiceQuery)
                                ->where('is_cancelled', '0') // Facturas no canceladas
                                ->sum('discount_amount');
        
        $totalProducts=Product::count('id');

        //$totalInvoices=Invoice::count('id');
        $totalInvoices = (clone $baseInvoiceQuery)
                                ->count('id');
        

        return view('home', compact(
            'totalPaid', 
            'totalDiscount',
            'totalProducts',
            'totalInvoices'
            
        ));
    }
}
