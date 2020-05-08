<?php

namespace App\Imports;

use Auth;
use Validator;
use App\Sku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SkuImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Sku([
            'business_id'   => Auth::user()->business_id,
            'code'          => $row['code'],
            'name'          => $row['name'], 
            'brand'         => $row['brand'], 
            'category'      => $row['category'], 
            'supplier'      => $row['supplier'], 
            'cost'          => $row['cost'],
            'price'         => $row['price'],
            'alert_quantity'=> $row['alert_quantity']
        ]);
    }
    
    public function rules(): array
    {
        return [
            'code' => 'required',
            'name' => 'required',
            'brand' => 'nullable',
            'category' => 'nullable',
            'supplier' => 'nullable',
            'cost' => 'required|numeric',
            'price' => 'required|numeric',
            'alert_quantity' => 'required|numeric',
        ];
    }
}