
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory()->create(['name' => 'Kopi Robusta', 'price' => 25000, 'stok' => 20]);
        Product::factory()->create(['name' => 'Teh Melati', 'price' => 15000, 'stok' => 30]);
        Product::factory()->create(['name' => 'Gula Aren', 'price' => 12000, 'stok' => 25]);
    }
}
