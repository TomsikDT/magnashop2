<?php
namespace modules\product\controller;

use base\controller\Controller;
use modules\product\model\Product;

class ProductController extends Controller
{
    public function list(): void {
        if (empty($_SESSION['user'])) {
            $this->redirect('/login/login');
        }

        $productModel = new Product();
        $products = $productModel->getAllWithQuantity(); // předpokládá i skladové množství
        $warehouses = $productModel->getAllWarehouses(); // přidáme i sklady pro modály

        $this->set('products', $products);
        $this->set('warehouses', $warehouses);
        $this->setHeader(['title' => 'Seznam produktů']);
        $this->view = 'product/view/list';
        $this->vypisView(); 
    }

    public function add(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }

        $productModel = new Product();
        $warehouses = $productModel->getAllWarehouses();

        if ($this->isPost()) {
            $name = $this->input('name');
            $description = $this->input('description');
            $price = floatval($this->input('price'));
            $vat = floatval($this->input('vat_rate'));
            $type = $this->input('type');

            // 🖼️ Upload obrázku
            $imagePath = '';
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = 'src/uploads/';
                $filename = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $uploadDir . $filename;

                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = $targetFile;
                }
            }

            if (!$name || !$price || !$vat || !in_array($type, ['product', 'work'])) {
                $this->set('error', 'Vyplňte všechna pole správně.');
            } else {
                $productId = $productModel->create($name, $description, $price, $vat, $type, $imagePath);

                foreach ($_POST['warehouse'] ?? [] as $whId => $qty) {
                    $qty = intval($qty);
                    $productModel->setWarehouseQuantity($productId, (int)$whId, $qty);
                }

                $this->redirect('/product/list');
            }
        }

        $this->set('warehouses', $warehouses);
        $this->setHeader(['title' => 'Přidat produkt']);
        $this->view = 'product/view/add';
        $this->vypisView();
    }

    public function update(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }

        if (!$this->isPost()) {
            $this->redirect('/product/list');
        }

        $productModel = new Product();

        $id = intval($this->input('product_id'));
        $name = $this->input('name');
        $price = floatval($this->input('price'));
        $vat = floatval($this->input('vat_rate'));
        $type = $this->input('type');

        // 🖼️ Pokud je nový obrázek, nahraj ho
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = 'src/uploads/';
            $filename = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $filename;

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            }
        }

        $productModel->update($id, $name, $price, $vat, $type, $imagePath);

        foreach ($_POST['warehouse'] ?? [] as $whId => $qty) {
            $productModel->setWarehouseQuantity($id, (int)$whId, (int)$qty);
        }

        $this->redirect('/product/list');
    }

    public function delete(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }

        if ($this->isPost()) {
            $id = intval($this->input('product_id'));
            $model = new Product();
            $model->delete($id);
        }

        $this->redirect('/product/list');
    }

    public function warehouseAdd(): void {
        if ($this->isPost()) {
            $db = \base\model\Database::getConnection();
            $stmt = $db->prepare("INSERT INTO warehouses (name, address, note) VALUES (?, ?, ?)");
            $stmt->execute([
                $this->input('name'),
                $this->input('address'),
                $this->input('note')
            ]);
        }
        $this->redirect('/product/list');
    }
    
    public function warehouseUpdate(): void {
        if ($this->isPost()) {
            $db = \base\model\Database::getConnection();
            $stmt = $db->prepare("UPDATE warehouses SET name = ?, address = ?, note = ? WHERE id = ?");
            $stmt->execute([
                $this->input('name'),
                $this->input('address'),
                $this->input('note'),
                intval($this->input('warehouse_id'))
            ]);
        }
        $this->redirect('/product/list');
    }
    
    public function warehouseDelete(): void {
        if ($this->isPost()) {
            $db = \base\model\Database::getConnection();
            $stmt = $db->prepare("DELETE FROM warehouses WHERE id = ?");
            $stmt->execute([intval($this->input('warehouse_id'))]);
        }
        $this->redirect('/product/list');
    }

    public function trash(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }
    
        $model = new Product();
        $this->set('trashed', $model->getTrash());
        $this->setHeader(['title' => 'Koš produktů']);
        $this->view = 'product/view/trash';
        $this->vypisView();
    }
    
    public function restore(): void {
        if ($this->isPost() && isset($_POST['product_id'])) {
            (new Product())->restore((int)$_POST['product_id']);
        }
        $this->redirect('/product/trash');
    }
    
    public function restoreAll(): void {
        (new Product())->restoreAll();
        $this->redirect('/product/trash');
    }
    
    public function deletePermanent(): void {
        if ($this->isPost() && isset($_POST['product_id'])) {
            (new Product())->deletePermanently((int)$_POST['product_id']);
        }
        $this->redirect('/product/trash');
    }
    
    public function deleteAllPermanent(): void {
        (new Product())->deleteAllPermanently();
        $this->redirect('/product/trash');
    }
    
    
}
