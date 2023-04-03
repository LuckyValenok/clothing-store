<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageSaver;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandCatalogRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    private ImageSaver $imageSaver;

    public function __construct(ImageSaver $imageSaver)
    {
        $this->imageSaver = $imageSaver;
    }

    /**
     * Показывает список всех брендов
     *
     * @return View
     */
    public function index(): View
    {
        $brands = Brand::all();
        return view('admin.brand.index', compact('brands'));
    }

    /**
     * Сохраняет новый бренд в базу данных
     *
     * @param BrandCatalogRequest $request
     * @return RedirectResponse
     */
    public function store(BrandCatalogRequest $request): RedirectResponse
    {
        $data = $request->all();
        $data['image'] = $this->imageSaver->upload($request, null, 'brand');
        $brand = Brand::create($data);
        return redirect()
            ->route('admin.brand.show', ['brand' => $brand->id])
            ->with('success', 'Новый бренд успешно создан');
    }

    /**
     * Показывает форму для создания бренда
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.brand.create');
    }

    /**
     * Показывает страницу бренда
     *
     * @param Brand $brand
     * @return View
     */
    public function show(Brand $brand): View
    {
        return view('admin.brand.show', compact('brand'));
    }

    /**
     * Показывает форму для редактирования бренда
     *
     * @param Brand $brand
     * @return View
     */
    public function edit(Brand $brand): View
    {
        return view('admin.brand.edit', compact('brand'));
    }

    /**
     * Обновляет бренд (запись в таблице БД)
     *
     * @param BrandCatalogRequest $request
     * @param Brand $brand
     * @return RedirectResponse
     */
    public function update(BrandCatalogRequest $request, Brand $brand): RedirectResponse
    {
        $data = $request->all();
        $data['image'] = $this->imageSaver->upload($request, $brand, 'brand');
        $brand->update($data);
        return redirect()
            ->route('admin.brand.show', ['brand' => $brand->id])
            ->with('success', 'Бренд был успешно отредактирован');
    }

    /**
     * Удаляет бренд (запись в таблице БД)
     *
     * @param Brand $brand
     * @return RedirectResponse
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products->count()) {
            return back()->withErrors('Нельзя удалить бренд, у которого есть товары');
        }
        $this->imageSaver->remove($brand, 'brand');
        $brand->delete();
        return redirect()
            ->route('admin.brand.index')
            ->with('success', 'Бренд каталога успешно удален');
    }
}
