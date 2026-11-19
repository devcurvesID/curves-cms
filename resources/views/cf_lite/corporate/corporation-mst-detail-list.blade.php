@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/corporate/corporate-mst-list')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.group_list')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        @if(count($groups) > 0)
            @foreach($groups as $group)
                <div class="col-md-3">
                    <div class="thumbnail">
                        <h3>{{$group->group_name}}</h3><br/>
                        @foreach($membercorps as $membercorp)
                            @if($membercorp->sequence_group == $group->sequence_group)
                                <div class="row">
                                    <div class="media col-md-12">
                                      <div class="media-left col-md-2">
                                        @if(!empty($membercorp->membercorporation_user->user_memberpersonal->photo))
                                            <img class="img-circle" src="{{asset('images/member_photos/'.$membercorp->membercorporation_user->user_memberpersonal->photo)}}" style="width: 60px; height: 60px; object-fit: cover;margin-left: 10px;margin-bottom: 10px;">
                                        @else
                                            <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 60px; height: 60px; object-fit: cover;margin-left: 10px;margin-bottom: 10px;">
                                        @endif
                                      </div>
                                      <div class=" col-md-8">
                                        <h4 class="media-heading">
                                            {{$membercorp->membercorporation_user->name}}
                                        </h4>
                                      </div>
                                      <div class="col-md-2">
                                          {!! Form::open(['method' => 'DELETE' , 'style'=>'display:inline-block' , 'action' => ['CorporationController@delete_member_corporation' ]])!!}{!! Form::hidden('id_mc',$membercorp->user_id) !!}{!! Form::submit('X',['class' => 'btn btn-danger' , 'onclick' => 'return confirm("Anda yakin akan mengeluarkan anggota dari grup ini?")']) !!} {!! Form::close() !!}
                                      </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right paging">
                {{$groups->links()}}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
@endsection