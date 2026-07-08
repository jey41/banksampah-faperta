@extends('layouts.admin')

@section('content')
<x-admin.page-header title="Edit Harga Sampah" subtitle="Master Data" />

    @include('admin.trash-prices.create')
@endsection