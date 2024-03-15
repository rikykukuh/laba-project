@extends('layouts.AdminLTE.index')

@section('icon_page', 'plus')

@section('title', 'Add Order')

@section('layout_css')
    <!-- blueimp Gallery styles -->
    <link rel="stylesheet" href="https://blueimp.github.io/Gallery/css/blueimp-gallery.min.css" />
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="{{ asset('/plugins/blueimp/css/jquery.fileupload.css') }}" />
    <link rel="stylesheet" href="{{ asset('/plugins/blueimp/css/jquery.fileupload-ui.css') }}" />
@endsection
@section('menu_pagina')

	<li role="presentation">
		<a href="{{ route('orders.index') }}" class="link_menu_page">
			<i class="fa fa-shopping-basket"></i> Orders
		</a>
	</li>

@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Add Order</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse Form Order">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
					 <form id="fileupload" action="{{ route('orders.store') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">
                                    <label for="client_id">Client</label>
                                    <select name="client_id" id="client_id" class="form-control" data-placeholder="Choose Client" required>
                                        <option disabled selected> -- Choose Client -- </option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}"> {{ $client->name }} </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('client_id'))
                                        <span class="help-block">
                                             <strong>{{ $errors->first('client_id') }}</strong>
                                         </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('picked_by') ? 'has-error' : '' }}">
                                    <label for="picked_by">Picked By</label>
                                    <input type="text" name="picked_by" id="picked_by" class="form-control" placeholder="Picked By" required value="{{ old('picked_by') }}" autofocus>
                                    @if($errors->has('picked_by'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('picked_by') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('picked_at') ? 'has-error' : '' }}">
                                    <label for="picked_at">Picked At</label>
                                    <input type="date" name="picked_at" id="picked_at" class="form-control" placeholder="Picked By" required value="{{ old('picked_at') }}">
                                    @if($errors->has('picked_at'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('picked_at') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" data-placeholder="Choose Status" required>
                                        @foreach($statuses as $status)
                                            <option value="{{ $loop->index }}"> {{ $status }} </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('status'))
                                        <span class="help-block">
                                             <strong>{{ $errors->first('status') }}</strong>
                                         </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group {{ $errors->has('note') ? 'has-error' : '' }}">
                                    <label for="note">Note</label>
                                    <textarea name="note" placeholder="Note" id="note" class="form-control" required>{{ old('note') }}</textarea>
                                    @if($errors->has('note'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('note') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- <div class="col-lg-12"> --}}
                            {{--     <div class="form-group"> --}}
                            {{--         <label for="order_item_photo">Photo</label> --}}
                            {{--         <input type="file" id="order_item_photo" class="form-control order_item_photo" name="files" multiple> --}}
                            {{--     </div> --}}
                            {{-- </div> --}}

                            {{-- <div class="col-lg-12"> --}}
                            {{--     <div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}"> --}}
                            {{--         <label for="total">Total</label> --}}
                            {{--         <input type="number" name="total" id="total" class="form-control" placeholder="Total" required value="{{ old('total') }}" > --}}
                            {{--         @if($errors->has('total')) --}}
                            {{--             <span class="help-block"> --}}
                            {{--                 <strong>{{ $errors->first('total') }}</strong> --}}
                            {{--             </span> --}}
                            {{--         @endif --}}
                            {{--     </div> --}}
                            {{-- </div> --}}
                            {{-- <div class="col-lg-12"> --}}
                            {{--     <div class="form-group {{ $errors->has('uang_muka') ? 'has-error' : '' }}"> --}}
                            {{--         <label for="uang_muka">Uang Muka</label> --}}
                            {{--         <input type="number" name="uang_muka" id="uang_muka" class="form-control" placeholder="Uang Muka" required value="{{ old('uang_muka') }}"> --}}
                            {{--         @if($errors->has('uang_muka')) --}}
                            {{--             <span class="help-block"> --}}
                            {{--                 <strong>{{ $errors->first('uang_muka') }}</strong> --}}
                            {{--             </span> --}}
                            {{--         @endif --}}
                            {{--     </div> --}}
                            {{-- </div> --}}
                            {{-- <div class="col-lg-12"> --}}
                            {{--     <div class="form-group {{ $errors->has('payment') ? 'has-error' : '' }}"> --}}
                            {{--         <label for="payment">Payment</label> --}}
                            {{--         <input type="number" name="payment" id="payment" class="form-control" placeholder="Payment" required value="{{ old('payment') }}" > --}}
                            {{--         @if($errors->has('payment')) --}}
                            {{--             <span class="help-block"> --}}
                            {{--                 <strong>{{ $errors->first('payment') }}</strong> --}}
                            {{--             </span> --}}
                            {{--         @endif --}}
                            {{--     </div> --}}
                            {{-- </div> --}}
                            {{-- <div class="col-lg-12"> --}}
                            {{--     <div class="form-group {{ $errors->has('number_ticket') ? 'has-error' : '' }}"> --}}
                            {{--         <label for="number_ticket">No Tiket</label> --}}
                            {{--         <input type="text" name="number_ticket" id="number_ticket" class="form-control" placeholder="No Tiket" required value="{{ old('number_ticket') }}" > --}}
                            {{--         @if($errors->has('number_ticket')) --}}
                            {{--             <span class="help-block"> --}}
                            {{--                 <strong>{{ $errors->first('number_ticket') }}</strong> --}}
                            {{--             </span> --}}
                            {{--         @endif --}}
                            {{--     </div> --}}
                            {{-- </div> --}}
                            {{-- <div class="col-lg-12"> --}}
                            {{--     <div class="form-group {{ $errors->has('due_date') ? 'has-error' : '' }}"> --}}
                            {{--         <label for="due_date">Jatuh Tempo</label> --}}
                            {{--         <input type="date" name="due_date" id="due_date" class="form-control" placeholder="Jatuh Tempo" required value="{{ old('due_date') }}" > --}}
                            {{--         @if($errors->has('due_date')) --}}
                            {{--             <span class="help-block"> --}}
                            {{--                 <strong>{{ $errors->first('due_date') }}</strong> --}}
                            {{--             </span> --}}
                            {{--         @endif --}}
                            {{--     </div> --}}
                            {{-- </div> --}}
                            {{-- <div class="col-lg-12"> --}}
                            {{--     <div class="form-group {{ $errors->has('sisa_pembayaran') ? 'has-error' : '' }}"> --}}
                            {{--         <label for="sisa_pembayaran">Sisa Pembayaran</label> --}}
                            {{--         <input type="number" name="sisa_pembayaran" id="sisa_pembayaran" class="form-control" placeholder="Sisa Pembayaran" required value="{{ old('sisa_pembayaran') }}" > --}}
                            {{--         @if($errors->has('sisa_pembayaran')) --}}
                            {{--             <span class="help-block"> --}}
                            {{--                 <strong>{{ $errors->first('sisa_pembayaran') }}</strong> --}}
                            {{--             </span> --}}
                            {{--         @endif --}}
                            {{--     </div> --}}
                            {{-- </div> --}}

                            <!-- Redirect browsers with JavaScript disabled to the origin page -->
                            <noscript>
                                <input type="hidden"
                                       name="redirect"
                                       value="https://blueimp.github.io/jQuery-File-Upload/" />
                            </noscript>
                            <div>
                                <div class="col-lg-12">
                                    <span class="btn btn-success add-item" style="margin-right: 15px;">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        <span>Add item</span>
                                    </span>

                                    <!-- The fileinput-button span is used to style the file input field as button -->
                                     {{-- <span class="btn btn-info fileinput-button" style="margin-right: 15px;"> --}}
                                     {{--     <i class="glyphicon glyphicon-file"></i> --}}
                                     {{--     <span>Add files...</span> --}}
                                     {{--     <input type="file" id="order_item_photo" name="files" multiple /> --}}
                                     {{-- </span> --}}

                                    {{-- <button type="button" class="btn btn-danger delete"> --}}
                                    {{--     <i class="glyphicon glyphicon-trash"></i> --}}
                                    {{--     <span>Delete selected</span> --}}
                                    {{-- </button> --}}
                                    {{-- <input type="checkbox" class="toggle" /> --}}

                                    <!-- The global file processing state -->
                                    {{-- <span class="fileupload-process"></span> --}}
                                </div>
                                {{-- <!-- The global progress state --> --}}
                                {{-- <div class="col-lg-5 fileupload-progress fade"> --}}
                                {{--     <!-- The global progress bar --> --}}
                                {{--     <div class="progress progress-striped active" --}}
                                {{--          role="progressbar" --}}
                                {{--          aria-valuemin="0" --}}
                                {{--          aria-valuemax="100"> --}}
                                {{--         <div class="progress-bar progress-bar-success" --}}
                                {{--              style="width: 0%;"></div> --}}
                                {{--     </div> --}}
                                {{--     <!-- The extended global progress state --> --}}
                                {{--     <div class="progress-extended"> </div> --}}
                                {{-- </div> --}}
                                <div class="col-lg-12" style="margin-bottom: 15px;">
                                    <hr>
                                </div>
                            </div>

                            <div class="field-wrapper"></div>

                            {{-- <div class="col-md-12"> --}}
                            {{--     <!-- The table listing the files available for upload/download --> --}}
                            {{--     <table role="presentation" class="table table-striped"> --}}
                            {{--         <thead> --}}
                            {{--             <tr> --}}
                            {{--                 <th>Image</th> --}}
                            {{--                 <th>Filename</th> --}}
                            {{--                 <th>Filesize</th> --}}
                            {{--                 <th>Action</th> --}}
                            {{--             </tr> --}}
                            {{--         </thead> --}}
                            {{--         <tbody class="files"></tbody> --}}
                            {{--         <tfoot> --}}
                            {{--         <tr> --}}
                            {{--             <th>Image</th> --}}
                            {{--             <th>Filename</th> --}}
                            {{--             <th>Filesize</th> --}}
                            {{--             <th>Action</th> --}}
                            {{--         </tr> --}}
                            {{--         </tfoot> --}}
                            {{--     </table> --}}
                            {{-- </div> --}}

                            {{-- <div class="col-lg-12"> --}}
                            {{--     <div class="panel panel-default"> --}}
                            {{--         <div class="panel-heading"> --}}
                            {{--             <h3 class="panel-title">Demo Notes</h3> --}}
                            {{--         </div> --}}
                            {{--         <div class="panel-body"> --}}
                            {{--             <ul> --}}
                            {{--                 <li> --}}
                            {{--                     The maximum file size for uploads in this demo is --}}
                            {{--                     <strong>999 KB</strong> (default file size is unlimited). --}}
                            {{--                 </li> --}}
                            {{--                 <li> --}}
                            {{--                     Only image files (<strong>JPG, GIF, PNG</strong>) are allowed in --}}
                            {{--                     this demo (by default there is no file type restriction). --}}
                            {{--                 </li> --}}
                            {{--                 <li> --}}
                            {{--                     Uploaded files will be deleted automatically after --}}
                            {{--                     <strong>5 minutes or less</strong> (demo files are stored in --}}
                            {{--                     memory). --}}
                            {{--                 </li> --}}
                            {{--                 <li> --}}
                            {{--                     You can <strong>drag & drop</strong> files from your desktop --}}
                            {{--                     on this webpage (see --}}
                            {{--                     <a href="https://github.com/blueimp/jQuery-File-Upload/wiki/Browser-support">Browser support</a>). --}}
                            {{--                 </li> --}}
                            {{--                 <li> --}}
                            {{--                     Please refer to the --}}
                            {{--                     <a href="https://github.com/blueimp/jQuery-File-Upload">project website</a> --}}
                            {{--                     and --}}
                            {{--                     <a href="https://github.com/blueimp/jQuery-File-Upload/wiki">documentation</a> --}}
                            {{--                     for more information. --}}
                            {{--                 </li> --}}
                            {{--                 <li> --}}
                            {{--                     Built with the --}}
                            {{--                     <a href="https://getbootstrap.com/">Bootstrap</a> CSS framework --}}
                            {{--                     and Icons from <a href="https://glyphicons.com/">Glyphicons</a>. --}}
                            {{--                 </li> --}}
                            {{--             </ul> --}}
                            {{--         </div> --}}
                            {{--     </div> --}}
                            {{-- </div> --}}

                             <div class="col-lg-12">
                                 <button type="submit" class="btn btn-primary pull-right" style="margin-right: 15px;">
                                     <i class="fa fa-fw fa-save"></i>
                                     <span>Save</span>
                                 </button><a href="{{ route('orders.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Back to Orders</a>
                             </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('layout_js')
    <script>
        const imageFiles = [];

        let tableImageHtml = `
            <table role="presentation" class="table table-striped table-bordered table-hover">
                 <thead>
                     <tr>
                         <th width="300">Image</th>
                         <th width="300">Filename</th>
                         <th class="text-center">Filesize</th>
                         <!-- <th>Action</th> -->
                     </tr>
                 </thead>
                 <tbody class="content-image"></tbody>
                 <tfoot>
                     <tr>
                         <th width="300">Image</th>
                         <th width="300">Filename</th>
                         <th class="text-center">Filesize</th>
                         <!-- <th>Action</th> -->
                     </tr>
                 </tfoot>
             </table>
        `;

        function uuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                let r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        function handleImageUpload(element, counter) {
            const files = $(element)[0].files;
            if(files.length === 0) return;

            const dataFile = []
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const ext = file.name.split('.').pop().toLowerCase();
                if ($.inArray(ext, ['png', 'jpg', 'jpeg']) !== -1) {
                    dataFile.push(file);
                    imageFiles[counter]['item-' + counter].push(file);
                }
            }

            console.log(imageFiles);

            showImageAsTable(element, counter);
        }

        function showImageAsTable(element, counter) {
            console.log('Total gambar: ', imageFiles[counter]['item-' + counter]);
            const tableImage = $(element).parent().parent().parent().find('.table-image');
            tableImage.html(tableImageHtml);
            const contentImage = $(tableImage).find('.content-image');

            console.log('nih', imageFiles[counter]['item-' + counter])

            for (let i = 0; i < imageFiles[counter]['item-' + counter].length; i++) {
                const file = imageFiles[counter]['item-' + counter][i];
                console.log(i);
                console.log('NAH!!!', file);
                const ext = file.name.split('.').pop().toLowerCase();
                if ($.inArray(ext, ['png', 'jpg', 'jpeg']) !== -1) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        contentImage.append(`
                            <tr>
                                <td>
                                    <input type="hidden" name="item_images[${counter}][]" value="${e.target.result}" class="form-control item-images"/>
                                    <input type="hidden" name="file_name[${counter}][]" value="${file.name}" class="form-control item-images"/>
                                    <img src="${e.target.result}" alt="${file.name}" title="${file.name}"  width="80" height="60"/>
                                </td>
                                <td>
                                    <p style="margin-top:15px;">
                                        ${file.name}
                                    </p>
                                </td>
                                <td class="text-center">
                                    <p style="margin-top:15px;">
                                        ${file.size}
                                    </p>
                                </td>
                                <!-- <td>
                                    <button onclick="removeImage(event, this, ${counter}, ${i})" type="button" class="btn btn-danger btn-sm" title="Remove image">
                                        <span class="btn-label"><i class="fa fa-eraser"></i></span>
                                    </button>
                                </td> -->
                            </tr>
                        `);
                    }
                    reader.readAsDataURL(file);
                }
            }
        }

        function removeImage(e, el, index, element) {
            if (confirm("Are you sure you want to DELETE this image?") === true) {
                // const tableImage = $(el).parents().find('.field-wrapper');
                // console.log(tableImage)
                // // tableImage.html(tableImageHtml);
                // const contentImage = $(tableImage).find('.content-image');
                // console.log(contentImage)
                // tableImage.empty();

                $(el).parent().parent().remove();
                console.log('remove', imageFiles[index]['item-' + index])
                imageFiles[index]['item-' + index].splice(element, 1);
                // showImageAsTable($('.files'), index);
            }
        }

        $(function() {
            $('.select2').select2({
                "language": {
                    "noResults": function(){
                        return "Nenhum registro encontrado.";
                    }
                }
            });

            /**/
            let maxField = 10; //Input fields increment limitation
            let addButton = $('.add-item'); //Add button selector
            let wrapper = $('.field-wrapper'); //Input field wrapper
            let fieldHTML = (counter, imageFileCount) => {
                return `
                    <div>
                        <div class="col-lg-6" style="margin-bottom: 15px;">
                            <div class="form-group">
                                <label for="item-type-id-${counter}">Item Type</label>
                                <select name="item_type[${imageFileCount}]" id="item-type-id-${counter}" class="form-control" required>
                                <?php foreach($item_types as $item_type): ?>
                    <option value="<?= $item_type->id; ?>"> <?= $item_type->name; ?> </option>
                                <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-lg-6" style="margin-bottom: 15px;">
                <div class="form-group">
                    <label for="total-${counter}">Total Item</label>
                                <input type="number" name="total[${imageFileCount}]" id="total-${counter}" class="form-control" placeholder="Total Item" required>
                            </div>
                        </div>

                        <div class="col-lg-12" style="margin-bottom: 15px;">
                            <span class="btn btn-info fileinput-button button-${counter}" style="margin-right: 15px;">
                                <i class="glyphicon glyphicon-file"></i>
                                <span>Add files...</span>
                                <input type="file" name="files" class="files" multiple  accept="image/png, image/jpg, image/jpeg" onchange="handleImageUpload(this, ${imageFileCount})" />
                            </span>
                            <a href="javascript:void(0);" class="btn btn-danger remove-button" data-element="${imageFileCount}">
                                <span class="btn-label"><i class="glyphicon glyphicon-erase"></i></span> Remove Item
                            </a>
                        </div>
                        <div class="col-lg-12 table-image"></div>
                        <div class="col-lg-12" style="margin-bottom: 15px;">
                            <hr>
                        </div>
                    </div>
                `
            };
            let counter = 1; //Initial field counter is 1

            // Once add button is clicked
            $(addButton).click(function(){
                //Check maximum number of input fields
                if(counter < maxField){
                    imageFiles.push({[`item-${$('.files').length}`] : []});
                    console.log(imageFiles);
                    counter++; //Increase field counter
                    $(wrapper).append(fieldHTML(counter, $('.files').length)); //Add field html
                }else{
                    alert('A maximum of '+maxField+' fields are allowed to be added. ');
                }
            });

            // Once remove button is clicked
            $(wrapper).on('click', '.remove-button', function(e){
                e.preventDefault();
                if (confirm("Are you sure you want to DELETE this item?") === true) {
                    const element = parseInt($(this).data("element"), 10);
                    imageFiles.splice(element, 1);
                    $(this).parent().parent('div').remove(); //Remove field html
                    counter--; //Decrease field counter
                }
            });
        });

    </script>

@endsection
