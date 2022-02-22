<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Stock;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StockImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $rows =  array_slice($rows->toArray(), 1 );

        foreach ($rows as $key => $row) {
                echo $row[1];
            $product = Product::where('title',$row[0])->first();
            if ($product && $row[1]) {
                $Stock = Stock::where('product_id',$product->id)->get();
                $num = count($Stock)+1;
                Stock::create([
                    'libelle' => 'STOCK-'.$product->title.'-'.$num,
                    'product_id' => $product->id,
                    'quantite'    => $row[1],
                ]);
                $Product = Product::findOrFail($product->id);
                $produ['stock'] = $row[1]+ $Product->stock ;


                $status = $Product->fill($produ)->save();
            }else{
                return false;
            }
        }
    }
}
