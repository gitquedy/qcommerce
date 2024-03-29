<html>
    <head>
        <title>{{$sales->reference_no}}</title>
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
            .blank_row {
                height: 15px;
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
        <div><strong>SALES INVOICE #{{$sales->reference_no}}</strong></div>
        <div class="container">
            <div class="grid-item col-1">DELIVERED TO</div>
            <div class="grid-item col-2"><strong>{{$sales->customer_first_name.' '.$sales->customer_last_name}}</strong></div>
            <div class="grid-item col-3">DATE</div>
            <div class="grid-item col-3"><strong>: {{$sales->date}}</strong></div>
        </div>
        <div class="container">
            <div class="grid-item col-1">ADDRESS</div>
            <div class="grid-item col-2"><strong>{{$sales->customer->address}}</strong></div>
        </div>
        <table>
            <tr>
                <th>QTY</th>
                <th>UNIT</th>
                <th>PARTICULARS</th>
                <th>UNIT COST</th>
                <th>AMOUNT</th>
            </tr>
            @foreach($sales->items as $item)
            <tr>
                <td>{{$item->quantity}}</td>
                <td>pc</td>
                <td>{{$item->sku_name}}</td>
                <td>{{$item->unit_price}}</td>
                <td>{{$item->subtotal}}</td>
            </tr>
            @endforeach
            <tr>
                <td class="blank_row"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="blank_row"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th>TOTAL AMOUNT DUE: </th>
                <th></th>
                <th>{{$sales->total}}</th>
            </tr>
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