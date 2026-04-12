@extends('layouts.AdminLTE.index')

@section('icon_page', 'dashboard')

@section('title', 'Dashboard ')

@section('menu_pagina')

@section('content')
  <div class="row">
    <!-- <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>{{ $permissionCount }}</h3>

          <p>Peran</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-stalker"></i>
        </div>
        <a href="{{ route('role') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div> -->
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3>{{ $activeUserCount }}</h3>

          <p>Admin Terdaftar</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
        <a href="{{ route('user') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-blue">
        <div class="inner">
          <h3>{{ $totalOrderToday }}</h3>

          <p>Bon Hari ini</p>
        </div>
        <div class="icon">
          <i class="ion ion-clipboard"></i>
        </div>
        <a href="{{ route('orders.index') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-blue">
        <div class="inner">
          <h3>{{ $totalPenjualanToday }}</h3>

          <p>Penjualan Hari ini</p>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <a href="{{ route('order-products.index') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{ $barangMasuk }}</h3>

          <p>Barang masuk Hari ini</p>
        </div>
        <div class="icon">
          <i class="ion ion-settings"></i>
        </div>
        <a href="{{ route('orders.index') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
  </div>
  <!-- /.row -->
  <!-- content dashboard -->
  <!-- /.row -->
@endsection
