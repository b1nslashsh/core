<?php
namespace SCart\Core\Front\Controllers;

use App\Http\Controllers\RootFrontController;
use SCart\Core\Front\Models\ShopSubCategory;
use SCart\Core\Front\Models\ShopProduct;

class ShopSubCategoryController extends RootFrontController
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Category detail: list category child + product list
     * @param  [string] $alias
     * @return [view]
     */
    public function categoryDetail($alias, $storeCode)
    {
        $sortBy = 'sort';
        $sortOrder = 'asc';
        $filter_sort = request('filter_sort') ?? '';
        $filterArr = [
            'price_desc' => ['price', 'desc'],
            'price_asc' => ['price', 'asc'],
            'sort_desc' => ['sort', 'desc'],
            'sort_asc' => ['sort', 'asc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];
        if (array_key_exists($filter_sort, $filterArr)) {
            $sortBy = $filterArr[$filter_sort][0];
            $sortOrder = $filterArr[$filter_sort][1];
        }

        $category = (new ShopSubCategory)->getDetail($alias, $type = 'alias', $storeCode);

        if ($category) {
            $products = (new ShopProduct)
                ->getProductToCategory([$category->id])
                ->setLimit(sc_config('product_list'))
                ->setStore($category->store_id)
                ->setSort([$sortBy, $sortOrder])
                ->setPaginate()
                ->getData();

            sc_check_view($this->templatePath . '.store.store_product_list');
            return view($this->templatePath . '.store.store_product_list',
                array(
                    'title' => $category->title,
                    'description' => $category->description,
                    'keyword' => $category->keyword,
                    'products' => $products,
                    'layout_page' => 'store_product_list',
                    'og_image' => asset($category->getImage()),
                    'filter_sort' => $filter_sort,
                )
            );
        } else {
            return $this->itemNotFound();
        }

    }

}