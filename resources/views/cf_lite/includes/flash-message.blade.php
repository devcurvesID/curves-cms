@if(Session::has('flash_message')):
    <div class="alert alert-success {{Session::has('penting') ? 'alert-important' : ''}}" id="flash-alert-message">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <center>{{Session::get('flash_message')}}</center>
    </div>
@endif
