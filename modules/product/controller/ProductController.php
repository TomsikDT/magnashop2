<?php
namespace modules\product\controller;

use base\controller\Controller;
use modules\product\model\Product;
use modules\product\model\Category;
use modules\product\model\Dependency;

class ProductController extends Controller
{
    public function list(): void {
        if (empty($_SESSION['user'])) {
            $this->redirect('/login/login');
        }
    
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
    
        $productModel = new Product();
        $products = $productModel->getAllWithQuantity($categoryId);
        $warehouses = $productModel->getAllWarehouses();
    
        $categoryModel = new Category();
        $categories = $categoryModel->getTreeByWarehouse(); // ⬅️ nový strom dle skladu
    
        $childrenMap = [];
        foreach ($categories as $cat) {
            if (!empty($cat['parent_id'])) {
                $childrenMap[$cat['parent_id']] = true;
            }
        }
    
        foreach ($products as &$p) {
            $p['warehouses'] = $productModel->getWarehouseQuantities($p['id']);
        }
    
        $view = $_GET['view'] ?? 'cards';
        $categoryPath = $this->buildCategoryPath($categories, $categoryId);
        $categoryTreeByWarehouse = $categoryModel->getTreeGroupedByWarehouse();

        $this->set('categoryTreeByWarehouse', $categoryTreeByWarehouse);
        $this->set('categoryPath', $categoryPath);
        $this->set('childrenMap', $childrenMap);
        $this->set('categories', $categories);
        $this->set('products', $products);
        $this->set('warehouses', $warehouses);
        $this->set('categoryId', $categoryId);
        $this->setHeader(['title' => 'Seznam produktů']);
        $this->view = $view === 'table' ? 'product/view/list' : 'product/view/card_list';
        $this->vypisView();
    }
    

    private function buildCategoryPath(array $categories, ?int $categoryId): array {
        $path = [];
        $map = [];
        foreach ($categories as $cat) {
            $map[$cat['id']] = $cat;
        }
    
        while ($categoryId && isset($map[$categoryId])) {
            array_unshift($path, $map[$categoryId]);
            $categoryId = $map[$categoryId]['parent_id'];
        }
    
        return $path;
    }
    

    public function add(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }
        error_log(print_r($_POST['categories'] ?? [], true));

        $productModel = new Product();
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        //$warehouseModel = new Warehouse();  // odstranění? Sklady převedeny do modulu produkty  
        //$warehouses = $warehouseModel->getAll();
        $categories = $categoryModel->getTree();
    
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
                // ✅ Vytvoření produktu
                $productId = $productModel->create($name, $description, $price, $vat, $type, $imagePath);
    
                // ✅ Sklady
                foreach ($_POST['warehouse'] ?? [] as $whId => $qty) {
                    $qty = intval($qty);
                    $productModel->setWarehouseQuantity($productId, (int)$whId, $qty);
                }
    
                // ✅ Kategorie
                $categoryIds = $_POST['categories'] ?? [];
                $categoryModel->saveForProduct($productId, $categoryIds);

    
                $this->redirect('/product/list');
            }
        }
    
        //$this->set('warehouses', $warehouses);
        $this->set('categories', $categories);
        $this->set('selectedCategories', []); // při přidávání žádné
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
        $categoryModel = new Category(); // 🆕 přidáno
    
        $id = intval($this->input('product_id'));
        $name = $this->input('name');
        $price = floatval($this->input('price'));
        $vat = floatval($this->input('vat_rate'));
        $type = $this->input('type');
        $categoryIds = $_POST['categories'] ?? []; // 🆕
    
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
    
        // 🏷️ Uložit kategorie
        $categoryModel->saveForProduct($id, $categoryIds); // 🆕
    
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
    public function category(): void {
        if (empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }
    
        $categoryModel = new Category();
        $categories = $categoryModel->getTree();
        
        $childrenMap = [];
        foreach ($categories as $cat) {
            if (!empty($cat['parent_id'])) {
                $childrenMap[$cat['parent_id']] = true;
            }
        }
        
        $this->set('childrenMap', $childrenMap);
        $this->set('categories', $categories);
        $this->setHeader(['title' => 'Kategorie']);
        $this->redirect('/product/list');
    }
    
    public function saveCategory(): void {
        if (empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }
    
        $id = $this->input('category_id');
        $name = $this->input('name');
        $parentId = $this->input('parent_id') ?: null;
    
        $categoryModel = new Category();
    
        if ($id) {
            $categoryModel->update((int)$id, $name, $parentId);
        } else {
            $categoryModel->create($name, $parentId);
        }
    
        $this->redirect('/product/list');
    }

    public function deleteCategory(): void {
        if (empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }
    
        $categoryModel = new Category();
        $categoryModel->softDelete((int)$this->input('category_id'));
    
        $this->redirect('/product/list');
    }
    
    public function restoreCategory(): void {
        if (empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }
    
        $categoryModel = new Category();
        $categoryModel->restore((int)$this->input('category_id'));
    
        $this->redirect('/product/list');
    }

    public function deleteCategoryPernament(): void {
        if (empty($_SESSION['user']['is_admin'])) {
            $this->redirect('/login/login');
        }
    
        $categoryModel = new Category();
        $categoryModel->deletePermanently((int)$this->input('category_id'));
    
        $this->redirect('/product/list');
    }
    

    //dependencies - závislosti

public function dependencyList(): void {
    $productId = (int) $_GET['product_id'];
    $dependencyModel = new Dependency();

    $this->set('product_id', $productId);
    $this->set('dependencies', $dependencyModel->getDependencies($productId));
    $this->set('deletedDependencies', $dependencyModel->getDeletedDependencies($productId));
    $this->view = 'product/view/dependency_modal';
    $this->vypisView();
}

public function addDependency(): void {
    if ($this->isPost()) {
        $productId = (int) $this->input('product_id');
        $depId = (int) $this->input('dependency_product_id');
        $multiplier = (float) $this->input('quantity_multiplier');
        $autoAdd = (int) $this->input('auto_add');
        $note = $this->input('note');

        (new Dependency())->addDependency($productId, $depId, $multiplier, $autoAdd, $note);
    }

    $this->redirect('/product/list');
}

public function deleteDependency(): void {
    if ($this->isPost()) {
        $id = (int) $this->input('dependency_id');
        (new Dependency())->softDelete($id);
    }

    $this->redirect('/product/list');
}

public function restoreDependency(): void {
    if ($this->isPost()) {
        $id = (int) $this->input('dependency_id');
        (new Dependency())->restore($id);
    }

    $this->redirect('/product/list');
}

public function deleteDependencyPermanently(): void {
    if ($this->isPost()) {
        $id = (int) $this->input('dependency_id');
        (new Dependency())->deleteDependencyPermanently($id);
    }

    $this->redirect('/product/list');
}

public function getDependencies(): void {
    if (empty($_SESSION['user'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Neautorizovaný přístup']);
        return;
    }

    $productId = (int) ($_GET['product_id'] ?? 0);
    if (!$productId) {
        echo json_encode([]);
        return;
    }

    $dependencyModel = new \modules\product\model\Dependency();
    $deps = $dependencyModel->getDependencies($productId);
    echo json_encode($deps);
}

public function saveDependency(): void{
    if (empty($_SESSION['user'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Neautorizovaný přístup']);
        return;
    }
    if (!$this->isPost()) {
        $this->redirect('/product/list');
    }

    $productId = (int) $this->input('product_id');
    $depId = (int) $this->input('dependent_product_id');
    $type = $this->input('type'); // fixed nebo work
    $qty = (float) $this->input('quantity');
    $note = $this->input('note');

    $autoAdd = 1; // zatím napevno

    $model = new \modules\product\model\Dependency();
    $model->addDependency($productId, $depId, $qty, $autoAdd, $note);

    $this->redirect('/product/list');
}

public function updateDependency(): void {
    if (empty($_SESSION['user'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Neautorizovaný přístup']);
        return;
    }
    if (!$this->isPost()) {
        $this->redirect('/product/list');
    }

    $id = (int) $this->input('dependency_id');
    $quantity = (float) $this->input('quantity');
    $type = $this->input('type');
    $autoAdd = ($type === 'work') ? 1 : 0;
    $note = $this->input('note');

    (new Dependency())->updateDependency($id, $quantity, $autoAdd, $note);

    $this->redirect('/product/list');
}


public function trash(): void {
    if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
        $this->redirect('/login/login');
    }

    $productModel = new Product();
    $categoryModel = new Category();
    $dependencyModel = new Dependency();

    $this->set('deletedDependencies', $dependencyModel->getDeletedDependencies());
    $this->set('trashed', $productModel->getTrash());
    $this->set('deletedCategories', $categoryModel->getDeleted()); // ✅ Přidáno

    $this->setHeader(['title' => 'Koš produktů a kategorií']);
    $this->view = 'product/view/trash';
    $this->vypisView();
}

    
}
