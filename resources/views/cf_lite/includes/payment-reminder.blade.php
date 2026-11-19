<!-- due payments -->
<div class="ba-payment-wrap">
    <div class="ba-payment popup-ani">
        <div class="payment-header">Payment Reminder <span class="closer">[x]</span></div>
        <ul class="navHeader">
            <li class="active tableTab" data-id="upcoming">Upcoming Dues</li>
            <li class="tableTab" data-id="pastdues">Past Dues</li>
            <li class="tableTab" data-id="inactivemembers">Inactive Member Dues</li>
        </ul>
        <div class="table-responsive" id="scrollTable">
            <!-- upcoming -->
            <table id="table-upcoming" class="table table-striped paymentTables">
                <tbody></tbody>
            </table>
            <!-- pastdues -->
            <table id="table-pastdues" class="table table-striped paymentTables">
                <tbody></tbody>
            </table>
            <!-- inactivemembers -->
            <table id="table-inactivemembers" class="table table-striped paymentTables">
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="ba-payment-fab">
        <div class="wrap">
            <div class="img-fab img"></div>
        </div>
    </div>
</div>
<!-- / due payments -->

<script>
   $(document).ready(function () {
    /** due payments **/
    $(".ba-payment-fab").click(function (e) {
        $('.ba-payment-fab .wrap').toggleClass("ani");
        $('.ba-payment').toggleClass("open");
        $('.ba-payment-fab .img-fab.img').toggleClass("close");
        $('#table-upcoming').fadeIn();
        $('#scrollTable').fadeIn();
        $('.navHeader').fadeIn();

        if($('.ba-staff-login').hasClass("open")){
            $('.ba-staff-login').removeClass("open")
            $('.ba-staff-login-fab .img-fab.img').removeClass("close")
        };
    });

    $('.closer').click(function () {
        $('.ba-payment').toggleClass("open");

        if($('.ba-staff-login').hasClass("open")){
            $('.ba-staff-login').removeClass("open")
            $('.ba-staff-login-fab .img-fab.img').removeClass("close")
        };
    });

    $('.tableTab').click(function () {
        var activeTable = $(this).attr('data-id');

        $('.tableTab').removeClass('active');
        $('.paymentTables').fadeOut();
        $(this).addClass('active');
        $('#table-'+ activeTable).fadeIn();

        var scrollable = document.getElementById('scrollTable');
        scrollable.scrollTop = 0;
    });

    /*** PULL DUE PAYMENTS FOR CURRENT CLUB ****/
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_wm_notif_token"]').attr('content')
        }
    });
    var type = 'GET';
    var my_url = '{{url('due-payments')}}';

    $.ajax({
        type: type,
        url: my_url,
        dataType: 'json',
        success: function (data) {
            //upcoming
            //$('#table-upcoming').fadeIn();
            $('#scrollTable').fadeOut();
            $('.navHeader').fadeOut();

            if(data.upcoming.length > 0){
                var paymentDue = '';
                for (const [key, value] of Object.entries(data.upcoming)) {

                    paymentDue += '<tr>';
                    paymentDue += '<td width="40"><a href="'+ value.billUrl +'"><img class="img-circle" src="'+ value.photo +'" title="Foto" style="width: 40px; height: 40px; object-fit: cover;"></a></td>';
                    paymentDue += '<td align="left">';

                    if(value.workout != ''){
                        paymentDue += '<a href="'+ value.billUrl +'"><b>' + value.name + '</b><span class="lastWO">Last Workout: '+ value.workout +'</span><br>';
                    }else{
                        paymentDue += '<a href="'+ value.billUrl +'"><b>' + value.name + '</b><span class="lastWO">Last Workout: -- </span><br>';
                    }
                    paymentDue += '<span>Due Date: ' + value.payment_date + '</span><br>';
                    paymentDue += '<span>Amount: ' + value.amount_due + '</span><br>';
                    paymentDue += '<span>Status: ' + value.payment_status + '</span><br>';
                    paymentDue += '<span>Member Status: ' + value.userStatus + '</span></a>';
                    paymentDue += '</td>';
                    paymentDue += '</tr>';
                }
                $('#table-upcoming tbody').append(paymentDue);
            }else{
                $('#table-upcoming tbody').append('<tr><td align="center"><i>No upcoming payment dues for the next 7 days</i></td></tr>');
            }

            //pastdues
            if(data.pastDues.length > 0){
                var paymentDue = '';
                for (const [key, value] of Object.entries(data.pastDues)) {

                    paymentDue += '<tr>';
                    paymentDue += '<td width="40"><a href="'+ value.billUrl +'"><img class="img-circle" src="'+ value.photo +'" title="Foto" style="width: 40px; height: 40px; object-fit: cover;"></a></td>';
                    paymentDue += '<td align="left">';

                    if(value.workout != ''){
                        paymentDue += '<a href="'+ value.billUrl +'"><b>' + value.name + '</b><span class="lastWO">Last Workout: '+ value.workout +'</span><br>';
                    }else{
                        paymentDue += '<a href="'+ value.billUrl +'"><b>' + value.name + '</b><span class="lastWO">Last Workout: -- </span><br>';
                    }
                    paymentDue += '<span>Due Date: ' + value.payment_date + '</span><br>';
                    paymentDue += '<span>Amount: ' + value.amount_due + '</span><br>';
                    paymentDue += '<span>Status: ' + value.payment_status + '</span><br>';
                    paymentDue += '<span>Member Status: ' + value.userStatus + '</span></a>';
                    paymentDue += '</td>';
                    paymentDue += '</tr>';
                }
                $('#table-pastdues tbody').append(paymentDue);
            }else{
                $('#table-pastdues tbody').append('<tr><td align="center"><i>No past payment dues</i></td></tr>');
            }

            //inactivemembers
            if(data.pastDuesInactive.length > 0){
                var paymentDue = '';
                for (const [key, value] of Object.entries(data.pastDuesInactive)) {

                    paymentDue += '<tr>';
                    paymentDue += '<td width="40"><a href="'+ value.billUrl +'"><img class="img-circle" src="'+ value.photo +'" title="Foto" style="width: 40px; height: 40px; object-fit: cover;"></a></td>';
                    paymentDue += '<td align="left">';

                    if(value.workout != ''){
                        paymentDue += '<a href="'+ value.billUrl +'"><b>' + value.name + '</b><span class="lastWO">Last Workout: '+ value.workout +'</span><br>';
                    }else{
                        paymentDue += '<a href="'+ value.billUrl +'"><b>' + value.name + '</b><span class="lastWO">Last Workout: -- </span><br>';
                    }
                    paymentDue += '<span>Due Date: ' + value.payment_date + '</span><br>';
                    paymentDue += '<span>Amount: ' + value.amount_due + '</span><br>';
                    paymentDue += '<span>Status: ' + value.payment_status + '</span><br>';
                    paymentDue += '<span>Member Status: ' + value.userStatus + '</span></a>';
                    paymentDue += '</td>';
                    paymentDue += '</tr>';
                }
                $('#table-inactivemembers tbody').append(paymentDue);
            }else{
                $('#table-inactivemembers tbody').append("<tr><td align=\"center\"><i>No inactive member's payment dues</i></td></tr>");
            }
        },
        error: function (data) {
            console.log('Error: ', data);
        }
    });
  });
</script>
