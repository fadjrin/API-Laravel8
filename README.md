Instalasi
1. buka di website resmi laravel untuk minimum requirement laravel 8
2. install composer (baca dokumentasi composer)
3. lakukan cloning source code ini, masuk directory lalu ketikan perintah "composer install"
4. copy file .env.example dan ubah menjadi .env
5. lakukan setting database sesuai RDBMS yang ada di local PC/laptop *saat development saya menggunakan postgresql
6. masih di root directory, ketikan perintah "php artisan passport:install"
7. lalu ketikan "php artisan migrate"
8. lalu ketikan "php artisan db:seed" *untuk inisialiasi data
9. keterangan endpoint :

[] /api/register
method POST
body json

{
	"name":"User",
	"email":"admin@gmail.com",
	"password":123456
}

[] /api/login
method POST
body json

{
	"email":"admin@gmail.com",
	"password":123456
}

[] /api/product/{keyword} pencarian toko, produk, varian
method GET

keterangan : keyword optional yang wajib bearer token *didapat di endpoint login

[] /api/cart => create cart
method POST
body json
{
	"user_id":1,
	"store_id":2,
	"product_id":4,
	"variant_id": 10,
	"qty":2
}

[] /api/cart/{id?} ubah cart
bearer token wajib
method PUT
body json
{
	"user_id":1,
	"store_id":2,
	"product_id":4,
	"variant_id": 10,
	"qty":2
}

[] /api/cart/{user_id} ambil data belanja user
bearer token wajib
method GET

semua body json ada validasinya, silahkan dicoba