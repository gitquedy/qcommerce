<?php

namespace App\Imports;

use Auth;
use Validator;
use App\Adjustment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AdjustmentImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Adjustment([
            'sku_code'      => $row['sku_code'],
            'quantity'      => $row['quantity'], 
            'type'          => $row['type'],
        ]);
    }
    
    public function rules(): array
    {
        return [
            'sku_code' => 'required',
            'quantity' => 'required',
            'type' => 'required',
        ];
    }
}
