@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.list_accounting_account')</h2>
            </center>
        </div>
    </div>
    @if(\Illuminate\Support\Facades\Auth::user()->role_id < 4)
        <hr>
        <div class="row">
            @if(count($accounts) > 0)
                @foreach($accounts as $account)
                    <div class="col-md-3">
                        <div class="thumbnail" style="height: 360px;">
                            <img src="{{asset('images/icons/female_avatar.png')}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                            <h4 style="text-align: center;">{{$account->name}}</h4>
                            <ul style="height: 80px; list-style: none; overflow-y: scroll; background-color: #EFEFEF; padding-top: 10px; padding-bottom: 10px;">
                                @foreach($account->accounting_jubilant as $jubilant)
                                    <li>({{ucwords($jubilant->jubilant_name)}})</li>
                                @endforeach
                            </ul>
                            <a href="{{url('cf-lite/setting/accounting-account/'.$account->id.'/setting-list-jubilant')}}">@lang('labels.setting_accounting_account') <span class="fa fa-chevron-right"></span></a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endif
@endsection

@section('other-script')
@endsection