// Globální funkce pro otevření modalu úpravy produktu
function openEditModal(productId) {
    // Najdeme správný element podle view
    const isTableView = window.location.href.includes('view=table');
    const row = isTableView
        ? document.querySelector(`tr[data-product-id="${productId}"]`)
        : document.querySelector(`.product-card[data-product-id="${productId}"]`);

    if (!row) {
        console.warn('Řádek/karta produktu nenalezena');
        return;
    }

    const product = window.products.find(p => p.id == productId);
    if (!product) {
        console.warn('Produkt nenalezen ve window.products');
        return;
    }

    document.getElementById('edit-id').value = product.id;
    document.getElementById('edit-name').value = product.name;
    document.getElementById('edit-price').value = product.price;
    document.getElementById('edit-vat').value = product.vat_rate;
    document.getElementById('edit-type').value = product.type;

    if (product.warehouses) {
        for (const whId in product.warehouses) {
            const input = document.getElementById('edit-wh-' + whId);
            if (input) input.value = product.warehouses[whId];
        }
    }

    // Kategorie z atributu
    const modal = document.getElementById('editProductModal');
    const checkboxes = modal.querySelectorAll('#edit-categories-checkboxes input[type="checkbox"]');

    const catStr = row.dataset.categoryIds || '';
    const selectedCats = catStr.split(',').map(id => parseInt(id));

    checkboxes.forEach(cb => {
        cb.checked = selectedCats.includes(parseInt(cb.value));
    });

    document.getElementById('editProductModal').style.display = 'flex';
}


//funkce pro otevření modalu dependecies
function openDependencyModal(productId) {
    const modal = document.getElementById('dependencyModal');

    // Nastavíme ID produktu do hidden inputu
    modal.querySelector('input[name="product_id"]').value = productId;

    // Vyčistíme staré řádky
    const tableBody = modal.querySelector('#dependency-list');
    tableBody.innerHTML = '';

    // Načteme závislosti
    fetch(`/product/getDependencies?product_id=${productId}`)
        .then(res => res.json())
        .then(data => {
            console.log('Závislosti:', data);
            if (!Array.isArray(data)) return;

            data.forEach(dep => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${dep.dependency_name}</td>
                    <td>${dep.dependency_type === 'work' ? 'Práce' : 'Pevná'}</td>
                    <td>${dep.quantity_multiplier}</td>
                    <td>${dep.note ?? ''}</td>
                    <td>
                        <form method="post" action="/product/deleteDependency" onsubmit="return confirm('Odebrat závislost?');">
                            <input type="hidden" name="dependency_id" value="${dep.id}">
                            <input type="hidden" name="product_id" value="${dep.product_id}">
                            <button type="submit">🗑️</button>
                        </form>
                        <button type="button" onclick='editDependency(${JSON.stringify(dep)})'>✏️</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        });

    // Zobrazíme modal
    modal.style.display = 'flex';
}


// funkce pro editaci závislostí
function editDependency(dep) {
    const modal = document.getElementById('dependencyModal');

    // Předvyplníme formulář
    modal.querySelector('input[name="product_id"]').value = dep.product_id;
    modal.querySelector('select[name="dependent_product_id"]').value = dep.dependency_product_id;
    modal.querySelector('select[name="type"]').value = dep.dependency_type === 'work' ? 'work' : 'fixed';
    modal.querySelector('input[name="quantity"]').value = dep.quantity_multiplier;
    modal.querySelector('input[name="note"]').value = dep.note ?? '';

    // Změníme akci formuláře
    modal.querySelector('form').action = '/product/updateDependency';

    // Přidáme skrytý input s ID závislosti, pokud tam není
    let idInput = modal.querySelector('input[name="dependency_id"]');
    if (!idInput) {
        idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'dependency_id';
        modal.querySelector('form').appendChild(idInput);
    }
    idInput.value = dep.id;
}


// Funkce pro otevření modalu pro přidání produktu
function openAddModal() {
    const modal = document.getElementById('addProductModal');
    const checkboxes = modal.querySelectorAll('#add-categories-checkboxes input[type="checkbox"]');

    // Vynuluj výběr
    checkboxes.forEach(cb => cb.checked = false);

    modal.style.display = 'flex';
}


// Funkce pro otevření modalu pro správu skladů
function openWarehouseModal() {
    document.getElementById('warehouseModal').style.display = 'flex';
}

// Funkce pro přidání produktu do nabídky
function addToQuote(productId) {
    window.location.href = '/quote/add/' + productId;
}

// funkce pro otevření modalu s kategoriemi
function openCategoryModal() {
    document.getElementById('categoryModal').style.display = 'flex';
}

// funkce pro edit kategorií
function editCategory(id, name, parentId) {
    document.getElementById('category_id').value = id;
    document.getElementById('category_name').value = name;
    document.getElementById('category_parent').value = parentId ?? '';
    document.getElementById('category_submit').textContent = 'Uložit změny';
}

// rozbalování kategorií
function toggleSubcategories(rowEl) {
    const parentId = rowEl.dataset.id;
    const toggle = rowEl.querySelector('.category-toggle');

    const children = document.querySelectorAll(`.category-item[data-parent="${parentId}"]`);
    const anyVisible = Array.from(children).some(child => !child.classList.contains('hidden'));


    children.forEach(child => {
        if (anyVisible) {
            hideWithChildren(child);
        } else {
            child.classList.remove('hidden');
        }
    });

    // ✨ Měníme text pouze pokud tam nějaký je
    if (toggle && toggle.textContent.trim()) {
        toggle.textContent = anyVisible ? '+' : '−';
    }
}

function hideWithChildren(el) {
    const id = el.dataset.id;
    el.classList.add('hidden');

    const subChildren = document.querySelectorAll(`.category-item[data-parent="${id}"]`);
    subChildren.forEach(sub => hideWithChildren(sub));
}



function toggleAllCategories(collapse = false) {
    const items = document.querySelectorAll('.category-item');
    const toggles = document.querySelectorAll('.category-toggle');

    items.forEach(item => {
        if (item.dataset.parent) {
            item.classList.toggle('hidden', collapse);
        }
    });

    toggles.forEach(t => {
        if (t.textContent.trim() === '+' || t.textContent.trim() === '−') {
            t.textContent = collapse ? '+' : '−';
        }
    });
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
