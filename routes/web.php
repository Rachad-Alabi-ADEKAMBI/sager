<?php

use Illuminate\Support\Facades\{Route, Auth, DB};
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\{
    Auth\RegisteredUserController,
    Auth\AuthenticatedSessionController,
    ProductController,
    SaleController,
    StockController,
    SettingsController,
    ProformaController,
    RentabilityController,
    DepositController,
    StockDepositController,
    ClientController,
    ClaimController,
    ClaimsPaymentController,
};

use App\Models\{
    Product,
    User,
    Sale,
    Deposit,
    SaleProduct,
    ProformaProduct,
    Proforma,
    Notification,
    Stock,
    Client,
    Claim
};



Route::get('/', function () {
    return view('pages/front/home');
});

require __DIR__ . '/auth.php';

Route::get('/home', function () {
    return view('pages/front/home');
})->name('home');


/*admin routes*/
Route::get('/dashboardAdmin', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    $products = Product::all();

    // Ventes "done" uniquement
    $sales = Sale::where('status', 'done')->get();
    $totalCA = Sale::where('status', 'done')->sum('total');

    $today = Carbon::today();

    $salesCountToday = Sale::where('status', 'done')
        ->whereDate('created_at', $today)
        ->count();

    $salesAmountToday = Sale::where('status', 'done')
        ->whereDate('created_at', $today)
        ->sum('total');

    $notifications = Notification::latest()->take(5)->get();

    return view('pages/back/admin/dashboardAdmin', compact(
        'products',
        'sales',
        'totalCA',
        'salesCountToday',
        'salesAmountToday',
        'notifications'
    ));
})->name('dashboardAdmin');



//page produits
Route::get('/products', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    $products = Product::orderBy('id', 'desc')->get();
    return view('pages/back/admin/products');
})->name('products');


//page consignations (deposits)
Route::get('/deposits', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    $products = Product::orderBy('id', 'desc')->get();
    return view('pages/back/admin/deposits');
})->name('deposits');


Route::get('/stocks', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    return view('pages/back/admin/stocks');
})->name('stocks');


//delete a product
Route::post('/products/{id}/delete', [ProductController::class, 'destroy'])
    ->middleware('auth')
    ->name('products.destroy');

// update product prices
Route::post('/products/{id}/update-prices', [ProductController::class, 'updatePrices'])
    ->middleware('auth')
    ->name('products.updatePrices');

Route::get('/sellers', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
    $users = User::all();
    return view('pages/back/admin/sellers', compact('users'));
})->name('sellers');

Route::get('/rentability', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
    $users = User::all();
    return view('pages/back/admin/rentability');
})->name('rentability');

Route::get('/sales', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
    $sales = Sale::with('products')->orderBy('created_at', 'desc')->get();
    return view('pages/back/admin/sales', compact('sales'));
})->name('sales');



//route clients
Route::get('/clients', [ClientController::class, 'index'])->name('clients');
Route::get('/clientslist', [ClientController::class, 'clientsList'])->name('clients.list');
Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
Route::post('/clients/{id}/delete', [ClientController::class, 'delete']);

//route claims
Route::post('/claims/add', [ClaimController::class, 'add'])->name('claims.add');
Route::get('/claimslist', [ClaimController::class, 'list'])->name('claims.list');
Route::post('/claims/{id}/delete', [ClaimController::class, 'delete']);
Route::get('/claims', [ClaimController::class, 'index'])->name('claims');

//route claims payments
Route::post('/claims/pay', [ClaimsPaymentController::class, 'addPayment'])->name('claims.pay');
Route::get('/claims/payments', [ClaimsPaymentController::class, 'list'])->name('claims.payments.list');


Route::get('/proformas', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }
    return view('pages/back/admin/proformas');
})->name('proformas');

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

Route::get('/proforma', function () {
    if (!Auth::check() || Auth::user()->role !== 'seller') {
        return redirect()->route('login');
    }
    $products = Product::all();

    //get all the sales_id of the current 
    return view('pages/back/seller/proforma', compact('products'));
})->name('proforma');

Route::middleware(['auth'])->group(function () {

    // Create proforma invoice
    Route::post('/proforma', function (Request $request) {

        $validated = $request->validate([
            'seller_name' => 'required|string',
            'buyer_name' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calcul du total
            $total = collect($validated['products'])->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });

            // Création de la proforma
            $proforma = Proforma::create([
                'seller_name' => $validated['seller_name'],
                'buyer_name' => $validated['buyer_name'],
                'total' => $total,
                'status' => 'draft',
            ]);

            $details = [];

            // Enregistrement des produits liés
            foreach ($validated['products'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                ProformaProduct::create([
                    'proforma_id' => $proforma->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $details[] = [
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ];
            }

            // Notification
            Notification::create([
                'description' => 'Proforma N°' . $proforma->id . ' pour ' .
                    $proforma->buyer_name . ' par ' . $proforma->seller_name .
                    '. Total : ' . $total . ' FCFA.',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Proforma enregistrée avec succès.',
                'proforma_id' => $proforma->id,
                'total' => $total,
                'products' => $details
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Échec de l\'enregistrement de la proforma.',
                'message' => $e->getMessage()
            ], 500);
        }
    });

    // Liste de toutes les proformas
    Route::get('/proformasApiList', function (Request $request) {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $proformas = Proforma::with('products')
            ->orderBy('id', 'desc') // Tri par id décroissant
            ->get();

        return response()->json($proformas);
    })->name('proformasApiList');

    // Liste des proformas d'un vendeur connecté
    Route::get('/proformaApiBySellerList', function (Request $request) {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $proformas = Proforma::with('products.product') // charger aussi la relation 'product'
            ->where('seller_name', $user->name)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($proformas);
    })->name('proformaApiBySellerList');



    // Optional: cancel a proforma
    Route::post('/proformas/{id}/cancel', [ProformaController::class, 'cancel'])
        ->name('proformas.cancel');
});



//faire une vente
Route::post('/sale', [SaleController::class, 'store']);


// annuler une vente
Route::post('/invoices/{id}/cancel', [SaleController::class, 'cancel'])
    ->name('invoices.cancel')
    ->middleware('auth');



//tableau de bord
Route::get('/dashboard', function () {
    if (!Auth::check() || Auth::user()->role !== 'seller') {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $today = Carbon::today();

    // Toutes les ventes "done"
    $sales = Sale::where('seller_name', $user->name)
        ->where('status', 'done')
        ->orderBy('created_at', 'desc')
        ->get();

    // Nombre de ventes "done" aujourd'hui
    $salesCountToday = Sale::where('seller_name', $user->name)
        ->where('status', 'done')
        ->whereDate('created_at', $today)
        ->count();

    // Montant total des ventes "done" aujourd'hui
    $salesAmountToday = Sale::where('seller_name', $user->name)
        ->where('status', 'done')
        ->whereDate('created_at', $today)
        ->sum('total');

    // Dernières notifications de ventes "done"
    $salesNotifications = Sale::where('seller_name', $user->name)
        ->where('status', 'done')
        ->latest()
        ->take(20)
        ->get();

    // Chiffre d'affaires total des ventes "done"
    $totalCA = Sale::where('seller_name', $user->name)
        ->where('status', 'done')
        ->sum('total');

    return view('pages/back/seller/dashboard', compact(
        'sales',
        'salesCountToday',
        'salesAmountToday',
        'salesNotifications',
        'totalCA'
    ));
})->name('dashboard');



Route::get('/newInvoice/{sale_id}', function ($sale_id) {
    $sale = Sale::with('products')->findOrFail($sale_id);

    return view('pages/back/seller/newInvoice', [
        'sale' => $sale,
    ]);
});

Route::get('/newProforma/{proforma_id}', function ($proforma_id) {
    $proforma = Proforma::with('products')->findOrFail($proforma_id);

    return view('pages.back.seller.newProforma', [
        'sale' => $proforma, // correspond à la variable utilisée dans le Blade
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
//get products list
Route::get('/productsList', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $products = Product::orderBy('id', 'desc')->get(); // tri décroissant par id
    return response()->json($products);
})->name('productsList');


//add a product
Route::post('/products', function (\Illuminate\Http\Request $request) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return response()->json(['error' => 'Accès interdit'], 403);
    }

    return app(\App\Http\Controllers\ProductController::class)->store($request);
})->name('products.store');


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

//Route pour avoir les sellers
Route::get('/sellersList', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $sellers = User::where('role', '!=', 'admin')->get();
    return response()->json($sellers);
})->name('sellersList');


Route::get('/seller/{id}', function ($id) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $seller = User::find($id);
    if ($seller) {
        return response()->json($seller->toArray());
    } else {
        return response()->json(['error' => 'Seller not found'], 404);
    }
})->name('seller');


// Liste de tous les stocks
Route::get('/stocksList', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $stocks = Stock::all();
    return response()->json($stocks);
})->name('stocksList');

// Détails d’un stock par ID

Route::get('/stock/{id}', function ($id) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    // Recherche du stock lié à l'ID du produit
    $stock = Stock::where('product_id', $id)->orderBy('id', 'desc')->get();

    if ($stock) {
        return response()->json($stock->toArray());
    } else {
        return response()->json(['error' => 'Stock not found'], 404);
    }
})->name('stock');

//mise a jour du stock
Route::post('/products/{id}/stock', [ProductController::class, 'updateStock']);
Route::post('/products/{id}/stock/remove', [ProductController::class, 'removeStock']);

//seller sales
Route::get('/sellerSalesList', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    $sales = Sale::with('products')
        ->where('seller_name', $user->name)
        ->orderBy('id', 'desc')  // Tri par id décroissant
        ->get();

    return response()->json($sales);
})->name('sellerSalesList');


Route::get('/salesList', function () {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    $sales = Sale::orderBy('id', 'desc')->get();
    return response()->json($sales);
})->name('salesList');


Route::get('/saleDetails/{saleId}', function ($saleId) {
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    $sale = Sale::with('products')->findOrFail($saleId);
    return response()->json($sale);
})->name('sale.details');


Route::get('/proformaDetails/{proformaId}', function ($proformaId) {
    // Vérifie que l'utilisateur est connecté et admin
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return redirect()->route('login');
    }

    // Charge la proforma avec ses produits et les informations du produit lié
    $proforma = Proforma::with('products.product')->findOrFail($proformaId);

    // Retourne en JSON
    return response()->json($proforma);
})->name('proforma.details');



Route::post('/stocks', [StockController::class, 'store']);
//all the sales of an user based on his id
Route::get('/sellers/{id}/sales', function ($id) {
    // Vérifier si l'utilisateur est authentifié pour accéder à cette route
    if (!Auth::check()) {
        return response()->json(['error' => 'Non authentifié.'], 401);
    }

    // Rechercher le vendeur par son ID
    $seller = User::find($id);

    // Si le vendeur n'existe pas, renvoyer une erreur 404
    if (!$seller) {
        return response()->json(['error' => 'Vendeur non trouvé.'], 404);
    }

    // Récupérer les ventes du vendeur en utilisant son nom
    $sales = Sale::where('seller_name', $seller->name)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($sales);
});


//bannir un vendeur 
Route::post('/sellers/{id}/ban', function (Request $request, $id) {
    // 1. Vérification de l'authentification et du rôle
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        return response()->json(['error' => 'Accès non autorisé.'], 403);
    }

    // 2. Validation de la requête
    $request->validate([
        'reason' => 'required|string|min:10',
    ]);

    // 3. Recherche de l'utilisateur à bannir
    $seller = User::find($id);

    if (!$seller) {
        return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
    }

    // 4. Mettre à jour l'utilisateur pour le "bannir"
    // Vous devez avoir un champ `is_banned` ou similaire dans votre table `users`.
    // J'utilise ici un exemple de champ `status` et un champ `ban_reason`.
    $seller->status = 'banned'; // Assurez-vous que ce champ existe dans votre modèle User.
    $seller->ban_reason = $request->input('reason');
    $seller->save();

    // 5. Déconnecter l'utilisateur banni si nécessaire
    // Si l'utilisateur banni est actuellement connecté, cela le déconnecte.
    if ($seller->id === Auth::id()) {
        Auth::logout();
    }

    return response()->json(['message' => 'Utilisateur banni avec succès.'], 200);
});


//accounting datas
Route::get('/accounting/{id}', [ProductController::class, 'getAccountingData']);


//rentability data
Route::get('/rentabilityApi', [RentabilityController::class, 'getRentability'])
    ->name('api.rentability');


Route::get('/rentabilityApi/daily', [RentabilityController::class, 'getDailyRentability'])
    ->name('api.rentabilityApi.daily');


/* end api routes*/

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/home');
})->name('logout');


Route::get('/login', function () {
    if (Auth::check()) {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    return view('pages/front/login');
})->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');

Route::get('/reset_password', function () {
    return view('pages/front/reset_password');
})->middleware('guest')->name('reset_password');


//nouvelle vente
/*
Route::post('/sales/store', [SaleController::class, 'store'])->name('sales.store');
*/

//liste des consignations d'un prdoduit
Route::get('/deposits/{product_id}/history', [StockDepositController::class, 'history']);

//ajouter du stock pour les consignations
Route::post('/deposits/add', [DepositController::class, 'addDeposit']);

//liste des consignations
Route::get('/depositsList', [DepositController::class, 'depositsList']);

//mise a jour du password
Route::post('/settings/password', [SettingsController::class, 'updatePassword'])
    ->name('settings.updatePassword')
    ->middleware('auth');
