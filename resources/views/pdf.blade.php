<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Curves Digital Receipt</title>

    <style type="text/css">
        body{
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 10px;
            padding: 2rem 5rem;
        }
        h4 {
            margin: 0;
        }
        .w-full {
            width: 100%;
        }
        .w-half {
            width: 50%;
        }
        .margin-top {
            margin-top: 1.25rem;
        }
        .footer {
            font-size: 1.4rem;
            padding: 1rem;
            text-align: center
        }
        table {
            width: 100%;
            border-spacing: 0;
        }
        table.products {
            font-size: 0.775rem;
        }
        table.products tr {
            /* background-color: rgb(96 165 250); */
            padding: 1.5rem
        }
        table.products th {
            color: #ffffff;
            padding: 0.5rem 1rem;
        }
        table tr.items {
            /* background-color: rgb(241 245 249); */
        }
        table tr td {
            padding: 0.5rem 1rem;
        }
        .total {
            text-align: right;
            padding-right: 1rem;
            font-size: 0.875rem;
        }
        hr{
            border-top: 1px dashed black;
            border-bottom: none ;
        }
    </style>
</head>
<body>
    <table class="w-full">
        @php
            $path = 'images/curveslogo.png';
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $imagedata = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($imagedata);
        @endphp

        <tr>
            <td class="w-full" align="center">
                <img src="{{ $base64 }}" alt="Curves" width="100" />
            </td>
        </tr>
        <tr><td height="5"></td></tr>
        <tr>
            <td class="w-full" align="center">
                <span style="font-size:14px;">{{ $data['club'] }}</span>
            </td>
        </tr>
    </table>

    <div class="margin-top">
        <table class="products">
            <!-- Membership Payment -->
            @if ($data['module'] == 'membership' || $data['module'] == 'locker' || $data['module'] == 'renewal' || $data['module'] == 'promo' || $data['module'] == 'promoConvert' )
                @if (isset($data['membershipType']) && $data['membershipType'] != '')
                    <tr class="items">
                        <td width="100">Tipe Keanggotaan</td>
                        <td>{{ isset($data['membershipType']) ? $data['membershipType'] : '' }}</td>
                    </tr>
                @endif

                @foreach($data['param'] as $key => $val)
                    @if ($key == 'spacer1' || $key == 'spacer2')
                        <tr class="items">
                            <td colspan="2"><hr></td>
                        </tr>
                    @else
                        @if ($key == 'membershipType')

                        @else
                            <tr class="items">
                                <td width="100">
                                    @if ($key == 'payment_method_not_1' || $key == 'payment_method_1')

                                    @else
                                        {{ $key }} :
                                    @endif

                                </td>
                                <td>
                                    {{ $val }}
                                </td>
                            </tr>
                        @endif
                    @endif
                @endforeach
            @endif

            <!-- Product Payment -->
            @if ($data['module'] == 'product')
                @foreach($data['param'] as $key => $val)
                    @if ($key == 'spacer1' || $key == 'spacer2')
                        <tr class="items">
                            <td colspan="2"><hr></td>
                        </tr>
                    @else
                        @if ($key == 'item_list')
                            @foreach($val as $product)
                                <tr class="items">
                                    <td width="200">{{ $product['item'] }}</td>
                                    <td align="right">{{ $product['qty'] }} x {{ $product['price'] }}</td>
                                </tr>
                            @endforeach

                            <tr class="items">
                                <td colspan="2"><hr></td>
                            </tr>
                        @else
                            <tr class="items">
                                <td width="100">
                                    @if ($key == 'payment_method_not_1' || $key == 'payment_method_1')

                                    @else
                                        {{ $key }} :
                                    @endif

                                </td>
                                <td>
                                    {{ $val }}
                                </td>
                            </tr>
                        @endif
                    @endif
                @endforeach
            @endif

            <tr class="items">
                <td colspan="2"><hr></td>
            </tr>

            <tr class="total">
                <td colspan="2"><b>Total: {{ $data['total'] }}</b></td>
            </tr>

            <tr class="items">
                <td colspan="2"><hr></td>
            </tr>
            <tr>
                <td colspan="2" align="left">Sudah Termasuk Pajak</td>
            </tr>
            <tr><td height="20"></td></tr>

            <tr class="footer margin-top">
                <td colspan="2">Terima Kasih</td>
            </tr>

        </table>
    </div>

</body>
</html>
