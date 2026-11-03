<?php

namespace App\Livewire\Admin\Products;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Productos')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: '')]
    public string $status = '';

    public ?int $detailId = null;

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'category', 'status'])) {
            $this->resetPage();
        }
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('products.active');

        $product = Product::findOrFail($id);
        $product->update(['is_active' => ! $product->is_active]);
        session()->flash('success', "Producto actualizado");
    }

    public function delete(int $id): void
    {
        $this->authorize('products.delete');

        $product = Product::findOrFail($id);
        $product->delete();

        session()->flash('success', "Producto {$product->sku} eliminado.");
    }

    public function showDetail(int $id): void
    {
        $this->authorize('products.show');
        $this->detailId = $id;
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
    }

    public function render()
    {
        $products = Product::query()
            ->with('category')
            ->when($this->search, fn($query) => $query->where(fn($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%")))
            ->when($this->category, fn($query) => $query->where('category_id', $this->category))
            ->when($this->status === 'active', fn($query) => $query->where('is_active', true))
            ->when($this->status === 'inactive', fn($query) => $query->where('is_active', false))
            ->latest('id')
            ->paginate(10);

        $categories = Category::orderBy('name')->get();
        $detail = $this->detailId ? Product::with('category')->find($this->detailId) : null;
        return view('livewire.admin.products.index', compact('products', 'categories', 'detail'));
    }
}
