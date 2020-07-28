<?php

namespace App\Imports;

use Auth;
use App\Sku;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SkuImport implements ToModel, WithHeadingRow, WithValidation
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
            'code' => 'required|unique:sku,code,NULL,id,business_id,'.Auth::user()->business_id,
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