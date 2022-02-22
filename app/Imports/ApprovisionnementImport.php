<?php

namespace App\Imports;

use App\Models\BoutiqueProduct;
use Maatwebsite\Excel\Concerns\ToModel;

class ApprovisionnementImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new BoutiqueProduct([
            //
        ]);
    }
}
