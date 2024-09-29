@extends('layouts.AdminLTE.index')

@section('icon_page', 'plus')

@section('title', 'Tambah Reparasi')
@section('layout_css')
    <link href="{{ asset('public/plugins/jquery-image-viewer/dist/jquery.magnify.css') }}" rel="stylesheet">
    <style>
        .select2-container {
            width: 100% !important;
        }
        .glyphicon.spinning {
            animation: spin 1s infinite linear;
            -webkit-animation: spin2 1s infinite linear;
        }

        @keyframes spin {
            from { transform: scale(1) rotate(0deg); }
            to { transform: scale(1) rotate(360deg); }
        }

        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg); }
            to { -webkit-transform: rotate(360deg); }
        }

        #cameraContainer, #photoContainer {
            display: none;
        }
        #video {
            width: 100%;
            height: auto;
        }
        #canvas {
            display: none;
        }
    </style>
@endsection

@section('menu_pagina')

    <li role="presentation">
        <a href="{{ route('orders.index') }}" class="link_menu_page">
            <i class="fa fa-shopping-basket"></i> Reparasi
        </a>
    </li>

@endsection

@section('content')

    <div class="row">
        <div class="col-md-12" id="alert-container"></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="customer">Pelanggan: <small class="text-danger">*</small></label>
                        <select class="form-control" id="customer" name="customer" required></select>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Trigger the modal with a button -->
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-add-customer"
                        style="margin: 25px auto;border-left: 1px solid #ccc;">Tambah Pelanggan</button>
                    <!-- Modal -->
                    <div id="modal-add-customer" class="modal fade" role="dialog" data-keyboard="false"
                        data-backdrop="static">
                        <div class="modal-dialog modal-md">
                            <!-- Modal content-->
                            <form id="add-customer-form" action="{{ route('customers.store') }}" method="post"
                                onsubmit="saveCustomer(event)" autocomplete="off">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Tambah Pelanggan</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="name">Nama: <small class="text-danger">*</small></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone_number">No Telepon: <small
                                                    class="text-danger">*</small></label>
                                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="city_id">Kota: <small class="text-danger">*</small></label>
                                            <select class="form-control" id="city_id" name="city_id" required>
                                                <option disabled selected> -- Pilih Kota -- </option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}"> {{ $city->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Alamat:</label>
                                            <textarea class="form-control" id="address" name="address"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="reset" id="btn-reset-add-customer" class="btn btn-danger pull-left"
                                            onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()">Reset</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal"
                                            onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()"
                                            style="margin-right: 15px;">Batalkan</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="site_id">Cabang: <small class="text-danger">*</small></label>
                <select class="form-control" id="site_id" name="site_id" required>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}">{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Detail Customer -->
        <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Detail Pelanggan</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body" id="detail-user">
                </div>
            </div>
        </div>

        <!-- Status Customer -->
        <div class="col-md-6">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Status</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body" id="status-customer">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" id="items">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Barang:</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive no-padding">
                        <table class="table table-hover table-bordered" style="border: 1px solid #ddd !important;" id="table-items">
                            <thead class="bg-navy">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Jenis</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Foto</th>
                                    <th class="text-center">Biaya</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Modal Edit Item -->
                    <div class="modal fade" id="modal-edit-item" role="dialog" data-keyboard="false"
                        data-backdrop="static">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <form id="edit-item-form" action="" method="get" onsubmit="formEditItem(event)"
                                enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="item_element" id="item_element" value="">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Edit Item</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="type_edit">Jenis Service: <small
                                                    class="text-danger">*</small></label>
                                            <select id="type_edit" class="form-control" name="type_edit">
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="keterangan_edit">Keterangan: <small
                                                    class="text-danger">*</small></label>
                                            <textarea class="form-control" id="keterangan_edit" name="keterangan_edit"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="biaya_edit">Biaya: <small class="text-danger">*</small></label>
                                            <input type="text" class="form-control" id="biaya_edit"
                                                name="biaya_edit">
                                        </div>
                                         <div class="form-group">
                                             <label for="discount_item_edit">Discount:</label>
                                             <input type="text" class="form-control" id="discount_item_edit"
                                                 name="discount_item_edit">
                                         </div>
                                        <div class="form-group" style="display: none;">
                                            <label for="total_after_discount_edit">Discount Amount: </label>
                                            <span id="total_after_discount_edit"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="total_item_edit">Total: <small class="text-danger">*</small></label>
                                            <input readonly type="text" class="form-control" id="total_item_edit" name="total_item_edit">
                                        </div>
                                        <div class="form-group">
                                            <label for="gambar_edit">Foto: <small class="text-danger">*</small></label>
                                            <input type="file" class="form-control-file" id="gambar_edit"
                                                name="gambar_edit" accept=".jpg,.jpeg,.png" multiple
                                                onchange="handleEditImageUpload(this)">
                                        </div>
                                        <p>OR</p>
                                        <div class="webcam-edit-container"></div>
                                        <hr>
                                        <table role="presentation" class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="300">#</th>
                                                    <th width="300">Foto</th>
                                                    <th width="300">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="content-image"></tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default margin-r-5"
                                            data-dismiss="modal">Batalkan</button>
                                        <button type="submit" class="btn btn-warning">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Show Image Item -->
                    <div class="modal fade" id="modal-show-image-item" role="dialog" data-keyboard="false"
                        data-backdrop="static">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Item Foto</h4>
                                </div>
                                <div class="modal-body">
                                    <table role="presentation" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="300">#</th>
                                                <th width="300">Foto</th>
                                                <th width="300">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="content-image"></tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default margin-r-5"
                                        data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-1" style="margin-bottom: 15px;">
            <span class="btn btn-success" style="margin-right: 15px;" data-toggle="modal" data-target="#modal-add-item">
                <i class="glyphicon glyphicon-plus"></i>
                <span>Tambah</span>
            </span>
            <!-- Modal Add Item -->
            <div class="modal fade" id="modal-add-item" role="dialog" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <form id="add-item-form" action="" method="get" onsubmit="saveItem(event)"
                        enctype="multipart/form-data" autocomplete="off">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Tambah Barang</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="type">Jenis Service: <small class="text-danger">*</small></label>
                                    <select id="type" class="form-control" name="type" required>
{{--                                        @foreach ($products as $product)--}}
{{--                                            <option value="{{ $product->id }}">{{ $product->name }}</option>--}}
{{--                                        @endforeach--}}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan: <small class="text-danger">*</small></label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="biaya">Biaya: <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" id="biaya" name="biaya" required>
                                </div>
                                 <div class="form-group">
                                     <label for="discount_item">Discount:</label>
                                     <input type="text" class="form-control" id="discount_item" name="discount_item">
                                 </div>
                                <div class="form-group" style="display: none;">
                                    <label for="total_after_discount">Discount Amount: </label>
                                    <span id="total_after_discount"></span>
                                </div>
                                <div class="form-group">
                                    <label for="total_item">Total: <small class="text-danger">*</small></label>
                                    <input readonly type="text" class="form-control" id="total_item" name="total_item">
                                </div>
                                <div class="form-group">
                                    <label for="gambar">Foto: <small class="text-danger">*</small></label>
                                    <input type="file" class="form-control-file" id="gambar" name="gambar"
                                        accept=".jpg, .jpeg, .png" multiple onchange="handleImageUpload(this)">
                                </div>
                                <p>OR</p>
                                <div class="webcam-container"></div>
                                <hr>
                                <table role="presentation"
                                    class="table table-striped table-bordered table-hover list-image">
                                    <thead>
                                        <tr>
                                            <th width="300">#</th>
                                            <th width="300">Foto</th>
                                            <th width="300">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="list-image"></tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default margin-r-5"
                                    data-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-1"> --}}
        {{--     <button type="button" class="btn bg-blue" data-toggle="modal" data-target="#modal-add-product">Tambah Reparasi</button> --}}
        {{--     <div id="modal-add-product" class="modal fade" role="dialog" data-keyboard="false" --}}
        {{--          data-backdrop="static"> --}}
        {{--         <div class="modal-dialog modal-md"> --}}
        {{--             <!-- Modal content--> --}}
        {{--             <form id="form-add-product" action="{{ route('products.store') }}" method="post" --}}
        {{--                   onsubmit="saveProduct(event)" autocomplete="off"> --}}
        {{--                 <div class="modal-content"> --}}
        {{--                     <div class="modal-header"> --}}
        {{--                         <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
        {{--                         <h4 class="modal-title">Tambah Reparasi</h4> --}}
        {{--                     </div> --}}
        {{--                     <div class="modal-body"> --}}
        {{--                         {{ csrf_field() }} --}}
        {{--                         <input type="hidden" name="type" id="add_type" value="1"> --}}
        {{--                         <div class="row"> --}}
        {{--                             <div class="col-lg-12"> --}}
        {{--                                 <div class="form-price form-group {{ $errors->has('price') ? 'has-error' : '' }}"> --}}
        {{--                                     <label for="price">Harga</label> --}}
        {{--                                     <input type="number" name="price" id="price" class="form-control" placeholder="Harga" value="{{ old('name') }}" autofocus> --}}
        {{--                                     @if($errors->has('price')) --}}
        {{--                                         <span class="help-block"> --}}
        {{--                                     <strong>{{ $errors->first('price') }}</strong> --}}
        {{--                                 </span> --}}
        {{--                                     @endif --}}
        {{--                                 </div> --}}
        {{--                                 <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}"> --}}
        {{--                                     <label for="name">Nama Jenis Produk</label> --}}
        {{--                                     <input type="text" name="name" id="name" class="form-control" placeholder="Name" required value="{{ old('name') }}" autofocus> --}}
        {{--                                     @if($errors->has('name')) --}}
        {{--                                         <span class="help-block"> --}}
        {{--                                     <strong>{{ $errors->first('name') }}</strong> --}}
        {{--                                 </span> --}}
        {{--                                     @endif --}}
        {{--                                 </div> --}}
        {{--                             </div> --}}
        {{--                         </div> --}}
        {{--                     </div> --}}
        {{--                     <div class="modal-footer"> --}}
        {{--                          <button type="reset" id="btn-reset-add-customer" class="btn btn-danger pull-left" onclick="document.getElementById('form-add-product').reset();document.querySelector('#form-add-product #price').focus()">Reset</button> --}}
        {{--                         <button type="button" class="btn btn-default" data-dismiss="modal" onclick="document.getElementById('form-add-product').reset();document.querySelector('#form-add-product #price').focus()" style="margin-right: 15px;">Batalkan</button> --}}
        {{--                         <button type="submit" class="btn bg-blue">Simpan</button> --}}
        {{--                     </div> --}}
        {{--                 </div> --}}
        {{--             </form> --}}
        {{--         </div> --}}
        {{--     </div> --}}
        {{-- </div> --}}
    </div>
    <div class="row">
        <div class="col-md-12 total-items">
            <div class="box box-info">
                <div class="box-header with-border-product">
                    <h3 class="box-title">Pembayaran</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                title="" data-original-title="Collapse Form Pembayaran">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="payment_method">Metode Pembayaran: <small class="text-danger">*</small></label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            @foreach($payment_methods as $payment_method)
                                <option value="{{ $payment_method->id }}">{{ $payment_method->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payment_merchant">Penyedia Pembayaran: <small class="text-danger">*</small></label>
                        <select class="form-control" id="payment_merchant" name="payment_merchant" required>
                            <option value="">-</option>
                            {{--                            @foreach($payment_merchants as $payment_merchant)--}}
                            {{--                                <option value="{{ $payment_merchant->id }}">{{ $payment_merchant->name }}</option>--}}
                            {{--                            @endforeach--}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payment_type">Tipe Pembayaran: <small class="text-danger">*</small></label>
                        <select class="form-control" id="payment_type" name="payment_type" required>
                            <option value="0">DP</option>
                            <option value="1">Lunas</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 total-items">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Estimasi</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="" data-original-title="Collapse Form Order">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Estimasi Selesai Reparasi:</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="estimate_service_done">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Estimasi Pengambilan Barang:</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="estimate_take_item">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 total-items">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Nilai</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="" data-original-title="Collapse Form Order">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data"
                        autocomplete="off">
                        <input type="hidden" name="customer_id" id="customer_id" value="">
                        {{ csrf_field() }}
                        <p class="margin-b-2"><b>Sub total: </b><span id="sub_total"></span></p>
                        <p class="margin-b-2"><b>Discount: </b><input readonly type="text" id="discount" name="discount" value="" class="form-control" style="display: inline"></p>
                        <p class="margin-b-2"><i>INCLUDED PPN: </i><span id="tax">11%</span></p>
                        <p class="margin-b-2"><b>Total: </b><span id="total"></span></p>
                        <p class="margin-b-2"><b>Uang muka: </b><input type="text" id="dp" name="dp"
                                value="" class="form-control" style="display: inline"></p>
                        <p class="margin-b-2"><b>Kekurangan: </b><span id="kekurangan">-</span></p>
                        <input type="hidden" id="kekurangan-final" name="kekurangan" class="form-control"
                            style="display: inline">
                        {{-- <p class="margin-b-2"><b>Pembayaran: </b><input type="text" id="pembayaran" name="pembayaran" value="0" readonly class="form-control" style="display: inline"></p> --}}
                    </form>
                </div>
            </div>
        </div>
</div>

    <div class="row">
        <div class="col-md-12 total-items">
            {{--  <button type="submit" class="btn bg-olive pull-left" data-toggle="modal" data-target="#modal-take" style="margin-right: 15px;"> --}}
            {{--      <i class="fa fa-fw fa-save"></i> --}}
            {{--      <span>Ambil</span> --}}
            {{--  </button> --}}
            {{-- <!-- Modal --> --}}
            {{-- <div id="modal-take" class="modal fade" role="dialog"> --}}
            {{--     <div class="modal-dialog modal-lg"> --}}
            {{--         <!-- Modal content--> --}}
            {{--         <form id="add-customer-form" action="{{ route('customers.store') }}" method="post" onsubmit="saveCustomer(event)"> --}}
            {{--             <div class="modal-content"> --}}
            {{--                 <div class="modal-header"> --}}
            {{--                     <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
            {{--                     <h4 class="modal-title">Ambil</h4> --}}
            {{--                 </div> --}}
            {{--                 <div class="modal-body"> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="type">Metode Pembayaran: <small class="text-danger">*</small></label> --}}
            {{--                         <select class="form-control" id="type" name="type" required> --}}
            {{--                             @foreach ($products as $product) --}}
            {{--                                 <option value="{{ $product->id }}">{{ $product->name }}</option> --}}
            {{--                             @endforeach --}}
            {{--                         </select> --}}
            {{--                     </div> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="type_merchant">Penyedia Pembayaran: <small class="text-danger">*</small></label> --}}
            {{--                         <select class="form-control" id="type_merchant" name="type_merchant" required> --}}
            {{--                             @foreach ($payment_merchants as $payment_merchant) --}}
            {{--                                 <option value="{{ $payment_merchant->id }}">{{ $payment_merchant->name }}</option> --}}
            {{--                             @endforeach --}}
            {{--                         </select> --}}
            {{--                     </div> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="kekurangan">Kekurangan: <small class="text-danger">*</small></label> --}}
            {{--                         <input type="text" class="form-control" id="kekurangan" name="kekurangan" required> --}}
            {{--                     </div> --}}
            {{--                     <div class="form-group"> --}}
            {{--                         <label for="diambil">Dimbail oleh: <small class="text-danger">*</small></label> --}}
            {{--                         <input type="text" class="form-control" id="diambil" name="diambil" required> --}}
            {{--                     </div> --}}
            {{--                 </div> --}}
            {{--                 <div class="modal-footer"> --}}
            {{--                      --}}{{-- <button type="reset" class="btn btn-danger pull-left" onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()">Reset Form</button> --}}
            {{--                      --}}{{-- <button type="button" class="btn btn-default" data-dismiss="modal"  onclick="document.getElementById('add-customer-form').reset();document.querySelector('#add-customer-form #name').focus()" style="margin-right: 15px;">Close Form</button> --}}
            {{--                      --}}{{-- <button type="submit" class="btn btn-primary">Submit Form</button> --}}
            {{--                     <button type="button" class="btn btn-default" data-dismiss="modal" style="margin-right: 15px;">Close Form</button> --}}
            {{--                     <button type="button" class="btn btn-primary" data-dismiss="modal" style="margin-right: 15px;">Simpan</button> --}}
            {{--                 </div> --}}
            {{--             </div> --}}
            {{--         </form> --}}
            {{--     </div> --}}
            {{-- </div> --}}
            <button type="button" class="btn bg-purple pull-left" id="btn-order" style="margin-right: 15px;"
                onclick="createOrder()">
                <i class="fa fa-fw fa-save"></i>
                <span>Simpan</span>
            </button>
            <a href="{{ route('orders.index') }}" class="btn btn-default pull-left"><i class="fa fa-fw fa-close"></i>
                Batalkan</a>
        </div>
    </div>

@endsection

@section('layout_js')
    <script type="text/javascript" src="{{ asset('public/plugins/jquery-image-viewer/dist/jquery.magnify.js') }}"></script>
    <script>
        $('#items').hide();
        $('.total-items').hide();
        $('.list-image').hide();
        const products = @json($products);
        const items = [];
        let dataFile = [];

        const cameraContent = `
            <div class="form-group" style="margin-top: 15px;">
                <a href="javascript:void(0)" id="startCamera" class="btn btn-primary"><i class="fa fa-camera fa-fw"></i> Access Camera</a>
            </div>
            <div id="cameraContainer" class="form-group" style="margin-top: 15px;">
                <div class="text-center">
                    <label for="cameraSelect">Select Camera:</label>
                    <select id="cameraSelect" class="form-control mb-3"></select>
                    <video id="video" class="img-thumbnail" playsinline autoplay style="margin-top: 25px;"></video>
                    <div class="form-group" style="margin-top: 15px;">
                        <a href="javascript:void(0)" id="takePhoto" class="btn btn-success" style="margin-right: 10px;"><i class="fa fa-check fa-fw"></i> Take Photo</a>
                        <a href="javascript:void(0)" id="closeCamera" class="btn btn-danger"><i class="fa fa-close fa-fw"></i> Close Camera</a>
                    </div>
                </div>
            </div>
            <div id="photoContainer" class="form-group" style="margin-top: 25px;">
                <div class="text-center">
                    <canvas id="canvas"></canvas>
                    <img id="photo" class="img-thumbnail"/>
                    <div class="form-group" style="margin-top: 15px;">
                        <a href="javascript:void(0)" id="retakePhoto" class="btn bg-navy"><i class="fa fa-camera fa-fw"></i> Retake Photo</a>
                    </div>
                </div>
            </div>
        `;

        function initializeCamera() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const photo = document.getElementById('photo');
            const cameraContainer = $('#cameraContainer');
            const photoContainer = $('#photoContainer');
            const cameraSelect = $('#cameraSelect');
            let stream = null;

            $(document).on('click', '#startCamera', function () {
                cameraContainer.show();
                $('#startCamera').hide();
                navigator.mediaDevices.enumerateDevices()
                    .then(devices => {
                        devices.forEach(device => {
                            if (device.kind === 'videoinput') {
                                const option = document.createElement('option');
                                option.value = device.deviceId;
                                option.text = device.label || `Camera ${cameraSelect.length + 1}`;
                                cameraSelect.append(option);
                            }
                        });
                        return startCamera(cameraSelect.val());
                    })
                    .catch(function (err) {
                        console.log("An error occurred: " + err);
                    });
            });

            $(document).on('change', '#cameraSelect', function () {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                startCamera(cameraSelect.val());
            });

            function startCamera(deviceId) {
                navigator.mediaDevices.getUserMedia({
                    video: { deviceId: deviceId ? { exact: deviceId } : undefined }
                })
                    .then(function (mediaStream) {
                        stream = mediaStream;
                        video.srcObject = stream;
                        video.play();
                    })
                    .catch(function (err) {
                        console.log("An error occurred: " + err);
                    });
            }

            $(document).on('click', '#takePhoto', function () {
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                photo.src = canvas.toDataURL('image/jpeg');
                dataFile.push(photo.src);
                console.log(dataFile);
                setTimeout(() => {
                    renderListImage();
                    const contentImage = $('.content-image');
                    contentImage.empty();

                    dataFile.forEach(function(image, index) {
                        const row = `
                    <tr>
                        <th>
                            ${index + 1}
                        </th>
                        <td class="text-center">
                            <img src="${image}" alt="Foto Barang ${index + 1}" title="Foto Barang ${index + 1}" class="img-thumbnail" style="height:100px;cursor:pointer;"  data-magnify="gallery" data-caption="Foto Barang ${index + 1}" data-src="${image}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeImage(${index})">Hapus foto</button>
                        </td>
                    </tr>
                `;
                        contentImage.append(row);
                    });
                }, 0);
                setTimeout(function () {
                    $('#retakePhoto').trigger('click');
                }, 0)
                photoContainer.show();
                cameraContainer.hide();
                canvas.show();
            });

            $(document).on('click', '#closeCamera', function () {
                if (stream) {
                    stream.getTracks().forEach(function (track) {
                        track.stop();
                    });
                }
                cameraContainer.hide();
                $('#startCamera').show();
                cameraSelect.empty();
            });

            $(document).on('click', '#retakePhoto', function () {
                photoContainer.hide();
                cameraContainer.show();
            });

            $(document).on('click', '#cancelPhoto', function () {
                photo.src = "";
                canvas.hide();
                photoContainer.hide();
                $('#startCamera').show();
                if (stream) {
                    stream.getTracks().forEach(function (track) {
                        track.stop();
                    });
                }
                cameraContainer.hide();
                cameraSelect.empty();
            });
        }

        function getTypeById(nameKey, myArray) {
            for (let i = 0; i < myArray.length; i++) {
                if (myArray[i].id === parseFloat(nameKey, 10)) {
                    return myArray[i];
                }
            }
        }

        $('#payment_method').on('change', function() {
            getPaymentMerchant();
        });

        function getPaymentMerchant() {
            $.ajax({
                url: '{{ route("order-products.merchant_by_payment") }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    payment_method: $('#payment_method').val()
                },
                success: function(response) {
                    $('#payment_merchant').empty();
                    $.each(response, function(index, paymentMerchant) {
                        $('#payment_merchant').append('<option value="' + paymentMerchant.id + '">' + paymentMerchant.name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function uuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                let r = Math.random() * 16 | 0,
                    v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        function handleImageUpload(element) {
            const files = $(element)[0].files;
            if (files.length === 0) return;

            // console.log(files);
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const ext = file.name.split('.').pop().toLowerCase();
                if ($.inArray(ext, ['png', 'jpg', 'jpeg']) !== -1) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Mengakses data URL base64 dari file
                        const base64Data = e.target.result;
                        // Memasukkan data URL base64 ke dalam array
                        dataFile.push(base64Data);
                    };
                    // Membaca file sebagai data URL
                    reader.readAsDataURL(file);
                }
            }

            setTimeout(() => {
                renderListImage();
                if (dataFile.length === 0) {
                    $(element).val(null);
                }
            }, 500);

            if($('#closeCamera').length > 0) {
                $('#closeCamera').trigger('click');
            }
        }

        function handleEditImageUpload(element) {
            const files = $(element)[0].files;
            if (files.length === 0) return;

            // console.log(files);
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const ext = file.name.split('.').pop().toLowerCase();
                if ($.inArray(ext, ['png', 'jpg', 'jpeg']) !== -1) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Mengakses data URL base64 dari file
                        const base64Data = e.target.result;
                        // Memasukkan data URL base64 ke dalam array
                        dataFile.push(base64Data);
                    };
                    // Membaca file sebagai data URL
                    reader.readAsDataURL(file);
                }
            }

            setTimeout(() => {
                const contentImage = $('.content-image');
                contentImage.empty();

                dataFile.forEach(function(image, index) {
                    const row = `
                    <tr>
                        <th>
                            ${index + 1}
                        </th>
                        <td class="text-center">
                            <img src="${image}" alt="Foto Barang ${index + 1}" title="Foto Barang ${index + 1}" class="img-thumbnail" style="height:100px;cursor:pointer;"  data-magnify="gallery" data-caption="Foto Barang ${index + 1}" data-src="${image}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeImage(${index})">Hapus foto</button>
                        </td>
                    </tr>
                `;
                    contentImage.append(row);
                });
            }, 500);

            if($('#closeCamera').length > 0) {
                $('#closeCamera').trigger('click');
            }
        }

        function renderListImage(itemIndex=null) {
            // console.log(dataFile);
            const listImage = $('#list-image');
            $('.list-image').hide();
            $('.list-image').show();
            listImage.empty();
            for (let i = 0; i < dataFile.length; i++) {
                if(itemIndex === null) {
                    listImage.append(`
                        <tr>
                            <th>
                                ${i + 1}
                            </th>
                            <td class="text-center">
                                <img src="${dataFile[i]}" alt="Foto Barang ${i + 1}" title="Foto Barang ${i + 1}" class="img-thumbnail" style="height:100px;cursor:pointer;" data-magnify="gallery" data-caption="Foto Barang ${i + 1}" data-src="${dataFile[i]}">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeImage(${i})">Hapus foto</button>
                            </td>
                        </tr>
                    `);
                } else {
                    listImage.append(`
                        <tr>
                            <th>
                                ${i + 1}
                            </th>
                            <td class="text-center">
                                <img src="${dataFile[i]}" alt="Foto Barang ${i + 1}" title="Foto Barang ${i + 1}" class="img-thumbnail" style="height:100px;cursor: pointer;" data-magnify="gallery" data-caption="Foto Barang ${i + 1}" data-src="${dataFile[i]}">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeEditImage(event, this, ${itemIndex}, ${i})">Hapus foto</button>
                            </td>
                        </tr>
                    `);
                }
            }
        }

        function removeImage(itemIndex) {
            if (confirm("Apakah Anda yakin ingin MENGHAPUS foto ini?") === true) {

                dataFile.splice(itemIndex, 1);

                setTimeout(() => {
                    renderListImage(itemIndex);

                    const contentImage = $('.content-image');
                    contentImage.empty();

                    dataFile.forEach(function(image, imageIndex) {
                        const row = `
                            <tr>
                                <th>
                                    ${imageIndex + 1}
                                </th>
                                <td class="text-center">
                                    <img src="${image}" alt="Foto Barang ${imageIndex + 1}" title="Foto Barang ${imageIndex + 1}" class="img-thumbnail" style="height:100px;cursor:pointer;" data-magnify="gallery" data-caption="Foto Barang ${imageIndex + 1}" data-src="${image}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeEditImage(event, this, ${itemIndex}, ${imageIndex})">Hapus foto</button>
                                </td>
                            </tr>
                        `;
                        contentImage.append(row);
                    });

                    if (dataFile.length === 0) {
                        $('.list-image').hide();
                        $('#gambar').val(null);
                    }
                }, 500);
            }
        }

        function removeEditImage(e, el, itemIndex, imageIndex) {
            if (confirm("Apakah Anda yakin ingin MENGHAPUS foto ini?") === true) {
                items[itemIndex]['gambar'].splice(imageIndex, 1);

                const message = 'Foto berhasil dihapus';
                $('.top-right').notify({
                    message: {
                        text: `Sukses! ${message}`
                    }
                }).show();

                setTimeout(() => {
                    showImageAsTable(itemIndex);
                    if (dataFile.length === 0) {
                        $('.list-image').hide();
                        $('#gambar_edit').val(null);
                    }
                }, 500);
            }
        }

        function showEditItemForm(index) {
            showImageAsTable(index);
            $('#item_element').val(index);

            const dataItem = items[index];
            let info_discount = parseInt(dataItem.discount_item, 10) > 100 ? dataItem.discount_item : dataItem.biaya * (parseInt(dataItem.discount_item, 10) / 100);


            $(`#type_edit option[value='${dataItem.type}']`).prop('selected', true);
            // const type = $('#type_edit').val();
            const keterangan = $('#keterangan_edit').val(dataItem.keterangan);
            const biaya = $('#biaya_edit').val(parseInt(dataItem.biaya, 10).toLocaleString('id-ID'));
            const discount_item = $('#discount_item_edit').val(parseInt(dataItem.discount_item, 10).toLocaleString('id-ID'));
            const total_item_edit = $('#total_item_edit').val((parseInt(dataItem.biaya, 10) - parseInt(info_discount, 10)).toLocaleString('id-ID'));
            $('#discount_item_edit').trigger('input');
        }

        function showImageAsTable(itemIndex) {
            const contentImage = $('.content-image');
            // console.log(items)
            contentImage.empty();

            items[itemIndex].gambar.forEach(function(image, imageIndex) {
                const row = `
                    <tr>
                        <th>
                            ${imageIndex + 1}
                        </th>
                        <td class="text-center">
                            <img src="${image}" alt="Foto Barang ${imageIndex + 1}" title="Foto Barang ${imageIndex + 1}" class="img-thumbnail" style="height:100px;cursor: pointer;" data-magnify="gallery" data-caption="Foto Barang ${imageIndex + 1}" data-src="${image}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeEditImage(event, this, ${itemIndex}, ${imageIndex})">Hapus foto</button>
                        </td>
                    </tr>
                `;
                contentImage.append(row);
                dataFile.push(image);
            });
        }

        function removeItem(e, el, index) {
            if (confirm("Apakah Anda yakin ingin MENGHAPUS item ini?") === true) {

                items.splice(index, 1);
                renderItems();

                if (items && !items.length) {
                    $('#items').hide();
                    $('.total-items').hide();
                }

                const message = 'Item berhasil dihapus';
                $('.top-right').notify({
                    message: {
                        text: `Sukses! ${message}`
                    }
                }).show();

            }
        }

        function selectCustomerById(customerId) {
            $.ajax({
                type: 'GET',
                url: '{{ route('customer.search') }}' + '?term=' + customerId + '&_type=query&q=' +
                customerId, // Asumsi endpoint mendukung query parameter 'id'
                success: function(data) {
                    if (data && data.length > 0) {
                        let item = data[data.length - 1]; // asumsi data kembali sebagai array dan pelanggan yang dicari selalu index 0
                        let newOption = new Option(item.name, item.id, true, true);

                        $(newOption).data('address', item.address);
                        $(newOption).data('phone', item.phone_number);

                        $('#customer').append(newOption).trigger('change');
                        $('#customer').trigger({
                            type: 'select2:select',
                            params: {
                                data: item,
                            }
                        });
                    }
                }
            });
        }

        function saveCustomer(event) {
            event.preventDefault();
            $.ajax({
                url: $('#add-customer-form').attr('action'),
                method: 'POST',
                data: $('#add-customer-form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Handle response
                    // console.log(response);
                    const customerId = response.id;
                    selectCustomerById(customerId);
                    const type = 'success';
                    const message = 'Pelanggan berhasil disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Sukses!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(
                    alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup alert setelah 3 detik
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 3000);

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-customer').modal('hide');

                    $('#customer').val(customerId).trigger('change');

                    resetFormAddItem();
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    const type = 'danger';
                    const message = 'Pelanggan tidak bisa disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Oops!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(
                    alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-customer').modal('hide');
                }
            });
        }

        function saveProduct(event) {
            event.preventDefault();
            $.ajax({
                url: $('#form-add-product').attr('action'),
                method: 'POST',
                data: $('#form-add-product').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Handle response
                    // console.log(response);
                    const productId = response.id;
                    const type = 'success';
                    const message = 'Reparasi berhasil disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Sukses!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup alert setelah 3 detik
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 3000);

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-product').modal('hide');

                    $('#type').val(productId).trigger('change');

                    $('#form-add-product').trigger("reset");
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    const type = 'danger';
                    const message = 'Reparasi tidak bisa disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Oops!</strong> ${message}
                        </div>
                    `;
                    $('#alert-container').html(alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup modal setelah selesai menyimpan data
                    $('#modal-add-product').modal('hide');
                }
            });
        }

        function resetFormAddItem() {
            const listImage = $('#list-image');
            listImage.empty();
            $('.list-image').hide();
            // Reset form
            $('#add-item-form, #add-customer-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            dataFile = [];
        }

        function renderItems() {
            const tbody = $('#table-items tbody'); // Ganti '#itemTable' dengan ID dari elemen tabel Anda
            tbody.empty(); // Kosongkan isi tabel sebelum menambahkan item baru


            // Loop melalui setiap item dan tambahkan baris HTML untuk masing-masing item
            items.forEach(function(item, index) {
                let type = getTypeById(item.type, products);
                const imageSource = ''; // Tentukan sumber gambar, misalnya dari properti gambar item
                let info_discount = parseInt(item.discount_item, 10) > 100 ? item.discount_item : item.biaya * (parseInt(item.discount_item, 10) / 100);

                const row = `
                    <tr>
                        <th class="text-center">${index + 1}</th>
                        <td class="text-center">${type.name}</td>
                        <td class="text-center">${item.keterangan}</td>
                        <td class="text-center">
                            <!--
                            <img src="${imageSource}" alt="Foto Barang" class="img-thumbnail" style="height:50px">
                            -->
                            <span class="btn btn-info btn-xs" style="margin-right: 15px;" data-toggle="modal" data-target="#modal-show-image-item" onclick="showImageAsTable(${index})">
                                <i class="fa fa-image margin-r-5"></i>
                                <span>Tampilkan Foto</span>
                            </span>
                        </td>
                        <td class="text-center">
                            <b>${parseInt(item.biaya, 10).toLocaleString('id-ID')}</b>
                        </td>
                        <td class="text-center">
                            <b>${parseInt(item.discount_item, 10).toLocaleString('id-ID')}${item.discount_item > 100 ? '' : '%'} ${item.discount_item > 100 ? '' : "(" + (parseInt(item.biaya, 10) * (item.discount_item / 100)).toLocaleString('id-ID') + ")"}</b>
                        </td>
                        <td class="text-center">
                            <b>${(parseInt(item.biaya, 10) - parseInt(info_discount, 10)).toLocaleString('id-ID')}</b>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-warning btn-xs margin-r-5" data-toggle="modal" data-target="#modal-edit-item" onclick="showEditItemForm(${index})">Edit</button>
                            <button type="button" class="btn btn-danger btn-xs margin-r-5" onclick="removeItem(event, this, ${index})">Hapus</button>
                        </td>
                    </tr>
                `;
                tbody.append(row); // Tambahkan baris ke tabel
            });
        }

        function saveItem(event) {
            $('#items').show();
            $('.total-items').show();
            event.preventDefault(); // Menghentikan aksi default dari submit form

            // Ambil nilai dari form
            const type = $('#type').val();
            const keterangan = $('#keterangan').val();
            const biaya = $('#biaya').val().replace(/\./g, '');
            const discount_item = $('#discount_item').val() !== '' ? parseInt($('#discount_item').val().replace(/\./g, ''), 10) : 0;
            const gambar = dataFile;

            // Buat objek item
            const newItem = {
                type,
                keterangan,
                biaya,
                discount_item,
                // netto: biaya - discount_item,
                gambar
            };

            // console.log(newItem);

            // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

            // Contoh operasi CRUD sederhana (tambahkan item ke array)
            items.push(newItem); // items adalah variabel yang berisi array item

            renderItems();

            // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
            const message = 'Barang berhasil ditambahkan!';
            $('.top-right').notify({
                message: {
                    text: `Sukses! ${message}`
                }
            }).show();

            // Tutup modal setelah selesai menyimpan data
            $('#modal-add-item').modal('hide');

            // Reset form
            $('#add-item-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            sumTotalItem();
            $('#discount').trigger('input');
            dataFile = [];
        }

        function formEditItem(event) {
            $('#items').show();
            $('.total-items').show();
            event.preventDefault(); // Menghentikan aksi default dari submit form

            const element = $('#item_element').val();

            // Ambil nilai dari form
            const type = $('#type_edit').val();
            const keterangan = $('#keterangan_edit').val();
            const biaya = $('#biaya_edit').val().replace(/\./g, '');
            const discount_item = $('#discount_item_edit').val().replace(/\./g, '');

            // Buat objek item
            const newItem = {
                type,
                keterangan,
                biaya,
                discount_item,
                // netto: biaya - discount_item,
                gambar: dataFile
            };

            // console.log(newItem);

            // Lakukan operasi CRUD di sini, misalnya tambahkan item ke array atau kirimkan ke server melalui AJAX

            // Contoh operasi CRUD sederhana (tambahkan item ke array)
            items[element] = newItem; // items adalah variabel yang berisi array item

            renderItems();

            // Tampilkan pesan atau lakukan tindakan lainnya setelah berhasil menambahkan item
            const message = 'Barang berhasil diedit!';
            $('.top-right').notify({
                message: {
                    text: `Sukses! ${message}`
                }
            }).show();

            // Tutup modal setelah selesai menyimpan data
            $('#modal-edit-item').modal('hide');

            // Reset form
            $('#edit-item-form').trigger("reset");
            // Atau lakukan tindakan lainnya, seperti menutup modal, mereset form, dll.

            sumTotalItem();

            dataFile = [];
        }

        function sumTotalItem() {
            const dp = $('#dp').val();

            let total = 0;
            let discount = 0;

            for (let i = 0; i < items.length; i++) {
                total += parseInt(items[i].biaya, 10);
                discount += parseInt(items[i].discount_item, 10) > 100 ? Number(items[i].discount_item) : items[i].biaya * (parseInt(items[i].discount_item, 10) / 100);
            }
            console.log(total.toLocaleString('id-ID'))

            $('#sub_total').text(total.toLocaleString('id-ID'));
            $('#discount').val(discount.toLocaleString('id-ID'));
            if (dp !== '') {
                $('#kekurangan').text(parseInt(total - discount - dp.replace(/\./g, ''), 10).toLocaleString('id-ID'));
            } else {
                $('#kekurangan').text(parseInt(total - discount, 10).toLocaleString('id-ID'));
            }
        }

        function createOrder() {
            const customer_id = $('#customer_id').val();
            const site_id = $('#site_id option:selected').val();
            const dp = $('#dp').val().replace(/\./g, '');
            const discount = $('#discount').val().replace(/\./g, '');
            const total = $('#sub_total').text().replace(/\./g, '');
            const kekurangan = $('#kekurangan-final').val();
            const estimate_service_done = $('#estimate_service_done').val();
            const estimate_take_item = $('#estimate_take_item').val();
            const payment_method = $('#payment_method').val();
            const payment_merchant = $('#payment_merchant').val();
            const payment_type = $('#payment_type').val();

            // console.log('FINAL RESULT');
            // console.table([{items}, {customer_id}, {site_id}, {dp}, {total}, {kekurangan}]);

            const url = "{{ route('orders.store') }}";
            $('.alert').alert('close');

            const alert = `
                        <div class="alert alert-warning alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Mohon tunggu!</strong> Reparasi sedang diproses...
                            &nbsp;<span class="glyphicon glyphicon-refresh spinning"></span>
                        </div>
                    `;
            $('#alert-container').html(alert);

            $.ajax({
                url,
                data: {
                    items,
                    customer_id,
                    site_id,
                    dp,
                    discount: discount === '' ? 0 : discount,
                    total,
                    kekurangan,
                    estimate_service_done,
                    estimate_take_item,
                    payment_method,
                    payment_merchant,
                    payment_type,
                },
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Handle response
                    console.log(response);
                    $('#alert-container').empty();
                    const type = 'success';
                    const message = 'Reparasi berhasil disimpan!'
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Sukses!</strong> ${message}
                            &nbsp;<span class="glyphicon glyphicon-ok"></span>
                        </div>
                    `;
                    $('#alert-container').html(
                    alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert

                    // Tutup alert setelah 3 detik
                    setTimeout(() => {
                        $('.alert').alert('close');
                        window.location.reload();
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(error);
                    $('#alert-container').empty();
                    const type = 'danger';
                    const message = 'Reparasi tidak berhasil disimpan!'
                    const statusCode = `${xhr.statusText} (${xhr.status})`;
                    const alert = `
                        <div class="alert alert-${type} alert-dismissible">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          <strong>Oops!</strong> ${message} ${statusCode}
                        &nbsp;<span class="glyphicon glyphicon-remove"></span>
                        </div>
                    `;
                    $('#alert-container').html(
                    alert); // Ganti '#alert-container' dengan ID dari elemen tempat Anda ingin menampilkan alert
                }
            });

            $('html, body').animate({
                scrollTop: $("#alert-container").offset().top - 125
            }, 1000);
        }

        $(function() {
            $('#modal-add-customer').on('shown.bs.modal', function() {
                $('#add-customer-form #name').focus();
            });
            $('#modal-show-image-item').on('hidden.bs.modal', function() {
                $('.content-image').empty();
                dataFile = [];
                $('#edit-item-form').trigger('reset');
                resetFormAddItem();
            });
            $('#modal-add-item').on('hidden.bs.modal', function() {
                resetFormAddItem();
                if ($('.webcam-container').length > 0) {
                    $('#closeCamera').trigger('click');
                }
                $('.webcam-container').empty();
            });
            $('#modal-add-item').on('shown.bs.modal', function () {
                dataFile = [];
                $('.webcam-container').append(cameraContent);
                initializeCamera();
            });
            $('#modal-edit-item').on('shown.bs.modal', function () {
                $('.webcam-edit-container').append(cameraContent);
                initializeCamera();
            });
            $('#modal-edit-item').on('hidden.bs.modal', function() {
                $('#item_element').val('');
                // $(`#type_edit option[value='1']`).attr("selected");
                $('#keterangan_edit').val('');
                $('#biaya_edit').val('');
                if ($('.webcam-edit-container').length > 0) {
                    $('#closeCamera').trigger('click');
                }
                $('.webcam-edit-container').empty();
                $('#edit-item-form').trigger('reset');
                resetFormAddItem();
            });
            $('#customer').select2({
                placeholder: '-- Pelanggan --',
                ajax: {
                    url: '{{ route('customer.search') }}', // Ganti dengan URL endpoint Anda
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        const options = [];
                        $.each(data, function(index, item) {
                            options.push({
                                id: item.id,
                                text: `${item.name} (${item.phone_number})`,
                                data: {
                                    address: item.address,
                                    phone: item.phone_number
                                }
                            });
                        });
                        return {
                            results: options
                        };
                    },
                    cache: true
                }
            });

            $('#customer').on('select2:select', function(e) {
                const data = e.params.data;

                let customerId, customerName, address, phone;

                if (data.data === undefined) {
                    customerId = data.id;
                    customerName = data.name;
                    address = data.address;
                    phone = data.phone_number;
                } else {
                    const dataAttribute = data.data;

                    customerId = data.id;
                    customerName = data.text;
                    address = dataAttribute.address;
                    phone = dataAttribute.phone;
                }

                // detail-user
                $('#detail-user').html(`
                    <p class="text-muted well well-sm no-shadow margin-b-10">
                        <strong>Nama:</strong> ${customerName === null ? '-' : customerName}
                         <br>
                        <strong>Alamat:</strong> ${address === null ? '-' : address}
                        <br>
                        <strong>Telepon:</strong> ${phone === null ? '-' : phone}
                    </p>
                `);
                // status-customer
                $('#status-customer').html(`
                    <p class="text-center margin-b-10"><b>BARU</b></p>
                    <p class="text-center margin-b-2"><b>Oleh: </b> -<!-- ${customerName === null ? '-' : customerName} --></p>
                    <p class="text-center margin-b-2"><b>Pada: </b> -<!-- ${phone === null ? '-' : phone} --></p>
                `);

                $('#customer_id').val(customerId);
            });

            $('#type').select2({
                dropdownAutoWidth: true,
                width: 'resolve',
                dropdownParent: $("#modal-add-item .modal-body"),
                placeholder: '-- Jenis Service --',
                ajax: {
                    url: '{{ route('products.search', ['type' => 1]) }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        const options = [];
                        $.each(data, function(index, item) {
                            options.push({
                                id: item.id,
                                text: `${item.name}`,
                                data: {
                                    id: item.id,
                                    name: item.name,
                                    price: item.price
                                }
                            });
                        });
                        return {
                            results: options
                        };
                    },
                    cache: true
                }
            });

            $('#dp, #discount').on('input', function(e) {
                let total = $(this).val().replace(/\./g, '');

                // Hapus semua karakter selain digit
                let digitsOnly = total.replace(/\D/g, '');

                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Update nilai input dengan angka yang diformat
                $(this).val(formattedNumber);
            });

            $('#dp').on('input', function(e) {
                let total = $('#sub_total').text().replace(/\./g, '');
                const total_expense = parseInt(total, 10);
                const discount = $('#discount').val() === '' ? 0 : $('#discount').val().replace(/\./g, '');
                const dp = $('#dp').val();

                // Hapus semua karakter selain digit
                let digitsOnly = dp.replace(/\D/g, '');

                total = (total_expense - parseInt(discount, 10)) - parseInt(digitsOnly, 10);

                // let formattedNumber = 0;
                // // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                // if($(this).val() !== '') {
                //     formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                // }

                // Update nilai input dengan angka yang diformat
                // $(this).val(formattedNumber);

                const kekurangan = !isNaN(total) ? total.toLocaleString('id-ID') : '-';
                $('#kekurangan').text(kekurangan.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
                $('#kekurangan-final').val(kekurangan.replace(/\D/g, ''));
            });

            $('#biaya, #biaya_edit, #discount_item, #discount_item_edit').on('input', function(e) {
                const value = $(this).val();

                // Hapus semua karakter selain digit
                let digitsOnly = value.replace(/\D/g, '');

                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Update nilai input dengan angka yang diformat
                $(this).val(formattedNumber);
            });

            $('#biaya').on('input', function(e) {
                $('#discount_item').trigger('input');
            });

            $('#discount_item').on('input', function(e) {
                const discount_item = $(this).val();

                let digitsOnly = discount_item.replace(/\D/g, '');

                let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                $(this).val(formattedNumber);

                const biaya = $('#biaya').val().replace(/\D/g, '');
                const total_biaya = (Number(biaya));

                const total_item = $('#total_item');
                if(discount_item === '') {
                    total_item.val(total_biaya === 0 ? '' : total_biaya.toLocaleString('id-ID'));
                    $('#total_after_discount_edit').text('').parent().css('display', 'none');
                } else {
                    if(Number(digitsOnly) > 100) {
                        total_item.val(total_biaya === 0 ? '' : (total_biaya - Number(digitsOnly)).toLocaleString('id-ID'));
                        $('#total_after_discount').text('').parent().css('display', 'none');
                    } else {
                        const total_after_discount = total_biaya * (Number(digitsOnly) / 100);
                        if(Number(digitsOnly) === 100) {
                            total_item.val(total_biaya === 0 ? '' : total_biaya.toLocaleString('id-ID'));
                            $('#total_after_discount').text('').parent().css('display', 'none');
                        } else {
                            $('#total_after_discount').text(total_after_discount.toLocaleString('id-ID')).parent().css('display', 'inline');
                            total_item.val(total_biaya === 0 ? '' : (total_biaya - total_after_discount).toLocaleString('id-ID'));
                        }
                    }
                }
            });

            $('#discount_item_edit').on('input', function(e) {
                const discount_item_edit = $(this).val();

                let digitsOnly = discount_item_edit.replace(/\D/g, '');

                let formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                $(this).val(formattedNumber);


                const biaya = $('#biaya_edit').val().replace(/\D/g, '');
                const total_biaya = (Number(biaya));

                const total_item_edit = $('#total_item_edit');
                if(discount_item_edit === '') {
                    total_item_edit.val(total_biaya === 0 ? '' : total_biaya.toLocaleString('id-ID'));
                } else {
                    if(Number(digitsOnly) > 100) {
                        total_item_edit.val(total_biaya === 0 ? '' : (total_biaya - Number(digitsOnly)).toLocaleString('id-ID'));
                        $('#total_after_discount_edit_edit').text('').parent().css('display', 'none');
                    } else {
                        const total_after_discount_edit = total_biaya * (Number(digitsOnly) / 100);
                        if(Number(digitsOnly) === 100) {
                            total_item_edit.val(total_biaya === 0 ? '' : total_biaya.toLocaleString('id-ID'));
                            $('#total_after_discount_edit').text('').parent().css('display', 'none');
                        } else {
                            $('#total_after_discount_edit').text(total_after_discount_edit.toLocaleString('id-ID')).parent().css('display', 'inline');
                            total_item_edit.val(total_biaya === 0 ? '' : (total_biaya - total_after_discount_edit).toLocaleString('id-ID'));
                        }
                    }
                }
            });

            $('#discount').on('input', function(e) {
                const value = $(this).val();
                let amount = $('#sub_total').text().replace(/\./g, '');
                const amount_value = parseInt(amount, 10);
                const discount = value === '' ? 0 : value;

                // Hapus semua karakter selain digit
                let digitsOnly = discount === 0 ? 0 : discount.replace(/\D/g, '');

                let netto = amount_value - (digitsOnly === 0 ? 0 : parseInt(digitsOnly, 10));

                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                let formattedNumber = '';
                // Format angka dengan menambahkan titik setiap 3 digit dari kanan ke kiri
                if(value !== '') {
                    formattedNumber = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                // Update nilai input dengan angka yang diformat
                $(this).val(formattedNumber);

                // Update nilai input dengan angka yang diformat
                $(this).val(formattedNumber);
                // $('#dp').trigger('input');
                const totalValue = value === '' ? amount_value.toLocaleString('id-ID') :  parseInt(netto, 10).toLocaleString('id-ID');
                console.log(totalValue);
                $('#total').text((totalValue));
            });

            $('#site_id').select2();

            $('#estimate_service_done').datepicker({
                todayHighlight: true,
                endDate: '+30d',
                datesDisabled: '+30d',
                autoclose: true,
                format: 'yyyy-mm-dd'
            }).datepicker("setDate",'+4d').on('changeDate', function(e) {
                // Dapatkan nilai dari datepicker
                const selectedDate = $('#estimate_service_done').datepicker('getDate');
                if (selectedDate) {
                    let newDate = moment(selectedDate).add(1, 'days');
                    $('#estimate_take_item').val(newDate.format('YYYY-MM-DD')).trigger('changeDate');
                }
            }).trigger('changeDate');
            $('#estimate_take_item').datepicker({
                todayHighlight: true,
                endDate: '+30d',
                datesDisabled: '+30d',
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
            // $('#city_id').select2();
            // $('.select2').select2({
            //     "language": {
            //         "noResults": function(){
            //             return "Nenhum registro encontrado.";
            //         }
            //     }
            // });

            let defaultSite = '{{ session('user.default_site.0') }}';
            if (defaultSite) {
                $('#site_id').val(defaultSite).trigger('change');
            }

        });
    </script>

@endsection
