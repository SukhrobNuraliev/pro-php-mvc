@extends('layouts/products')

<h1>All Products</h1>
<p>Show all products...</p>

@if($next)
    <a href="{{ $next }}">next</a>
@endif