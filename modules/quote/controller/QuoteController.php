<?php
namespace modules\quote\controller;

use base\controller\Controller;
use modules\product\model\Product;
use modules\quote\model\Quote;


class QuoteController extends Controller
{

    public function create(): void {
        if (empty($_SESSION['user']) || empty($_SESSION['quote'])) {
            $this->redirect('/quote/cart');
        }
    
        $productModel = new \modules\product\model\Product();
        $cart = $_SESSION['quote'];
        $items = [];
    
        $totalWithoutVat = 0;
        $totalWithVat = 0;
    
        foreach ($cart as $productId => $qty) {
            $product = $productModel->getById($productId);
            if (!$product) continue;
    
            $product['qty'] = $qty;
            $product['total'] = $product['price'] * $qty;
            $product['total_vat'] = $product['total'] * (1 + $product['vat_rate'] / 100);
    
            $totalWithoutVat += $product['total'];
            $totalWithVat += $product['total_vat'];
    
            $items[] = $product;
        }
    
        $this->set('items', $items);
        $this->set('totalWithoutVat', $totalWithoutVat);
        $this->set('totalWithVat', $totalWithVat);
        $this->setHeader(['title' => 'Vytvořit nabídku']);
        $this->view = 'quote/view/create';
        $this->vypisView();
    }
    

    public function cart(): void {
        if (empty($_SESSION['user'])) {
            $this->redirect('/login/login');
        }

        $productModel = new Product();
        $cart = $_SESSION['quote'] ?? [];

        $items = [];
        $totalWithoutVat = 0;
        $totalWithVat = 0;

        foreach ($cart as $productId => $qty) {
            $product = $productModel->getById($productId);
            if (!$product) continue;

            $product['qty'] = $qty;
            $product['total'] = $product['price'] * $qty;
            $product['total_vat'] = $product['total'] * (1 + $product['vat_rate'] / 100);

            $totalWithoutVat += $product['total'];
            $totalWithVat += $product['total_vat'];

            $items[] = $product;
        }

        $this->set('items', $items);
        $this->set('totalWithoutVat', $totalWithoutVat);
        $this->set('totalWithVat', $totalWithVat);
        $this->setHeader(['title' => 'Tvorba nabídky']);
        $this->view = 'quote/view/cart';
        $this->vypisView();
    }

    public function add(int $productId): void {
        if (!isset($_SESSION['quote'][$productId])) {
            $_SESSION['quote'][$productId] = 1;
        } else {
            $_SESSION['quote'][$productId]++;
        }
        $this->redirect('/quote/cart');
    }

    public function remove(int $productId): void {
        unset($_SESSION['quote'][$productId]);
        $this->redirect('/quote/cart');
    }

    public function update(): void {
        foreach ($_POST['qty'] ?? [] as $id => $qty) {
            $_SESSION['quote'][(int)$id] = max(1, (int)$qty);
        }
        $this->redirect('/quote/cart');
    }

    public function clear(): void {
        unset($_SESSION['quote']);
        $this->redirect('/quote/cart');
    }
    
    public function generate(): void {
        if (!$this->isPost() || empty($_SESSION['quote'])) {
            $this->redirect('/quote/cart');
        }
    
        $productModel = new \modules\product\model\Product();
        $quoteModel = new \modules\quote\model\Quote();
    
        $items = [];
        $totalWithoutVat = 0;
        $totalWithVat = 0;
    
        foreach ($_SESSION['quote'] as $productId => $qty) {
            $product = $productModel->getById($productId);
            if (!$product) continue;
    
            $product['qty'] = $qty;
            $product['total'] = $product['price'] * $qty;
            $product['total_vat'] = $product['total'] * (1 + $product['vat_rate'] / 100);
            $items[] = $product;
    
            $totalWithoutVat += $product['total'];
            $totalWithVat += $product['total_vat'];
        }
    
        // Data z formuláře
        $user_id = $_SESSION['user']['id'];
        $customer_name = $this->input('customer_name');
        $customer_email = $this->input('customer_email');
        $customer_note = $this->input('customer_note') ?? '';
        $note_top = $this->input('note_top') ?? '';
        $note_bottom = $this->input('note_bottom') ?? '';
    
        // Generování čísla nabídky
        $dateToday = date('Y-m-d');
        $dailyCount = $quoteModel->countQuotesForDate($dateToday) + 1;
        $quoteNumber = date('Ymd') . str_pad($dailyCount, 2, '0', STR_PAD_LEFT); // např. 2025032901
    
        // Uložení nabídky
        $quoteId = $quoteModel->createQuote(
            $user_id,
            $customer_name,
            $customer_email,
            $customer_note,
            $note_top,
            $note_bottom,
            $totalWithoutVat,
            $totalWithVat,
            $items,
            $quoteNumber
        );
    
        // 📄 Generování PDF
        $created_at = date('Y-m-d H:i:s'); // pro zobrazení v šabloně
        ob_start();
        require 'modules/quote/view/pdf_template.php';
        $html = ob_get_clean();
    
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("nabidka_$quoteNumber.pdf", ['Attachment' => false]);
    }
    

    public function list(): void {
        if (empty($_SESSION['user'])) {
            $this->redirect('/login/login');
        }
    
        $quoteModel = new \modules\quote\model\Quote();
        $quotes = $quoteModel->getAllQuotes();
    
        $this->set('quotes', $quotes);
        $this->setHeader(['title' => 'Přehled nabídek']);
        $this->view = 'quote/view/list';
        $this->vypisView();
    }
    
    public function show(int $id): void {
        $quoteModel = new \modules\quote\model\Quote();
        $quote = $quoteModel->getById($id);
        $items = $quoteModel->getItems($id);
    
        if (!$quote) {
            $this->redirect('/quote/list');
        }
    
        extract($quote); // pro šablonu
    
        ob_start();
        require 'modules/quote/view/pdf_template.php';
        $html = ob_get_clean();
    
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("nabidka_$id.pdf", ['Attachment' => false]);
    }

    
}
