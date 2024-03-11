@extends('layouts.AdminLTE.index')

@section('icon_page', 'plus')

@section('title', 'Add Order')

@section('layout_css')
    <!-- blueimp Gallery styles -->
    <link rel="stylesheet" href="https://blueimp.github.io/Gallery/css/blueimp-gallery.min.css" />
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="{{ asset('/plugins/blueimp/css/jquery.fileupload.css') }}" />
    <link rel="stylesheet" href="{{ asset('/plugins/blueimp/css/jquery.fileupload-ui.css') }}" />
    <!-- CSS adjustments for browsers with JavaScript disabled -->
    <noscript>
        <link rel="stylesheet" href="{{ asset('/plugins/blueimp/css/jquery.fileupload-noscript.css') }}" />
    </noscript>
    <noscript>
        <link rel="stylesheet" href="{{ asset('/plugins/blueimp/css/jquery.fileupload-ui-noscript.css') }}" />
    </noscript>
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
                                <div class="col-lg-7" style="margin-bottom: 30px;">
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
                                 <button type="submit" class="btn btn-primary pull-right start" style="margin-right: 15px;">
                                     <i class="glyphicon glyphicon-upload"></i>
                                     <span>Start upload</span>
                                 </button>
                                 <button type="reset" class="btn btn-warning pull-right cancel" style="margin-right: 15px;">
                                     <i class="glyphicon glyphicon-ban-circle"></i>
                                     <span>Cancel upload</span>
                                 </button>
                                 <a href="{{ route('orders.index') }}" class="btn btn-default pull-right" style="margin-right: 15px;"><i class="fa fa-fw fa-close"></i> Back to Orders</a>
                             </div>
                        </div>
                    </form>
                    </div>
                    <!-- The blueimp Gallery widget -->
                    <div id="blueimp-gallery"
                         class="blueimp-gallery blueimp-gallery-controls"
                         aria-label="image gallery"
                         aria-modal="true"
                         role="dialog"
                         data-filter=":even">
                        <div class="slides" aria-live="polite"></div>
                        <h3 class="title"></h3>
                        <a class="prev"
                           aria-controls="blueimp-gallery"
                           aria-label="previous slide"
                           aria-keyshortcuts="ArrowLeft"></a>
                        <a class="next"
                           aria-controls="blueimp-gallery"
                           aria-label="next slide"
                           aria-keyshortcuts="ArrowRight"></a>
                        <a class="close"
                           aria-controls="blueimp-gallery"
                           aria-label="close"
                           aria-keyshortcuts="Escape"></a>
                        <a class="play-pause"
                           aria-controls="blueimp-gallery"
                           aria-label="play slideshow"
                           aria-keyshortcuts="Space"
                           aria-pressed="false"
                           role="button"></a>
                        <ol class="indicator"></ol>
                    </div>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('layout_js')
    <!-- The template to display files available for upload -->
    <script id="template-upload" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade{%=o.options.loadImageFileTypes.test(file.type)?' image':''%}">
            <td>
                <span class="preview"></span>
            </td>
            <td>
                <p class="name">{%=file.name%}</p>
                <strong class="error text-danger"></strong>
            </td>
            <td>
                <p class="size">Processing...</p>
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
            </td>
            <td>
                {% if (!o.options.autoUpload && o.options.edit && o.options.loadImageFileTypes.test(file.type)) { %}
                <button class="btn btn-success edit" data-index="{%=i%}" disabled>
                    <i class="glyphicon glyphicon-edit"></i>
                    <span>Edit</span>
                </button>
                {% } %}
                {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled style="display:none;">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
                {% } %}
                {% if (!i) { %}
                <button class="btn btn-warning cancel" style="display:none;">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
                <button onclick="removeMetadata(event, this)" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}" type="button" class="btn btn-danger">
                    <span class="btn-label"><i class="fa fa-eraser"></i></span>
                    Remove
                </button>
                {% } %}
            </td>
        </tr>
        {% } %}
    </script>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade{%=file.thumbnailUrl?' image':''%}">
            <td>
                <span class="preview">
                    {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                    {% } %}
                </span>
            </td>
            <td>
                <p class="name">
                    {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                    {% } else { %}
                    <span>{%=file.name%}</span>
                    {% } %}
                </p>
                {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
            </td>
            <td>
                <span class="size">{%=o.formatFileSize(file.size)%}</span>
            </td>
            <td>
                {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}" {% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}' {% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
                {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
                {% } %}
            </td>
        </tr>
        {% } %}
    </script>

    <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
    <script src="{{ asset('/plugins/blueimp/js/vendor/jquery.ui.widget.js') }}"></script>
    <!-- The Templates plugin is included to render the upload/download listings -->
    <script src="https://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
    <!-- The Canvas to Blob plugin is included for image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
    <!-- blueimp Gallery script -->
    <script src="https://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="{{ asset('/plugins/blueimp/js/jquery.iframe-transport.js') }}"></script>
    <!-- The basic File Upload plugin -->
    <script src="{{ asset('/plugins/blueimp/js/jquery.fileupload.js') }}"></script>
    <!-- The File Upload processing plugin -->
    <script src="{{ asset('/plugins/blueimp/js/jquery.fileupload-process.js') }}"></script>
    <!-- The File Upload image preview & resize plugin -->
    <script src="{{ asset('/plugins/blueimp/js/jquery.fileupload-image.js') }}"></script>
    <!-- The File Upload audio preview plugin -->
    <script src="{{ asset('/plugins/blueimp/js/jquery.fileupload-audio.js') }}"></script>
    <!-- The File Upload video preview plugin -->
    <script src="{{ asset('/plugins/blueimp/js/jquery.fileupload-video.js') }}"></script>
    <!-- The File Upload validation plugin -->
    <script src="{{ asset('/plugins/blueimp/js/jquery.fileupload-validate.js') }}"></script>
    <!-- The File Upload user interface plugin -->
    <script src="{{ asset('/plugins/blueimp/js/jquery.fileupload-ui.js') }}"></script>
    <!-- The main application script -->
{{--    <script src="{{ asset('/plugins/blueimp/js/demo.js') }}"></script>--}}
    <script>
        function uuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                let r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
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
            let fieldHTML = `
                <div>
                    <div class="col-lg-6" style="margin-bottom: 15px;">
                        <div class="form-group>
                                <label for="item_type_id">&nbsp;</label>
                                <select name="item_type_id" id="item_type_id" class="form-control" required>
                                    <?php foreach($item_types as $item_type): ?>
                                        <option value="<?= $item_type->id; ?>"> <?= $item_type->name; ?> </option>
                                    <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6" style="margin-bottom: 15px;">
                                <div class="form-group>
                                    <label for="total">&nbsp;</label>
                                    <input type="number" name="total" id="total" class="form-control" placeholder="Total Item" required value="">
                                </div>
                            </div>

                            <div class="col-lg-6 fileupload-buttonbar" style="margin-bottom: 15px;">
                                <span class="btn btn-info fileinput-button button-${uuid()}" style="margin-right: 15px;">
                                      <i class="glyphicon glyphicon-file"></i>
                                      <span>Add files...</span>
                                      <input type="file" name="files" multiple />
                                  </span>

                                  <a href="javascript:void(0);" class="btn btn-danger remove-button">Remove Item</a>
                            </div>

                            <div class="col-lg-12" style="margin-bottom: 15px;">
                                <span class="fileupload-process"></span>

                              <!-- The global progress state -->
                                <div class="col-lg-5 fileupload-progress fade">
                                    <!-- The global progress bar -->
                                    <div class="progress progress-striped active"
                                         role="progressbar"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        <div class="progress-bar progress-bar-success"
                                             style="width: 0%;"></div>
                                    </div>
                                    <!-- The extended global progress state -->
                                    <div class="progress-extended"> </div>
                                </div>
                            </div>
                            <div class="col-lg-12" style="margin-bottom: 15px;">
                                <!-- The table listing the files available for upload/download -->
                                 <table role="presentation" class="table table-striped">
                                     <thead>
                                         <tr>
                                             <th>Image</th>
                                             <th>Filename</th>
                                             <th>Filesize</th>
                                             <th>Action</th>
                                         </tr>
                                     </thead>
                                     <tbody class="files"></tbody>
                                     <tfoot>
                                     <tr>
                                         <th>Image</th>
                                         <th>Filename</th>
                                         <th>Filesize</th>
                                         <th>Action</th>
                                     </tr>
                                     </tfoot>
                                 </table>
                            </div>
                        </div>
                        `;
            let x = 1; //Initial field counter is 1

            // Once add button is clicked
            $(addButton).click(function(){
                //Check maximum number of input fields
                if(x < maxField){
                    x++; //Increase field counter
                    $(wrapper).append(fieldHTML); //Add field html
                    console.log($('#fileupload').fileupload());
                }else{
                    alert('A maximum of '+maxField+' fields are allowed to be added. ');
                }
            });

            // Once remove button is clicked
            $(wrapper).on('click', '.remove-button', function(e){
                e.preventDefault();
                $(this).parent().parent('div').remove(); //Remove field html
                x--; //Decrease field counter
            });

            $('#fileupload').fileupload();

            {{--$('#fileupload').fileupload({--}}
            {{--    url: "{{ route('orders.store') }}",--}}
            {{--    downloadTemplateId: null,--}}
            {{--    acceptFileTypes: /.\.(gif|jpg|png|jpeg)$/i,--}}
            {{--    dataType: 'json',--}}
            {{--    imageOrientation: 1,--}}
            {{--    disableImageResize: true,--}}
            {{--    disableImageMetaDataSave: true,--}}
            {{--    imageMaxWidth: 1920,--}}
            {{--    imageMaxHeight: 1080,--}}
            {{--    sequentialUploads: true,--}}
            {{--    maxFileSize: 1073741824,--}}
            {{--    autoUpload: false,--}}
            {{--});--}}
        });

        function removeMetadata(e, el) {
            if (confirm("Are you sure you want to DELETE this metadata?") === true) {
                $(el).addClass('cancel');
            }
        }

    </script>

@endsection
