<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_product_via_form(): void
    {
        $resp = $this->post('/products', ['name' => 'Produk Baru', 'price' => 12345, 'stok' => 7]);
        $resp->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['name' => 'Produk Baru', 'price' => 12345, 'stok' => 7]);
    }

    public function test_update_product_via_form(): void
    {
        $id = \App\Models\Product::factory()->create(['name' => 'A', 'price' => 1000, 'stok' => 2])->id;
        $resp = $this->put("/products/{$id}", ['name' => 'A+', 'price' => 2000, 'stok' => 5]);
        $resp->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['id' => $id, 'name' => 'A+', 'price' => 2000, 'stok' => 5]);
    }

    public function test_delete_product_via_form(): void
    {
        $product = \App\Models\Product::factory()->create();
        $resp = $this->delete("/products/{$product->id}");
        $resp->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
