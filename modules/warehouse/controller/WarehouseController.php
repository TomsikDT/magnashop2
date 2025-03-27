<?php
namespace modules\warehouse\controller;

use base\controller\Controller;
use modules\warehouse\model\Warehouse;

class WarehouseController extends Controller
{
    public function list(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }

        $model = new Warehouse();
        $warehouses = $model->getAllWithStock();

        $this->set('warehouses', $warehouses);
        $this->setHeader(['title' => 'Sklady']);
        $this->view = 'warehouse/view/list';
        $this->vypisView();
    }

    public function products(int $warehouseId): void {
        if (empty($_SESSION['user'])) {
            $this->redirect('/login/login');
        }

        $model = new \modules\warehouse\model\Warehouse();
        $warehouse = $model->getById($warehouseId);
        $products = $model->getProductsInWarehouse($warehouseId);

        if (!$warehouse) {
            $this->redirect('/warehouse/list');
        }

        $this->set('warehouse', $warehouse);
        $this->set('products', $products);
        $this->setHeader(['title' => 'Produkty ve skladu: ' . $warehouse['name']]);
        $this->view = 'warehouse/view/products';
        $this->vypisView();
    }

    public function productsAll(): void{
        if (empty($_SESSION['user'])) {
            $this->redirect('/login/login');
        }

        $model = new \modules\warehouse\model\Warehouse();
        $warehouses = $model->getAll();
        $productsByWarehouse = [];

        foreach ($warehouses as $wh) {
            $productsByWarehouse[$wh['id']] = [
            'warehouse' => $wh,
            'products' => $model->getProductsInWarehouse($wh['id']),
            ];
        }

        $this->set('productsByWarehouse', $productsByWarehouse);
        $this->setHeader(['title' => 'Produkty podle skladů']);
        $this->view = 'warehouse/view/products-all';
        $this->vypisView();
}


}
