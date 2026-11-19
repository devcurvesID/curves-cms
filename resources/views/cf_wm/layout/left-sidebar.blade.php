<meta name="_mtoken" content="{!! csrf_token() !!}"/>
<div style="margin: 0px auto">
    <center>
        <div style="margin-top: 50px; color: rebeccapurple;">
            <div class="form-group">
                @if(!empty(Session::get('staff_session')))
                    {!! Form::label('active_staff', trans('labels.active_staff').': '.Session::get('staff_session_name'), ['class' => 'control-label', 'style' => 'font-size: 18px;']) !!}
                @else
                    {!! Form::label('active_staff', trans('labels.active_staff').': '.Auth::user()->name, ['class' => 'control-label', 'style' => 'font-size: 18px;']) !!}
                @endif
            </div>
            <div class="form-group">
                {!! Form::label('staff_session', trans('labels.change_active_staff').': ', ['class' => 'control-label pull-left']) !!}
                {!! Form::select('staff_session', $our_staffs, !empty(Session::get('staff_session')) ? Session::get('staff_session') : Auth::id(), ['class' => 'form-control', 'placeholder' => trans('labels.choose_staff'), 'style' => 'color: rebeccapurple;']) !!}
            </div>
        </div>

        <div class="a-close">
            <a href="{{url('cf-wm/wm-member-scan')}}" title="@lang('labels.back_to_front_page')" style="color: deeppink; font-size: 14px;"><span style="font-size: 13px; vertical-align: middle;">❤</span> <span class="fa fa-home"></span><span style="margin: 0 10px;">@lang('labels.front_page')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
        </div>

        <div class="a-close">
            <a href="{{ url('cf-wm/logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" style="font-size: 32px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;">@lang('labels.system_logout')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
        </div>
        <form id="logout-form" action="{{ url('cf-wm/logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </center>
</div>

<script>
    $(document).ready(function () {
        $('select[name="staff_session"]').on('change', function () {
            var url = "{{url('cf-lite')}}/";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_mtoken"]').attr('content')
                }
            });

            var formData = {
                staff_id: $(this).val()
            };

            var type = 'POST';
            var my_url = url + 'set-staff-session';

            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    window.location.reload();
                },
                error: function (data) {
                    console.log('Error: ', data);
                }
            });
        });
    });
</script>