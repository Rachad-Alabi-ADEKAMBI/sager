@extends('layouts.app')

@include('pages.back.admin.sidebar')


@section('title', 'Historique des ventes - sager')

@section('content')
    <main class="main-content" id='app'>
        <header class="header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Historique des ventes</h1>
            </div>
            <div class="user-info">
                <span>Administrateur</span>
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>

        <sales-component>

    </main>
@endsection


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/vue@3.4.21/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
