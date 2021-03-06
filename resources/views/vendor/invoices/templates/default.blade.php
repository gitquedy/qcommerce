<!DOCTYPE html>
<html lang="en">
<head>
    <title>Print Shipping Label</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css" media="screen">
        html {
            font-family: sans-serif;
            line-height: 1.15;
            margin: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 10px;
            margin: 36pt;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        strong {
            font-weight: bolder;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        table {
            border-collapse: collapse;
        }

        th {
            text-align: inherit;
        }

        h4, .h4 {
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        h4, .h4 {
            font-size: 1.5rem;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        * {
            font-family: "DejaVu Sans";
        }

        body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
            line-height: 1.1;
        }

        .party-header {
            font-size: 1.5rem;
            font-weight: 400;
        }

        .total-amount {
            font-size: 12px;
            font-weight: 700;
        }

        .border-0 {
            border: none !important;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

    <body>
        {{-- Header --}}
        @php
            $count = 0;
        @endphp
        @foreach($invoices as $invoice)
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h3>Order No: {{ $invoice->buyer->custom_fields['order number'] }}</h3>
                        <h3>Date: {{ $invoice->buyer->custom_fields['date'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- Seller - Buyer --}}
            <table class="table">
                <thead>
                    <tr>
                        <th class="border-0 pl-0" width="48.5%">
                            <h2>{{ __('invoices::invoice.seller') }}</h2>
                        </th>
                        <th class="border-0" width="3%"></th>
                        <th class="border-0 pl-0">
                            <h2>{{ __('invoices::invoice.buyer') }}</h2>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-0">
                            @if($invoice->seller->name)
                                <p class="seller-name">
                                    <strong>{{ $invoice->seller->name }}</strong>
                                </p>
                            @endif

                            @if($invoice->seller->address)
                                <p class="seller-address">
                                    {{ __('invoices::invoice.address') }}: {{ $invoice->seller->address }}
                                </p>
                            @endif

                            @if($invoice->seller->code)
                                <p class="seller-code">
                                    {{ __('invoices::invoice.code') }}: {{ $invoice->seller->code }}
                                </p>
                            @endif

                            @if($invoice->seller->vat)
                                <p class="seller-vat">
                                    {{ __('invoices::invoice.vat') }}: {{ $invoice->seller->vat }}
                                </p>
                            @endif

                            @if($invoice->seller->phone)
                                <p class="seller-phone">
                                    {{ __('invoices::invoice.phone') }}: {{ $invoice->seller->phone }}
                                </p>
                            @endif

                            @foreach($invoice->seller->custom_fields as $key => $value)
                                <p class="seller-custom-field">
                                    {{ ucfirst($key) }}: {{ $value }}
                                </p>
                            @endforeach
                        </td>
                        <td class="border-0"></td>
                        <td class="px-0">
                            @if($invoice->buyer->name)
                                <p class="buyer-name">
                                    <strong>{{ $invoice->buyer->name }}</strong>
                                </p>
                            @endif

                            @if($invoice->buyer->address)
                                <p class="buyer-address">
                                    {{ __('invoices::invoice.address') }}: {{ $invoice->buyer->address }}
                                </p>
                            @endif

                            @if($invoice->buyer->code)
                                <p class="buyer-code">
                                    {{ __('invoices::invoice.code') }}: {{ $invoice->buyer->code }}
                                </p>
                            @endif

                            @if($invoice->buyer->vat)
                                <p class="buyer-vat">
                                    {{ __('invoices::invoice.vat') }}: {{ $invoice->buyer->vat }}
                                </p>
                            @endif

                            @if($invoice->buyer->phone)
                                <p class="buyer-phone">
                                    {{ __('invoices::invoice.phone') }}: {{ $invoice->buyer->phone }}
                                </p>
                            @endif

                            <!-- @foreach($invoice->buyer->custom_fields as $key => $value)
                                <p class="buyer-custom-field">
                                    {{ ucfirst($key) }}: {{ $value }}
                                </p>
                            @endforeach -->
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Table --}}
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="border-0 pl-0">Product</th>
                        @if($invoice->hasItemUnits)
                            <th scope="col" class="text-center border-0">{{ __('invoices::invoice.units') }}</th>
                        @endif
                        <th scope="col" class="text-center border-0">{{ __('invoices::invoice.quantity') }}</th>
                        <th scope="col" class="text-right border-0">{{ __('invoices::invoice.price') }}</th>
                        @if($invoice->hasItemDiscount)
                            <th scope="col" class="text-right border-0">{{ __('invoices::invoice.discount') }}</th>
                        @endif
                        @if($invoice->hasItemTax)
                            <th scope="col" class="text-right border-0">{{ __('invoices::invoice.tax') }}</th>
                        @endif
                        <th scope="col" class="text-right border-0 pr-0">{{ __('invoices::invoice.sub_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="pl-0">{{ $item->title }}</td>
                        @if($invoice->hasItemUnits)
                            <td class="text-center">{{ $item->units }}</td>
                        @endif
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">
                            {{ $invoice->formatCurrency((float)$item->price_per_unit) }}
                        </td>
                        @if($invoice->hasItemDiscount)
                            <td class="text-right">
                                {{ $invoice->formatCurrency((float)$item->discount) }}
                            </td>
                        @endif
                        @if($invoice->hasItemTax)
                            <td class="text-right">
                                {{ $invoice->formatCurrency((float)$item->tax) }}
                            </td>
                        @endif
                        <td class="text-right pr-0">
                            {{ $invoice->formatCurrency((float)$item->sub_total_price) }}
                        </td>
                    </tr>
                    @endforeach
                    {{-- Summary --}}
                    @if($invoice->hasItemOrInvoiceDiscount())
                        <tr>
                            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                            <td class="text-right pl-0">{{ __('invoices::invoice.total_discount') }}</td>
                            <td class="text-right pr-0">
                                {{ $invoice->formatCurrency((float)$invoice->total_discount) }}
                            </td>
                        </tr>
                    @endif
                    @if($invoice->taxable_amount)
                        <tr>
                            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                            <td class="text-right pl-0">{{ __('invoices::invoice.taxable_amount') }}</td>
                            <td class="text-right pr-0">
                                {{ $invoice->formatCurrency((float)$invoice->taxable_amount) }}
                            </td>
                        </tr>
                    @endif
                    @if($invoice->tax_rate)
                        <tr>
                            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                            <td class="text-right pl-0">{{ __('invoices::invoice.tax_rate') }}</td>
                            <td class="text-right pr-0">
                                {{ $invoice->tax_rate }}%
                            </td>
                        </tr>
                    @endif
                    @if($invoice->hasItemOrInvoiceTax())
                        <tr>
                            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                            <td class="text-right pl-0">{{ __('invoices::invoice.total_taxes') }}</td>
                            <td class="text-right pr-0">
                                {{ $invoice->formatCurrency((float)$invoice->total_taxes) }}
                            </td>
                        </tr>
                    @endif
                        <tr>
                            <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                            <td class="text-right pl-0">{{ __('invoices::invoice.total_amount') }}</td>
                            <td class="text-right pr-0 total-amount">
                                {{ $invoice->formatCurrency((float)$invoice->total_amount) }}
                            </td>
                        </tr>
                </tbody>
            </table>
            <hr>
            @php
                $count += 1;
            @endphp
            @if($count < count($invoices))
                <div class="page-break"></div>
            @endif
        @endforeach

        

        <script type="text/php">
            if (isset($pdf) && $PAGE_COUNT > 1) {
                $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
    </body>
</html>
