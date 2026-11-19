@extends('cf_lite.layouts.app')

@section('other-styles')
    <!-- Bootstrap -->
    <link href="{{asset('bootstrap-3_3_7/css/bootstrap.min.css')}}" rel="stylesheet">
    <script src="{{asset('bootstrap-3_3_7/js/bootstrap.min.js')}}"></script>

    <link rel="stylesheet" href="{{asset('css/hr/styles.css')}}">
    <style>
        #add-btn {
            background-color: #ff00fc;
            color: white;
            position: absolute;
            bottom: 20px;
            right: 20px;
            border-radius: 100%;
        }

        html, .container-fluid, #app {
            background-color: #424041;
        }

        .hr-frame {
            width: 100%;
            height: 250px;
            border-radius: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="btn btn-lg" id="add-btn"><span class="glyphicon glyphicon-plus"></span></div>
        <div class="row" id="member-grid">
            <div class="col-lg-2 col-md-4 col-sm-4" id="hr-col-1">
                <iframe class="hr-frame" id="frame-1" src="{{url('dev/ble-part')}}" scrolling="no"></iframe>
                <div>
                    <button class="reload-btn btn btn-default" id="reload-1"><span class="glyphicon glyphicon-refresh"></span></button>
                    <button class="delete-btn btn btn-default" id="delete-1"><span class="glyphicon glyphicon-trash"></span></button>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4" id="hr-col-2">
                <iframe class="hr-frame" id="frame-2" src="{{url('dev/ble-part')}}" scrolling="no"></iframe>
                <div>
                    <button class="reload-btn btn btn-default" id="reload-2"><span class="glyphicon glyphicon-refresh"></span></button>
                    <button class="delete-btn btn btn-default" id="delete-2"><span class="glyphicon glyphicon-trash"></span></button>
                </div>
            </div>
        </div>
        <input name="temp_counter" type="hidden" value="2">
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/hr/heartRateSensor.js')}}"></script>
    <script src="{{asset('js/hr/app.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#member-grid').on('click', '.reload-btn', function () {
                var id = $(this).attr('id').split('-').pop();
                document.getElementById('frame-' + id).contentWindow.location.reload();
            });

            $('#member-grid').on('click', '.delete-btn', function () {
                var id = $(this).attr('id').split('-').pop();
                $('#hr-col-' + id).remove();
            });

            $('#add-btn').on('click', function () {
                var counter = parseInt($('input[name="temp_counter"]').val()) + 1;
                var html = '<div class="col-lg-2 col-md-4 col-sm-4" id="hr-col-' + counter + '">';
                html += '<iframe class="hr-frame" id="frame-' + counter + '" src="{{url('dev/ble-part')}}" scrolling="no"></iframe>';
                html += '<div>' +
                    '<button class="reload-btn btn btn-default" id="reload-' + counter + '"><span class="glyphicon glyphicon-refresh"></span></button>' +
                    '<button class="delete-btn btn btn-default" id="delete-' + counter + '"><span class="glyphicon glyphicon-trash"></span></button>' +
                    '</div>';
                html += '</div>';
                $('#member-grid').append(html);

                $('input[name="temp_counter"]').val(counter);
            });
        });
    </script>
@endsection
