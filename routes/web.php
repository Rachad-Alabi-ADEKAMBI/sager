<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\Notification;

Route::get('/', function () {
    return view('pages/front/home');
});

require __DIR__.'/auth.php';

Route::get('/home', function () {
    return view('pages/front/home');
})->name('home');


/*admin routes*/

Route::get('/dashboardAdmin', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login'); // Redirige vers login
    }

      $products = Product::all();
      $sales = Sale::all();
      $totalCA = Sale::sum('total'); // somme de la colonne 'total'
     $notifications = Notification::latest()->take(5)->get();

    return view('pages/back/admin/dashboardAdmin', compact('products', 'sales', 'totalCA', 'notifications'));
})->name('dashboardAdmin');


Route::get('/products', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    $products = Product::orderBy('id', 'desc')->get();
    return view('pages/back/admin/products');
})->name('products');


Route::get('/stocks', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    $products = Product::orderBy('id', 'desc')->get();
    return view('pages/back/admin/stocks', compact('products'));
})->name('stocks');


Route::post('/products', [ProductController::class, 'store'])
    ->middleware('auth')
    ->name('products.store');

    Route::middleware('auth')->post('/products/{id}', [ProductController::class, 'update'])->name('products.update');



Route::get('/sellers', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
    $users = User::all();
    return view('pages/back/admin/sellers', compact('users'));
})->name('sellers');

Route::get('/sales', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
   $sales = Sale::with('products')->orderBy('created_at', 'desc')->get();
    return view('pages/back/admin/sales', compact('sales'));
})->name('sales');

Route::get('/settingsAdmin', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
    return view('pages/back/admin/settingsAdmin');
})->name('settingsAdmin');


Route::post('/users', [RegisteredUserController::class, 'store'])->name('users.store');


/*end admin routes*/



/*routes for seller*/
    Route::get('/sale', function () {
    if (!Auth::check() || Auth::user()->role !== 'seller') {
        return redirect()->route('login');
    }
      $products = Product::all();

      //get all the sales_id of the current 
    return view('pages/back/seller/sale', compact('products'));
})->name('sale');


Route::post('/sale', function (Request $request) {
    // Validation des données
    $validated = $request->validate([
        'seller_name' => 'required|string',
        'buyer_name' => 'required|string',
        'buyer_phone' => 'required|string',
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
        'products.*.price' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        // Calcul du total
        $total = collect($validated['products'])->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });

        // Création de la vente
        $sale = Sale::create([
            'seller_name' => $validated['seller_name'],
            'buyer_name' => $validated['buyer_name'],
            'buyer_phone' => $validated['buyer_phone'],
            'total' => $total,
        ]);

        $details = [];

        foreach ($validated['products'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            // Vérifie le stock
            if ($product->quantity < $item['quantity']) {
                throw new \Exception("Stock insuffisant pour le produit: {$product->name}");
            }

            // Création de la ligne de vente
            $saleProduct = SaleProduct::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // Mise à jour du stock
            $product->decrement('quantity', $item['quantity']);

            $details[] = [
                'product' => $product->name,
                'quantity_sold' => $item['quantity'],
                'price' => $item['price']
            ];
        }

        DB::commit();

  

        // Ajoute l'opération dans la table des notifications
        Notification::create([
            'description' => 'Facture N°' . $sale->id . '/' . now()->format('m') . '/' . now()->format('y') . '/FR-N pour ' .
                            $sale->buyer_name . ' par ' . $sale->seller_name . '. Total : ' . $total . ' FCFA.',
        ]);

        return response()->json([
            'message' => 'Vente enregistrée avec succès.',
            'sale_id' => $sale->id,
            'total' => $total,
            'products' => $details
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Échec de l\'enregistrement de la vente.',
            'message' => $e->getMessage()
        ], 500);
    }
});



 Route::get('/dashboard', function () {
    if (!Auth::check() || Auth::user()->role !== 'seller') {
        return redirect()->route('login');
    }

     $sales = Sale::where('seller_name', Auth::user()->name)->orderBy('created_at', 'desc')->get();    

    $salesNotifications = Sale::where('seller_name', Auth::user()->name)
             ->latest()
             ->take(5)
             ->get();

    $totalCA = \App\Models\Sale::where('seller_name', auth()->user()->name)->sum('total');

    return view('pages/back/seller/dashboard', compact('sales', 'salesNotifications', 'totalCA'));
})->name('dashboard');


Route::get('/newInvoice', function (Request $request) {
    $sale = Sale::with('products')->findOrFail($request->sale_id);

    return view('pages/back/seller/newInvoice', [
        'sale' => $sale,
    ]);
});

   Route::get('/settings', function () {
    if (!Auth::check() || Auth::user()->role !== 'seller') {
        return redirect()->route('login');
    }
    return view('pages/back/seller/settings');
})->name('settings');

/* end seller routes*/


/* api routes */
     Route::get('/productsList', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $products = Product::all();
    return response()->json($products);
})->name('productsList');


  Route::get('/product/{id}', function ($id) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $product = Product::find($id);
    if ($product) {
        return response()->json($product->toArray());
    } else {
        return response()->json(['error' => 'Product not found'], 404);
    }
})->name('product');

//user sales
Route::get('/userSales', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $sales = Sale::with('products')->where('seller_name', Auth::user()->name)->get();
    return response()->json($sales);
})->name('userSales');


     Route::get('/salesList', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    $sales = Sale::all();
    return response()->json($sales);
})->name('salesList');


 Route::get('/saleDetails/{saleId}', function ($saleId) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

$sale = Sale::with('products')->findOrFail($saleId);



    return response()->json($sale);
})->name('sale.details');



/* end api routes*/








Route::post('/logout', function () {
    Auth::logout();
    return redirect('/home');
})->name('logout');


Route::get('/login', function () {
    return view('pages/front/login');
})->middleware('guest')->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

    Route::get('/reset_password', function () {
    return view('pages/front/reset_password');
})->middleware('guest')->name('reset_password');



Route::post('/sales/store', [SaleController::class, 'store'])->name('sales.store');
