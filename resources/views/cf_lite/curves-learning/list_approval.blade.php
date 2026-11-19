@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        #data-table th {
            color: #ff1493;
        }

        table th {
            color: #ff1493;
        }

        table, th {
            text-align: center;
        }

        .pagination > .active > a,
        .pagination > .active > a:focus,
        .pagination > .active > a:hover, .pagination > .active > span,
        .pagination > .active > span:focus,
        .pagination > .active > span:hover {
            background-color: #ff1493;
            border-color: #ff1493;
        }

        section#staff-home {
            padding-bottom: 30px;
        }

        .approved_content_btn button {
            margin: 20px 0;
            background-color: #ff1493;
            color: #ffffff;
            transition: 0.2s;
            border-color: #ff1493;
        }

        .approved_content_btn button:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="col-xs-12">
            <left>
                <a href="{{url('cf-lite/curves-learning/choose-menus')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">@lang('labels.list') @lang('labels.approval')</h2>
            </center>
        </div>
        <div class="col-xs-12">
            {!! Form::open(['method' => 'PATCH', 'id' => 'form-bulk', 'action' => 'CurvesLearningController@update_bulk_content_approved']) !!}
            <hr/>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                    <thead>
                    <tr>
                        <th>@lang('labels.indexing')</th>
                        <th>@lang('labels.title')</th>
                        <th>@lang('labels.content')</th>
                        <th>@lang('labels.file')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($indexing = 1)
                    @foreach($contents as $content)
                        <tr>
                            <td>{{ $indexing }}</td>
                            <td>{{$content->title}}</td>
                            <td>{!! nl2br(e($content->content)) !!}</td>
                            <td>
                                @foreach($content->clmaterialcontent_clmaterialsuppot as $files)
                                    {{$files->file_name}}
                                    @if(!empty($files->file_name))
{{--                                        <img src="{{asset('images/product_images/'.$files->file_images)}}" style="width: 75px; height: 75px; object-fit: cover;">--}}
                                        <br>
                                    @else
{{--                                        <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 75px; height: 75px; object-fit: scale-down;">--}}
                                        <br>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                {!! Form::checkbox('content_check['.$content->id.']', $content->id, false, ['class' => 'content_check', 'id' => '$content_check'.$content->id]) !!}
                            </td>
                        </tr>
                        @php($indexing++)
                    @endforeach
                    </tbody>
                </table>
            </div>
             <div class="approved_content_btn">
                <button type="button" class="btn pull-right" id="approved">@lang('labels.submit')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </section>
@endsection
@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('js/dataTables.buttons.min.js')}}"></script>;
    <script src="{{asset('js/buttons.print.min.js')}}"></script>

    <script>
        $('#dataTable').DataTable({});
    </script>
    <script>
        $(document).ready(function () {
            $('#approved').on('click', function ($e) {
                var count = 0;
                $('.content_check').each(function ($q) {
                    if($(this).prop("checked") == true){
                        count++;
                    }
                })
                if (count > 0) {
                    $('#form-bulk').submit();
                } else {
                    alert('@lang('messages.no_file_checked')');
                }
            })
        });
    </script>
@endsection