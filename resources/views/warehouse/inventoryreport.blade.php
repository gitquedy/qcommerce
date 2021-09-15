<html>
    <head>
        <title>{{$warehouse->name}}</title>
        <style type="text/css" rel="stylesheet">
            * {
                font-size: 15px;
            }
            .container {
                margin-top: 10px;
                margin-bottom: 10px;
            }
            .grid-item {
                display: inline-block;
                vertical-align: middle;
            }
            .col-1 {
                width: 20%;
            }
            .col-2 {
                width: 45%;
                padding-right: 5px;
            }
            .col-3 {
                width: 15%;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            td {
                text-align: center;
            }
            table, th, td {
                border: 1px solid black;
            }
            img {
                vertical-align: middle;
                margin-right: 10px;
                max-height: 150px;
                max-width: 150px;
            }
            .rep, .company {
                display: inline-block;
                vertical-align: middle;
            }
            .sign {
                display: inline-block;
                text-align: center;
                width: 35%;
                vertical-align: middle;
                float: right;
            }
            .underline {
                text-decoration: underline;
            }
            .footer {
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <div>
            <img src="{{public_path('images/profile/company-logo/'.$company->logo)}}">
            <div class="company">
                <div>{{$company->name}}</div>
                <div>{{$company->address}}</div>
                <div>VAT Reg TIN: {{$company->vat_tin_no}}</div>
                <div>Mobile # {{$company->phone_no}}</div>
            </div>
        </div>
        <br><br>
        <div><strong>INVENTORY REPORT #{{$inv_num}}</strong></div>
        <div class="container">
            <div class="grid-item col-1">DELIVERED TO</div>
            <div class="grid-item col-2"><strong>{{$warehouse->name}}</strong></div>
            <div class="grid-item col-3">DATE</div>
            <div class="grid-item col-3"><strong>: {{Carbon\Carbon::now()->toDateString()}}</strong></div>
        </div>
        <div class="container">
            <div class="grid-item col-1">ADDRESS</div>
            <div class="grid-item col-2"><strong>{{$warehouse->address}}</strong></div>
            <div class="grid-item col-3">TERMS</div>
            <div class="grid-item col-3"><strong>: </strong></div>
        </div>
        <table>
            <tr>
                <th>QTY</th>
                <th>SOLD</th>
                <th>DR#</th>
                <th>PARTICULARS</th>
                <th>UNIT COST</th>
                <th>SELLING PRICE</th>
            </tr>
            @forelse($warehouse->items as $item)
                @if($item->quantity > 0)
                <tr>
                    <td>{{$item->quantity}}</td>
                    <td></td>
                    <td>{{($item->transfer_item)?$item->transfer_item->transfer->reference_no:''}}</td>
                    <td>{{$item->sku->name}}</td>
                    @if($item->transfer_item)
                    <td>{{($item->transfer_item->transfer->price_group)
                            ?
                            ($price_group_item = $item->transfer_item->transfer->price_group->items()->where('sku_id', $item->sku_id)->first())
                                ?
                                $price_group_item->price
                                :
                                ''
                            :
                            ''}}</td>
                    @else
                    <td></td>
                    @endif
                    <td>{{App\Sku::find($item->sku_id)->price}}</td>
                </tr>
                @endif
            @empty
            <tr>
                <td colspan="6">Empty.</td>
            </tr>
            @endforelse
        </table>
        <br><br>
        <div class="footer">
            <div class="rep">
                <br><br><br>
                <div>By Authorized Representative</div>
            </div>
            <div class="sign">
                <br><br>
                <div class="underline">_______________________________</div>
                <div>Partner Merchant</div>
                <div>Signature Over Printed Name</div>
            </div>
        </div>
    </body>
</html>