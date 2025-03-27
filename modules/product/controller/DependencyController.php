<?php
namespace modules\product\controller;

use base\controller\Controller;
use modules\product\model\Dependency;
use modules\product\model\Product;

class DependencyController extends Controller
{
    public function index(int $productId): void
    {

        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }

        $depModel = new Dependency();
        $productModel = new Product();

        if ($this->isPost()) {
            $depProductId = intval($this->input('dependency_product_id'));
            $multiplier = floatval($this->input('quantity_multiplier'));
            $autoAdd = $this->input('auto_add') === '1' ? 1 : 0;
            $note = $this->input('note');

            $depModel->addDependency($productId, $depProductId, $multiplier, $autoAdd, $note);
            $this->redirect('/product/dependency/index' . $productId);
        }

        $product = $productModel->getById($productId);
        $dependencies = $depModel->getDependencies($productId);
        $allProducts = $productModel->getAll();

        $this->set('product', $product);
        $this->set('dependencies', $dependencies);
        $this->set('allProducts', $allProducts);
        $this->setHeader(['title' => 'Závislosti produktu']);
        $this->view = 'product/view/dependency/index';
        $this->vypisView();
    }
}
