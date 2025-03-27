// 🌍 Globální funkce pro otevření modalu úpravy produktu
function openEditModal(productId) {
    const product = window.products.find(p => p.id == productId);
    if (!product) return;

    document.getElementById('edit-id').value = product.id;
    document.getElementById('edit-name').value = product.name;
    document.getElementById('edit-price').value = product.price;
    document.getElementById('edit-vat').value = product.vat_rate;
    document.getElementById('edit-type').value = product.type;

    for (const whId in product.warehouses) {
        const input = document.getElementById('edit-wh-' + whId);
        if (input) input.value = product.warehouses[whId];
    }

    document.getElementById('editProductModal').style.display = 'flex';
}

// Funkce pro otevření modalu pro přidání produktu
function openAddModal() {
    document.getElementById('addProductModal').style.display = 'flex';
}

// Funkce pro otevření modalu pro správu skladů
function openWarehouseModal() {
    document.getElementById('warehouseModal').style.display = 'flex';
}

// Funkce pro přidání produktu do nabídky
function addToQuote(productId) {
    window.location.href = '/quote/add/' + productId;
}


// Zavření  modalu tlačítkem
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
// zavření modalu kliknutím mimo
document.addEventListener('click', function (e) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
//zavření modalu escapem
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => modal.style.display = 'none');
    }
});
