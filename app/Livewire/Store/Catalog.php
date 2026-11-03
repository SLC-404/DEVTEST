<?php

namespace App\Livewire\Store;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.store')]
#[Title('Catálogo')]

class Catalog extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(except: '')]
    public string $minPrice = '';

    #[Url(except: '')]
    public string $maxPrice = '';

    #[Url(except: false)]
    public bool $inStock = false;

    public function clearFilters(): void
    {
        $this->reset(['search', 'category', 'minPrice', 'maxPrice', 'inStock']);
        $this->resetPage();
    }

    public function updated(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::query()
            ->available()
            ->with('category')
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->when($this->category, fn ($query) => $query->where('category_id', $this->category))
            ->when(is_numeric($this->minPrice), fn ($query) => $query->where('price_mxn', '>=', $this->minPrice))
            ->when(is_numeric($this->maxPrice), fn ($query) => $query->where('price_mxn', '<=', $this->maxPrice))
            ->when($this->inStock, fn ($query) => $query->where('stock', '>', 0))
            ->orderBy('name')
            ->paginate(12);

        $categories = Category::orderBy('name')->get();

        return view('livewire.store.catalog', compact('products', 'categories'));
    }
}
