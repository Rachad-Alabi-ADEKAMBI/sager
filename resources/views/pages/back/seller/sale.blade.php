@extends('layouts.app')

@include('pages.back.seller.sidebar')


@section('title', 'Nouvelle facture - sager')


@section('content')
<main class="content seller-sale-page" id="app">
    <script>
    </script>

    <header class="header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Vente</h1>
        </div>
        <div class="user-info">
            <span>{{ auth()->user()->name }}</span>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>

        <script>
            window.sellerName = "{{ auth()->user()->name ?? '' }}";
        </script>
    </header>

    <div class="seller-sale-web-title">
        <h1>Vente</h1>
    </div>

    <sale-component>

</main>

@endsection

<style>
    .seller-sale-web-title {
        padding: 1rem 2rem 0;
    }

    .seller-sale-web-title h1 {
        margin: 0;
        color: #222;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .seller-sale-page .main-content {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    .seller-sale-page .sales-content {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    .seller-sale-page .sales-form {
        margin-top: 1rem !important;
    }

    @media (max-width: 1024px) {
        .seller-sale-web-title {
            display: none;
        }

        .content {
            padding-top: 68px !important;
        }

        .content > .main-content,
        .content .main-content {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .content > .sales-content,
        .content .sales-content {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .content .sales-form {
            margin-top: 0 !important;
        }
    }

    @media (max-width: 768px) {
        .content {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
    }
</style>
