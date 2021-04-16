<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\StoreProductVariant;
use App\Models\StoreProduct;
use App\Models\UserCart;
use App\Models\Store;
use App\Models\User;
use Exception;

class StoreController extends Controller
{
	public function getproduct($search=null)
	{
		try{
			if ($search != null) {
				//kalau ada keyword pencarian
	            return response()->json([
	                'status'  => true,
	                'msg' => 'Data dengan pencarian',
	                'data' => $this->mappingProduct([
	                	'store' => Store::where('name','ilike','%'.$search.'%')->get(),
	                	'storeProduct' => StoreProduct::where('name','ilike','%'.$search.'%')->get(),
	                	'storeProductVariant' => StoreProductVariant::where('name','ilike','%'.$search.'%')->get()
	                ])
	            ]);
			}

            return response()->json([
                'status'  => true,
                'msg' => 'Data tanpa pencarian',
                'data' => $this->mappingProduct([
                	'store' => collect([]),
                	'storeProduct' => collect([]),
                	'storeProductVariant' => collect([])
                ])
            ]);
		}catch(Exception $e) {
            return response()->json([
                'status'  => false,
                'msg' => 'Data tidak ditemukan',
                'data' => []
            ])->setStatusCode(400);		
		}
	}

	protected function mappingProduct($param)
	{
		$variantIds = [];
		$productIds = [];
		$storeIds = [];

		//artinya ada pencarian
		if ((bool)$param['storeProductVariant']->count()) {

			$variantIds = $param['storeProductVariant']->unique('id')->map(function($item, $key) {
				return (int)$item->id;
			})->toArray();

			$productIds = $param['storeProductVariant']->unique('product_id')->map(function($item, $key) {
				return (int)$item->product_id;
			})->toArray();

			$storeIds = StoreProduct::whereIn('id', $productIds)->get()->unique('store_id')->map(function($item, $key) {
				return (int)$item->store_id;
			})->toArray();
		}

        //artinya ada pencarian
		if ((bool)$param['storeProduct']->count()) {
			$store_ids = $param['storeProduct']->unique('store_id')->map(function($item, $key) {
				return (int)$item->store_id;
			})->toArray();	


			$storeIds = array_merge($store_ids, $storeIds);			
		}

		//artinya ada pencarian
		if ((bool)$param['store']->count()) {
			$store_ids = $param['store']->unique('id')->map(function($item, $key) {
				return (int)$item->id;
			})->toArray();	

			$storeIds = array_merge($store_ids, $storeIds);	
		}

		if ((bool)count($storeIds)) {
			return $this->getProductProperty([
				'variantIds' => $variantIds,
				'productIds' => $productIds,
				'storeIds' => $storeIds
			]);
		}

		return $this->getProductProperty([
			'variantIds' => [],
			'productIds' => [],
			'storeIds' => []
		]);
	}

	protected function getProductProperty($param)
	{
		$rest = [];
		if ((bool)count($param['storeIds'])) {
			//ambil data store dengan id tertentu
			$store = Store::whereIn('id', $param['storeIds'])->get();
		}else{
			//ambil semua data store
			$store = Store::get();
		}

		foreach ($store as $key => $value) {
			     $rest[] = [
			     	'id' => $value->id,
			     	'name' => $value->name,
			     	'address' => $value->address,
			     	'product' => $this->getProductDetail($value->id, $param)
			     ];
		}

		return $rest;
	}

	protected function getProductDetail($id, $param)
	{
		$rest = [];
		
		if ((bool)count($param['productIds'])) {
			//ambil data produk dengan id dari pencarian
			$product = StoreProduct::whereIn('id', $param['productIds'])->where('store_id','=',$id)->get();
		}else{
			//jika tidak ada maka ambil semua data berdasarkan id yang dilempar dari store
			$product = StoreProduct::where('store_id', '=', $id)->get();
		}		

		foreach ($product as $key => $value) {
			     $rest[] = [
			     	'id' => $value->id,
			     	'store_id' => $value->store_id,
			     	'name' => $value->name,
			     	'variant' => $this->getProductVariant($value->id, $param)
			     ];
		}	

		return $rest;	
	}

	protected function getProductVariant($id, $param)
	{
		$rest = [];
		
		if ((bool)count($param['variantIds'])) {
			//ambil data varianr dengan id dari pencarian
			// $variant = StoreProductVariant::whereIn('id', $param['variantIds'])->get();
			$variant = StoreProductVariant::whereIn('id', $param['variantIds'])->where('product_id','=',$id)->get();
		}else{
			//jika tidak ada maka ambil semua data berdasarkan id yang dilempar dari produk
			$variant = StoreProductVariant::where('product_id', '=', $id)->get();
		}		

		foreach ($variant as $key => $value) {
			     $rest[] = [
			     	'id' => $value->id,
			     	'product_id' => $value->product_id,
			     	'name' => $value->name,
			     	'price' => $value->price
			     ];
		}		
		

		return $rest;			
	}

	public function createcart(Request $request)
	{
		$validator = $this->validateCart($request); 

		if (!$validator->fails()) {
			try{
				$cart = UserCart::create($request->input());

		        return response()->json([
		            'status'  => true,
		            'msg' => 'Proses sukses dilakukan',
		            'data' => $cart
		        ]);
			}catch(Exception $e) {
		        return response()->json([
		            'status'  => false,
		            'msg' => 'Proses gagal dilakukan',
		            'data' => []
		        ])->setStatusCode(400);						
			}
		}else{
            $messages = $validator->errors()->all('<li>:message</li>');
            return response()->json([
                'status'  => false,
                'msg' => '<ul>'.implode('', $messages).'</ul>',
                'data' => []
            ])->setStatusCode(400);												
		}
	}

	protected function validateCart($request)
	{
        $required['user_id'] = [
        	'required',
            function($attribute, $value, $fail) {
            	$data = User::find((int)$value);
                if($data == null) {
                   return $fail('user_id <b>'.$value.'</b> tidak tercatat di database'); 
                }
            },          	
        ]; 

        $required['store_id'] = [
        	'required',
            function($attribute, $value, $fail) {
            	$data = Store::find((int)$value);
                if($data == null) {
                   return $fail('store_id <b>'.$value.'</b> tidak tercatat di database'); 
                }
            },          	
        ];

        $required['product_id'] = [
        	'required',
            function($attribute, $value, $fail) use($request) {
            	$data = StoreProduct::find((int)$value);
                if($data == null) {
                   return $fail('product_id <b>'.$value.'</b> tidak tercatat di database'); 
                }else{
                	//lakukan pengecekan juga apakah produk ini berelasi dengan store yang di input diatas, jika tidak ada relasi berikan notif
                	$product = StoreProduct::where('store_id','=',$request->input('store_id'))->where('id','=',$value)->get();

                	if (!(bool)$product->count()) {
                		return $fail('product_id <b>'.$value.'</b> tidak berelasi dengan store_id '.$request->input('store_id')); 
                	}
                }
            },          	
        ];  

        $required['variant_id'] = [
        	'required',
            function($attribute, $value, $fail) use($request) {
            	$data = StoreProductVariant::find((int)$value);
                if($data == null) {
                   return $fail('variant_id <b>'.$value.'</b> tidak tercatat di database'); 
                }else{
                	//lakukan pengecekan juga apakah variant ini berelasi dengan produk yang di input diatas, jika tidak ada relasi berikan notif
                	$variant = StoreProductVariant::where('product_id','=',$request->input('product_id'))->where('id','=',$value)->get();

                	if (!(bool)$variant->count()) {
                		return $fail('variant_id <b>'.$value.'</b> tidak berelasi dengan product_id '.$request->input('product_id')); 
                	}
                }
            },          	
        ]; 

        $required['qty'] = 'required|numeric';                        

        $message['user_id.required'] = 'user_id wajib diinput'; 
        $message['store_id.required'] = 'store_id wajib diinput';           
        $message['product_id.required'] = 'product_id wajib diinput';           
        $message['variant_id.required'] = 'variant_id wajib diinput';  
        $message['qty.required'] = 'qty wajib diinput';           
        $message['qty.numeric'] = 'qty wajib angka';           

        return Validator::make($request->all(), $required, $message);		
	}

	public function updatecart(Request $request, $id=null)
	{
		$validator = $this->validateCart($request); 

		if (!$validator->fails()) {
			try{
				$cart = UserCart::find($id);
				if($cart == null) {
			        return response()->json([
			            'status'  => false,
			            'msg' => 'cart_id '.$id.' tidak ditemukan',
			            'data' => []
			        ])->setStatusCode(400);						
				}

	           	$cart->update($request->input());     

		        return response()->json([
		            'status'  => true,
		            'msg' => 'Proses sukses dilakukan',
		            'data' => $cart
		        ]);
			}catch(Exception $e) {
		        return response()->json([
		            'status'  => false,
		            'msg' => 'Proses gagal dilakukan',
		            'data' => []
		        ])->setStatusCode(400);						
			}
		}else{
            $messages = $validator->errors()->all('<li>:message</li>');
            return response()->json([
                'status'  => false,
                'msg' => '<ul>'.implode('', $messages).'</ul>',
                'data' => []
            ])->setStatusCode(400);												
		}
	}

	public function getcart($user_id=null)
	{
		if ($user_id == null) {
	        return response()->json([
	            'status'  => false,
	            'msg' => 'user_id wajib diinput',
	            'data' => []
	        ])->setStatusCode(400);				
		}

		$data = UserCart::where('user_id','=',$user_id);

		if (!(bool)$data->count()) {
	        return response()->json([
	            'status'  => false,
	            'msg' => 'Data tidak ditemukan',
	            'data' => []
	        ])->setStatusCode(400);				
		}

		$storeIds = [];
		$productIds = [];
		$variantIds = [];		
		foreach ($data->get() as $key => $value) {
			$storeIds[] = $value->store_id;
			$productIds[] = $value->product_id;
			$variantIds[] = $value->variant_id;
		}

        return response()->json([
            'status'  => true,
            'msg' => 'Data user_id '.$user_id,
            'data' => $this->mappingProduct([
            	'store' => Store::whereIn('id',$storeIds)->get(),
            	'storeProduct' => StoreProduct::whereIn('id',$productIds)->get(),
            	'storeProductVariant' => StoreProductVariant::whereIn('id',$variantIds)->get()
            ])
        ]);		
	}	
}
