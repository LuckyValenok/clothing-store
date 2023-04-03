<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static find(mixed $brand_id)
 * @method static where(string $string, $slug)
 * @method static create(array $data)
 * @property mixed $id
 * @property mixed $products
 */
class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'image',
    ];

    /**
     * Возвращает список популярных брендов каталога товаров.
     * Следовало бы отобрать бренды, товары которых продаются
     * чаще всего. Но поскольку таких данных у нас еще нет,
     * просто получаем 5 брендов с наибольшим кол-вом товаров
     */
    public static function popular()
    {
        return self::withCount('products')->orderByDesc('products_count')->limit(5)->get();
    }

    /**
     * Связь «один ко многим» таблицы `brands` с таблицей `products`
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
