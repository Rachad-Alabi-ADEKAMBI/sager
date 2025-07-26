@extends('layouts.app')

@include('pages.back.seller.sidebar')


@section('title', 'Vente- sager')

<script>
    window.sellerName = @json(auth()->user()->name);
</script>

@section('content')
    <sale-component>
    @endsection
