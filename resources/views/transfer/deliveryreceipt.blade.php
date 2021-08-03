<html>
    <head>
        <title>{{$transfer->reference_no}}</title>
        <style type="text/css" rel="stylesheet">
            * {
                /* border: 1px solid black; */
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
            .rep {
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
                /* position: fixed;
                bottom: 0;
                width: 100%; */
            }
        </style>
    </head>
    <body>
        <div>
            <div>{{$company->name}}</div>
            <div>{{$company->address}}</div>
            <div>VAT Reg TIN: {{$company->vat_tin_no}}</div>
            <div>{{$owner}}</div>
            <div>Mobile # {{$company->phone_no}}</div>
        </div>
        <br><br>
        <div><strong>DELIVERY RECEIPT</strong></div>
        <div class="container">
            <div class="grid-item col-1">DELIVERED TO</div>
            <div class="grid-item col-2"><strong>{{$warehouse->name}}</strong></div>
            <div class="grid-item col-3">DATE</div>
            <div class="grid-item col-3"><strong>: {{$transfer->date}}</strong></div>
        </div>
        <div class="container">
            <div class="grid-item col-1">ADDRESS</div>
            <div class="grid-item col-2"><strong>{{$warehouse->address}}</strong></div>
            <div class="grid-item col-3">TERMS</div>
            <div class="grid-item col-3"><strong>: Consignment</strong></div>
        </div>
        <table>
            <tr>
                <th>QTY</th>
                <th>UNIT</th>
                <th>PARTICULARS</th>
                <th>UNIT COST</th>
                <th>SELLING PRICE</th>
            </tr>
            @foreach($transfer->items as $item)
            <tr>
                <td>{{$item->quantity}}</td>
                <td>pc</td>
                <td>{{$item->sku_name}}</td>
                <td>{{App\Sku::find($item->sku_id)->cost}}</td>
                <td>{{App\Sku::find($item->sku_id)->price}}</td>
            </tr>
            @endforeach
        </table>
        <br><br>
        <div class="footer">
            <div class="rep">
                <div>Received the above goods in good order and condition.</div>
                <br><br>
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