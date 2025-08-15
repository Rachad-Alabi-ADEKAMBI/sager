@extends('layouts.app')

@include('pages.back.seller.sidebar')


@section('title', 'Vente- sager')



@section('content')
    <main class="content" id="app">
        <script>
            window.sellerName = @json(auth()->user()->name);
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
        </header>

        <sale-component>

    </main>

@endsection
