<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Seeder;
use App\Models\StoreProductVariant;
use App\Models\StoreProduct;
use App\Models\Store;
use Exception;
use DB;

class StoreDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::beginTransaction();
    	try{
	    	//====store======
	    	$ibox = Store::create([
	    		'name' => 'IBOX',
	    		'address' => 'BSD'
	    	]);

	    	$asus = Store::create([
	    		'name' => 'ASUS',
	    		'address' => 'Jakarta'
	    	]);     	

	        //====product======

	    	//MBP
	    	$mbp = StoreProduct::create([
	    		'store_id' => $ibox->id,
	    		'name' => 'Macbook Pro (MBP)'
	    	]);

	    	//MBA
	    	$mba = StoreProduct::create([
	    		'store_id' => $ibox->id,
	    		'name' => 'Macbook Air (MBA)'
	    	]);

	    	//Iphone
	    	$iphone = StoreProduct::create([
	    		'store_id' => $ibox->id,
	    		'name' => 'Iphone'
	    	]);

	    	//vivobook
	    	$vivobook = StoreProduct::create([
	    		'store_id' => $asus->id,
	    		'name' => 'Vivobook'
	    	]); 

	    	//====product variant======  

	    	StoreProductVariant::create([
	    		'name' => 'Macbook Pro intel i9 16 inch',
	    		'product_id' => $mbp->id,
	    		'price' => 45000000
	    	]); 	

	    	StoreProductVariant::create([
	    		'name' => 'Macbook Pro intel i5 13 inch',
	    		'product_id' => $mbp->id,
	    		'price' => 22000000
	    	]); 	    	

	    	StoreProductVariant::create([
	    		'name' => 'Macbook Pro intel i5 CTO 13 inch',
	    		'product_id' => $mbp->id,
	    		'price' => 35000000
	    	]); 	    	 

	    	StoreProductVariant::create([
	    		'name' => 'Macbook Pro M1 13 inch',
	    		'product_id' => $mbp->id,
	    		'price' => 18000000
	    	]);

	    	StoreProductVariant::create([
	    		'name' => 'Macbook Pro M1 16 inch',
	    		'product_id' => $mbp->id,
	    		'price' => 30000000
	    	]); 

	    	StoreProductVariant::create([
	    		'name' => 'Macbook Air 2017 13 inch',
	    		'product_id' => $mba->id,
	    		'price' => 12000000
	    	]);

	    	StoreProductVariant::create([
	    		'name' => 'Macbook Air M1 2021',
	    		'product_id' => $mba->id,
	    		'price' => 15000000
	    	]);    	     	   	 	    	     	   	

	    	StoreProductVariant::create([
	    		'name' => 'Iphone 5s',
	    		'product_id' => $iphone->id,
	    		'price' => 800000
	    	]);    	     	   	 

	    	StoreProductVariant::create([
	    		'name' => 'Iphone 12 pro',
	    		'product_id' => $iphone->id,
	    		'price' => 20000000
	    	]);  

	    	StoreProductVariant::create([
	    		'name' => 'Vivobook Pro',
	    		'product_id' => $vivobook->id,
	    		'price' => 20000000
	    	]);      	  	

	    	StoreProductVariant::create([
	    		'name' => 'Vivobook S',
	    		'product_id' => $vivobook->id,
	    		'price' => 10000000
	    	]); 

	    	DB::commit(); 

	    	Log::info('Sukses migrasi data');
    	}catch(Exception $e) {
    		DB::rollback();
    		Log::info('Gagal migrasi data');
    	}    	  	    	     	   	     		    	     	   	    	
   	
    }
}
