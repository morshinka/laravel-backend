<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // dikarenakan pada table OrderItem memiliki 3 filed sedangkan pada factory hanya menambahkan
        // quantity tidak serta merta bisa melakukan seeding sehingga perlu dibuatkan data manual
        // untuk melakukan seeding

        // ambil data dari DB Order yang pertama
        $order = Order::find(1);

        // ambil semua data yang ada di DB Product
        $products = Product::all();

        // melakukan factory untuk mengenerate 5 data dengan order_id akan di isi dengan var $order
        // data product_id akan di ambil dari var $products secara random berdasarkan id
        for($i = 0; $i < 5; $i++){
            OrderItem::factory()->create([
                'order_id' => $order,
                'product_id' => $products->random()->id,
            ]);
        }
    }
}
