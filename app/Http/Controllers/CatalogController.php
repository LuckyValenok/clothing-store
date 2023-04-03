<?php

namespace App\Http\Controllers;

use App\Helpers\ProductFilter;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        // корневые категории
        $roots = Category::where('parent_id', 0)->get();
        // популярные бренды
        $brands = Brand::popular();
        return view('catalog.index', compact('roots', 'brands'));
    }

    public function category(Category $category, ProductFilter $filters): View
    {
        $products = Product::categoryProducts($category->id) // товары категории и всех ее потомков
        ->filterProducts($filters) // фильтруем товары категории и всех ее потомков
        ->paginate(6)
            ->withQueryString();
        return view('catalog.category', compact('category', 'products'));
    }

    public function brand(Brand $brand, ProductFilter $filters): View
    {
        $products = $brand
            ->products() // возвращает построитель запроса
            ->filterProducts($filters)
            ->paginate(6)
            ->withQueryString();
        return view('catalog.brand', compact('brand', 'products'));
    }

    public function product(Product $product): View
    {
        return view('catalog.product', compact('product'));
    }

    public function search(Request $request): View
    {
        $search = $request->input('query');
        $query = Product::search($search);
        $products = $query->paginate(6)->withQueryString();
        return view('catalog.search', compact('products', 'search'));
    }
}
