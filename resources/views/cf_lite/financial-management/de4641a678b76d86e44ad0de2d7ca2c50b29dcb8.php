

<?php $__env->startSection('other-style'); ?>
    <link href="<?php echo e(asset('css/payment.css')); ?>" rel="stylesheet">
    <style>
        body{
            color: white;
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }
        table{
            color: #414042;
        }

        .vip-box, .vip-box-gold {
            width: 400px;
            height: 100px;
            padding: 5px;
            padding-left: 15px;
            padding-right: 15px;
            border: 2px solid deeppink;
            border-radius: 7px;
            color: deeppink;
        }

        .vip-box-gold {
            border-color: gold;
            color: darkgoldenrod;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title"><?php echo app('translator')->getFromJson('labels.payment_detail'); ?><br><small>(<?php echo app('translator')->getFromJson('labels.continue_membership'); ?>)</small></h2>
            </center>
        </div>
    </div>

    <?php echo $__env->make('cf_lite.includes.error-message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php if(Session::get('payment_type') == 'continue'): ?>
        <?php echo Form::open(['action' => ['PaymentController@store_continue_payment', $membership_type->id], 'method' => 'POST', 'id' => 'form-continue-payment']); ?>

        <div class="row">
            <div class="col-md-6">
                <div class="form-horizontal">
                    <div class="form-group">
                        <?php echo Form::label('fa_coach', trans('labels.fa_coach').': ', ['class' => 'control-label col-md-4', 'style' => 'color: deeppink; margin-left: 10px;']); ?>

                        <div class="col-md-6">
                            <?php if(!empty(Session::get('staff_session'))): ?>
                                <?php echo Form::select('fa_coach', $fa_coaches, Session::get('staff_session'), ['class' => 'form-control']); ?>

                            <?php else: ?>
                                <?php echo Form::select('fa_coach', $fa_coaches, Auth::id(), ['class' => 'form-control']); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <table class="member-detail">
                    <tr>
                        <td><?php echo app('translator')->getFromJson('labels.name'); ?></td>
                        <td>:</td>
                        <td><?php echo e($member->name); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo app('translator')->getFromJson('labels.payment_number'); ?></td>
                        <td>:</td>
                        <td>
                            <?php ($month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII')); ?>
                            <?php echo e(sprintf('%08d', $temp_auto_number->current_number)); ?>/PY/CIND/<?php echo e(sprintf('%03d', $member->club_id)); ?>/<?php echo e($month_roman[\Carbon\Carbon::now()->format('n')]); ?>/<?php echo e(\Carbon\Carbon::now()->format('Y')); ?>

                            <?php echo Form::hidden('payment_number', '/PY/CIND/'.sprintf('%03d', $member->club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y')); ?>

                        </td>
                    </tr>
                    <tr>
                        <td><?php echo app('translator')->getFromJson('labels.membership_type'); ?></td>
                        <td>:</td>
                        <td><?php echo e($membership_type->membership_type_name); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="member-detail">
                    <tr>
                        <td><?php echo app('translator')->getFromJson('labels.payment_category'); ?></td>
                        <td>:</td>
                        <td><?php echo app('translator')->getFromJson('labels.first_payment'); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo app('translator')->getFromJson('labels.date'); ?></td>
                        <td>:</td>
                        <td>
                            <?php echo Form::date('pay_date', \Carbon\Carbon::today(), ['class' => 'form-control']); ?>

                        </td>
                    </tr>
                    <tr>
                        <td><?php echo Form::label('payment_method', trans('labels.payment_method'), ['class' => 'control-label']); ?></td>
                        <td>:</td>
                        <td>
                            <?php if($membership_type->is_vip == 'general'): ?>
                                <?php echo Form::select('payment_method', $payment_methods, null, ['class' => 'form-control', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.payment_method').' --']); ?>

                            <?php elseif($membership_type->is_vip == 'vip'): ?>
                                <?php echo Form::select('payment_method', $payment_methods, '1', ['class' => 'form-control', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.payment_method').' --', 'readonly']); ?>

                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo Form::label('bank', trans('labels.bank'), ['class' => 'control-label']); ?></td>
                        <td>:</td>
                        <td>
                            <select name="bank" class="form-control select2" id="bank">
                                <option value=""><?php echo app('translator')->getFromJson('labels.choose'); ?> <?php echo app('translator')->getFromJson('labels.bank'); ?></option>
                                <?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($bank->id); ?>"><?php echo e($bank->bank_abv); ?> - <?php echo e($bank->bank_account_number); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="3"><?php echo app('translator')->getFromJson('labels.first_payment'); ?></th>
                            <th rowspan="2"><?php echo app('translator')->getFromJson('labels.rest_of_the_bill'); ?></th>
                        </tr>
                        <tr>
                            <th><?php echo app('translator')->getFromJson('labels.type'); ?></th>
                            <th><?php echo app('translator')->getFromJson('labels.duration'); ?></th>
                            <th><?php echo app('translator')->getFromJson('labels.fee'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo app('translator')->getFromJson('labels.service_fee'); ?></td>
                            <td>
                                <?php echo e($csf->sf_freq); ?>

                            </td>
                            <td>
                                <?php ($sf_amount = 0); ?>
                                <?php if(strtotime(\Carbon\Carbon::now()) > strtotime(\Carbon\Carbon::parse($member->user_memberstatus->thru_date))): ?>
                                    <?php ($sf_amount = $csf->sf_amount); ?>
                                <?php endif; ?>
                                <?php echo Form::text('sf_amount', $sf_amount, ['class' => 'form-control rupiah']); ?>

                            </td>
                            <td class="my_rupiah">0</td>
                        </tr>
                        <tr>
                            <td><?php echo app('translator')->getFromJson('labels.monthly_fee'); ?></td>
                            <td>
                                <?php echo e($csf->mf_freq); ?>

                                <?php if($membership_type->payment_type == 'Prepaid'): ?>
                                    <?php echo Form::hidden('mf_amount', ($csf->mf_amount * $membership_type->duration)); ?>

                                <?php else: ?>
                                    <?php echo Form::hidden('mf_amount', $csf->mf_amount); ?>

                                <?php endif; ?>
                            </td>
                            <td class="my_rupiah">
                                <?php if($membership_type->payment_type == 'Prepaid'): ?>
                                    <?php echo e($csf->mf_amount * $membership_type->duration); ?>

                                <?php else: ?>
                                    <?php echo e($csf->mf_amount); ?>

                                <?php endif; ?>
                            </td>
                            <td class="my_rupiah">
                                <?php if($membership_type->payment_type == 'Prepaid'): ?>
                                    0
                                <?php else: ?>
                                    <?php echo e(($membership_type->duration - 1) * $csf->mf_amount); ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                            if ($membership_type->payment_type == 'Prepaid'):
                                $subtotal = $sf_amount + ($csf->mf_amount * $membership_type->duration);
                            else:
                                $subtotal = $sf_amount + $csf->mf_amount;
                            endif;

                            if (!empty($csf->pre_pay_discount_value)):
                                $discount = $csf->pre_pay_discount_value;
                            else:
                                $discount = round($csf->pre_pay_discount * 0.01 * $sf_amount);
                            endif;
                         ?>
                        <tr>
                            <td><?php echo app('translator')->getFromJson('labels.discount'); ?></td>
                            <td>1&times;</td>
                            <td>
                                <?php if($membership_type->is_vip == 'general'): ?>
                                    <?php echo Form::text('discount', $discount, ['class' => 'form-control rupiah']); ?>

                                <?php elseif($membership_type->is_vip == 'vip'): ?>
                                    <?php echo Form::text('discount', $discount, ['class' => 'form-control rupiah', 'readonly']); ?>

                                <?php endif; ?>
                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td><?php echo app('translator')->getFromJson('labels.voucher_code'); ?></td>
                            <td>
                                <div class="input-group">
                                    <?php if($membership_type->is_vip == 'general'): ?>
                                        <?php echo Form::text('voucher_code', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert_voucher_code')]); ?>

                                    <?php elseif($membership_type->is_vip == 'vip'): ?>
                                        <?php echo Form::text('voucher_code', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert_voucher_code'), 'readonly']); ?>

                                    <?php endif; ?>
                                    <span class="input-group-btn">
                                            <button class="btn btn-default" id="check-voucher-btn" type="button"><?php echo app('translator')->getFromJson('labels.verify'); ?></button>
                                        </span>
                                </div>
                            </td>
                            <td>
                                <?php echo Form::text('voucher_value', '0', ['class' => 'form-control', 'readonly']); ?>

                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td colspan="2"><?php echo app('translator')->getFromJson('labels.total'); ?></td>
                            <td><?php echo Form::text('total', ($subtotal - $discount), ['class' => 'form-control rupiah', 'readonly']); ?></td>
                            <td class="my_rupiah">
                                <?php if($membership_type->payment_type == 'Prepaid'): ?>
                                    0
                                <?php else: ?>
                                    <?php echo e(($membership_type->duration - 1) * $csf->mf_amount); ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            
                            
                                
                                
                            
                            
                            <td colspan="3">
                                <?php if($membership_type->is_vip == 'general'): ?>
                                    <div class="vip-box">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <?php echo Form::checkbox('use_vip', 'use_vip', false, ['id' => 'use_vip']); ?> <?php echo app('translator')->getFromJson('labels.use_vip_voucher'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <?php echo Form::label('vip_duration', trans('labels.free_membership_fee').': ', ['class' => 'control-label']); ?>

                                                <?php echo Form::number('vip_duration', '0', ['class' => 'form-control', 'style' => 'width: 60px;', 'min' => '0', 'disabled']); ?>

                                                <?php echo Form::select('vip_option', ['day' => trans('labels.day'), 'month' => trans('labels.month'), 'year' => trans('labels.year')], 'day', ['class' => 'form-control', 'id' => 'vip-option', 'disabled']); ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php elseif($membership_type->is_vip == 'vip'): ?>
                                    <div class="vip-box-gold">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <?php echo Form::checkbox('use_vip', 'use_vip', true, ['id' => 'use_vip']); ?> <?php echo app('translator')->getFromJson('labels.use_vip_voucher'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <?php echo Form::label('vip_duration', trans('labels.free_membership_fee').': ', ['class' => 'control-label']); ?>

                                                <?php echo Form::number('vip_duration', '0', ['class' => 'form-control', 'style' => 'width: 60px;', 'min' => '0', 'max' => '365']); ?>

                                                <?php echo Form::select('vip_option', ['day' => trans('labels.day'), 'month' => trans('labels.month'), 'year' => trans('labels.year')], 'day', ['class' => 'form-control', 'id' => 'vip-option']); ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td rowspan="2">
                                <button type="button" id="pay-btn" class="btn btn-success btn-lg" data-toggle="modal"
                                        data-target="#payment-modal"><?php echo app('translator')->getFromJson('labels.pay'); ?></button>
                            </td>
                        </tr>
                        
                            
                            
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Payment -->
        <div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="payment-modalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="payment-modalLabel"><?php echo app('translator')->getFromJson('labels.payment'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <tr>
                                <th><?php echo Form::label('total_bill', trans('labels.billing_total'), ['class' => 'control-label']); ?></th>
                                <td>:</td>
                                <td><?php echo Form::text('total_bill', ($subtotal - $discount), ['class' => 'form-control rupiah', 'readonly']); ?></td>
                            </tr>
                            <tr style="border-bottom: 1px solid;">
                                <th><?php echo Form::label('total_pay', trans('labels.cash'), ['class' => 'control-label', 'id' => 'payment-method-label']); ?></th>
                                <td>:</td>
                                <td><?php echo Form::text('total_pay', ($subtotal - $discount), ['class' => 'form-control rupiah']); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo Form::label('rest_of_bill', trans('labels.rest_of_the_bill'), ['class' => 'control-label']); ?></th>
                                <td>:</td>
                                <td><?php echo Form::text('rest_of_bill', '0', ['class' => 'form-control rupiah', 'readonly']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo app('translator')->getFromJson('labels.cancel'); ?></button>
                        <button type="button" class="btn btn-primary"
                                id="form-payment-submit-btn"><?php echo app('translator')->getFromJson('labels.process'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php echo Form::close(); ?>

    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('other-script'); ?>
    <script>
        $(document).ready(function () {
            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
            });

            var url = "<?php echo e(url('cf-lite')); ?>/";
            var mf_amount = parseInt(rupiah_format_destroyer($('input[name="mf_amount"]').val()));

            $('input[name="sf_amount"]').on('keyup', function () {
                var sf_amount = parseInt(rupiah_format_destroyer($('input[name="sf_amount"]').val()));

                if ($(this).val() == "") {
                    sf_amount = 0;
                } else {
                    sf_amount = parseInt(rupiah_format_destroyer($(this).val()));
                }

                var first_amount = sf_amount + mf_amount;
                var voucher = parseInt(rupiah_format_destroyer($('input[name="voucher_value"]').val()));
                var discount = parseInt(rupiah_format_destroyer($('input[name="discount"]').val()));
                var subtotal = sf_amount + mf_amount - discount - voucher;
                var tax_percentage = parseInt(rupiah_format_destroyer($('input[name="tax_percentage"]').val()));
//                var tax = Math.ceil(subtotal * tax_percentage * 0.01);
                var tax = 0;
                var total = subtotal + tax;

                $('input[name="subtotal"]').val(rupiah_formater(subtotal));
//                $('input[name="tax_amount"]').val(rupiah_formater(tax));
                $('input[name="total"]').val(rupiah_formater(total));

                $('input[name="discount"]').val(rupiah_formater(discount));
                $('input[name="sf_amount"]').val(rupiah_formater(sf_amount));
            });

            $('input[name="discount"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var discount = 0;
                } else {
                    var discount = parseInt(rupiah_format_destroyer($(this).val()));
                }

                var sf_amount = parseInt(rupiah_format_destroyer($('input[name="sf_amount"]').val()));
                var first_amount = sf_amount + mf_amount;
                var voucher = parseInt(rupiah_format_destroyer($('input[name="voucher_value"]').val()));
                var subtotal = first_amount - discount - voucher;
                var tax_percentage = parseInt(rupiah_format_destroyer($('input[name="tax_percentage"]').val()));
//                var tax = Math.ceil(subtotal * tax_percentage * 0.01);
                var tax = 0;
                var total = subtotal + tax;

                $('input[name="subtotal"]').val(rupiah_formater(subtotal));
//                $('input[name="tax_amount"]').val(rupiah_formater(tax));
                $('input[name="total"]').val(rupiah_formater(total));

                $('input[name="discount"]').val(rupiah_formater(discount));
            });

            $('#check-voucher-btn').on('click', function () {
                if ($('input[name="voucher_code"]').val() == '') {
                    return false;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    voucher_code: $('input[name="voucher_code"]').val()
                };

                var type = 'POST';
                var my_url = url + 'verify-voucher-code-product';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.voucher_data == 'expired') {
                            alert('<?php echo app('translator')->getFromJson('messages.sorry_the_voucher_expired'); ?>');
                        } else {
                            if (data.voucher_data['type_id'] == '1' || data.voucher_data['type_id'] == '3') {
//                                var subtotal = parseInt(rupiah_format_destroyer($('input[name="subtotal"]').val()));
                                var subtotal = parseInt(rupiah_format_destroyer($('input[name="total"]').val()));
                                var old_voucher = parseInt(rupiah_format_destroyer($('input[name="voucher_value"]').val()));
                                if (data.voucher_data['status'] == 'Out') {
                                    if (data.voucher_data['voucher_base'] == 'value') {
                                        var voucher = parseInt(data.voucher_data['value']);
                                    } else if (data.voucher_data['voucher_base'] == 'percent') {
                                        var voucher = (subtotal + old_voucher) * parseInt(data.voucher_data['percent']) * 0.01;
                                        if (voucher > data.voucher_data['value']) {
                                            voucher = parseInt(data.voucher_data['value']);
                                        }
                                    }
                                    var total = subtotal + old_voucher - voucher;
                                    $('input[name="voucher_value"]').val(rupiah_formater(voucher));
                                } else if (data.voucher_data['status'] == 'Used') {
                                    var total = subtotal + old_voucher;
                                    alert('<?php echo app('translator')->getFromJson('messages.sorry_the_voucher_has_been_used'); ?>');
                                    $('input[name="voucher_value"]').val('0');
                                } else if (data.voucher_data['status'] == 'InStock') {
                                    var total = subtotal + old_voucher;
                                    alert('<?php echo app('translator')->getFromJson('messages.sorry_the_voucher_has_not_been_released'); ?>');
                                    $('input[name="voucher_value"]').val('0');
                                }

                                if (total >= 0) {
//                                    $('input[name="subtotal"]').val(rupiah_formater(total));
                                    $('input[name="total"]').val(rupiah_formater(total));
                                } else {
//                                    $('input[name="subtotal"]').val('0');
                                    $('input[name="total"]').val(rupiah_formater(total));
                                    total = 0;
                                }
//                                var tax_percentage = parseInt(rupiah_format_destroyer($('input[name="tax_percentage"]').val()));
//                                var tax_value = Math.ceil(total * tax_percentage * 0.01);
//                                var final_total = total + tax_value;
                                var final_total = total;

//                                $('input[name="tax_amount"]').val(rupiah_formater(tax_value));
                                $('input[name="total"]').val(rupiah_formater(final_total));
                            } else if (data.voucher_data['type_id'] == '2') {
//                                var subtotal = parseInt(rupiah_format_destroyer($('input[name="subtotal"]').val()));
                                var subtotal = parseInt(rupiah_format_destroyer($('input[name="total"]').val()));
                                var old_voucher = parseInt(rupiah_format_destroyer($('input[name="voucher_value"]').val()));
                                if (data.voucher_data['status'] == 'Out') {
                                    var total = 0;
                                    $('input[name="voucher_value"]').val(rupiah_formater(parseInt(subtotal + old_voucher)));
                                } else if (data.voucher_data['status'] == 'Used') {
                                    var total = subtotal + old_voucher;
                                    $('input[name="voucher_value"]').val('0');
                                    alert('<?php echo app('translator')->getFromJson('messages.sorry_the_voucher_has_been_used'); ?>');
                                    $('input[name="voucher_value"]').val('0');
                                } else if (data.voucher_data['status'] == 'InStock') {
                                    var total = subtotal + old_voucher;
                                    alert('<?php echo app('translator')->getFromJson('messages.sorry_the_voucher_has_not_been_released'); ?>');
                                    $('input[name="voucher_value"]').val('0');
                                }

//                                $('input[name="subtotal"]').val(rupiah_formater(total));
//                                $('input[name="tax_amount"]').val('0');
                                $('input[name="total"]').val(rupiah_formater(total));
                            }
                        }
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#pay-btn').on('click', function () {
                var nominal = $('input[name="total"]').val();
                $('input[name="total_bill"]').val(nominal);
                $('input[name="total_pay"]').val(nominal);
            });

            $('#total_pay').on('keyup', function () {
                if ($(this).val() == "") {
                    var total_pay = 0;
                } else {
                    var total_pay = parseInt(rupiah_format_destroyer($('#total_pay').val()));
                }
                var rest_of_bill = parseInt(rupiah_format_destroyer($('#total_bill').val())) - parseInt(rupiah_format_destroyer(total_pay));
                $('#rest_of_bill').val(rupiah_formater(rest_of_bill));
                $('#total_pay').val(rupiah_formater(total_pay));
            });

            $('#form-payment-submit-btn').on('click', function () {
                if (($('#payment_method').val() == '1') && ($('#bank').val() != '')) {
                    alert('<?php echo app('translator')->getFromJson('messages.please_check_payment_method'); ?>');
                } else if (($('#payment_method').val() != '1') && ($('#bank').val() == '')) {
                    alert('<?php echo app('translator')->getFromJson('messages.please_choose_bank'); ?>');
                } else {
                    $('.rupiah').each(function () {
                        var x = rupiah_format_destroyer($(this).val());
                        $(this).val((x));
                    });
                    $('#form-continue-payment').submit();
                }
            });

            init_format();

            $('#payment_method').on('change', function () {
                if ($(this).val() == '1') {
                    $('#payment-method-label').text('<?php echo app('translator')->getFromJson('labels.cash'); ?>');
                } else if ($(this).val() == '2') {
                    $('#payment-method-label').text('<?php echo app('translator')->getFromJson('labels.debet'); ?>');
                } else if ($(this).val() == '3') {
                    $('#payment-method-label').text('<?php echo app('translator')->getFromJson('labels.credit'); ?>');
                } else if ($(this).val() == '4') {
                    $('#payment-method-label').text('<?php echo app('translator')->getFromJson('labels.transfer'); ?>');
                }
            });

            $('.vip-box').on('click', '#use_vip', function () {
                if ($(this).prop('checked') == true) {
                    $('#vip_duration').prop('disabled', false);
                    $('#vip-option').prop('disabled', false);
                } else {
                    $('#vip_duration').prop('disabled', true);
                    $('#vip-option').prop('disabled', true);
                }
            });

            $('.vip-box-gold').on('click', '#use_vip', function () {
                $(this).prop('checked', true);
            });

            $('#vip-option').on('change', function () {
                if ($(this).val() == 'day') {
                    $('#vip_duration').attr('max', '365');
                } else if ($(this).val() == 'month') {
                    $('#vip_duration').attr('max', '18');
                } else if ($(this).val() == 'year') {
                    $('#vip_duration').attr('max', '2');
                }
            });
        });


        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="<?php echo e(asset('js/my_js.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cf_lite.layouts.staff-template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>