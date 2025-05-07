document.addEventListener('DOMContentLoaded', () => {
    // --- Authentication Check (Safety Net) ---
    // Although the script in <head> is primary, this prevents JS errors if accessed directly.
    if (sessionStorage.getItem('isLoggedIn_EliteFootwear') !== 'true') {
        // Avoid running rest of the script if not logged in
        console.warn("User not logged in, cannot initialize script.");
        // Optional: Redirect again if needed, though head script should handle it.
        // window.location.replace('login.html');
        return; // Stop script execution
    }

    // --- Data Storage & Handling (using localStorage) ---
    let stock = {}; // { "ItemName_Size": { name, size, qty, price, supplier, lastUpdated } }
    let sales = []; // [ { name, size, qty, price, timestamp } ]

    function loadData() {
        const storedStock = localStorage.getItem('eliteFootwear_stock');
        const storedSales = localStorage.getItem('eliteFootwear_sales');
        try {
            stock = storedStock ? JSON.parse(storedStock) : {};
            sales = storedSales ? JSON.parse(storedSales) : [];
        } catch (e) {
            console.error("Error loading data from localStorage:", e);
            stock = {}; // Reset to empty on error
            sales = [];
        }
        console.log("Data loaded:", { stock, sales });
    }

    function saveData() {
        try {
            localStorage.setItem('eliteFootwear_stock', JSON.stringify(stock));
            localStorage.setItem('eliteFootwear_sales', JSON.stringify(sales));
            console.log("Data saved.");
        } catch (e) {
            console.error("Error saving data to localStorage:", e);
            // Consider notifying the user if saving fails
            alert("Error: Could not save data. localStorage might be full or disabled.");
        }
    }

    // --- Utility Functions ---
    function formatDateTime(isoString) {
        // (Keep the same formatting function as before)
        if (!isoString) return 'N/A';
        try {
            return new Date(isoString).toLocaleString('en-BD', {
                timeZone: 'Asia/Dhaka', year: 'numeric', month: 'short',
                day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true
            });
        } catch (e) { console.error("Date format error", e); return isoString; }
    }

    function displayMessage(element, message, type, duration = 4000) {
        if (!element) return; // Don't error if element doesn't exist on page
        element.textContent = message;
        // Ensure classes are set correctly, remove previous type
        element.className = 'message'; // Reset classes
        element.classList.add(type); // Add new type
        element.style.display = 'block'; // Make sure it's visible
        setTimeout(() => {
            if (element) { // Check again in case user navigated away
                 element.textContent = '';
                 element.style.display = 'none';
                 element.classList.remove(type);
            }
        }, duration);
    }

    // --- Core UI Update Functions ---

    function updateStockTable() {
        const tableBody = document.getElementById('stock-table-body');
        const totalValueSpan = document.getElementById('total-stock-value');
        const noStockRow = document.getElementById('no-stock-row');
        const noSearchResultsRow = document.getElementById('no-search-results-row');

        // Only run if the table elements exist on the current page
        if (!tableBody || !totalValueSpan || !noStockRow || !noSearchResultsRow) {
            // console.log("Stock table elements not found on this page.");
            return;
        }
        // console.log("Updating stock table...");

        tableBody.innerHTML = ''; // Clear existing rows (excluding message rows which will be added later)
        let totalValue = 0;
        const sortedKeys = Object.keys(stock).sort((a, b) => { /* ... same sort ... */
             const itemA = stock[a]; const itemB = stock[b]; if (!itemA || !itemB) return 0; if (itemA.name < itemB.name) return -1; if (itemA.name > itemB.name) return 1; if (itemA.size < itemB.size) return -1; if (itemA.size > itemB.size) return 1; return 0;
        });
        let hasStockItems = sortedKeys.length > 0;

        if (hasStockItems) {
            sortedKeys.forEach(itemKey => {
                const item = stock[itemKey];
                if (!item) return; // Safety check

                const itemTotalValue = (item.qty || 0) * (item.price || 0);
                totalValue += itemTotalValue;

                const row = document.createElement('tr');
                 // Important: Ensure data attributes and content are correct
                row.innerHTML = `
                    <td>${item.name || 'N/A'}</td>
                    <td>${item.size || 'N/A'}</td>
                    <td>${item.qty || 0}</td>
                    <td>${(item.price || 0).toFixed(2)}</td>
                    <td>${itemTotalValue.toFixed(2)}</td>
                    <td>${item.supplier || 'N/A'}</td>
                    <td>${formatDateTime(item.lastUpdated)}</td>
                    <td><button class="delete-btn" data-item-key="${itemKey}">Delete</button></td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Append message rows (needed for filtering logic)
        tableBody.appendChild(noStockRow);
        tableBody.appendChild(noSearchResultsRow);

        totalValueSpan.textContent = totalValue.toFixed(2);

        // Re-apply filter after updating table
        const searchInput = document.getElementById('search-stock-input');
        filterStockTable(searchInput ? searchInput.value.toLowerCase().trim() : ''); // Apply filter if search exists
    }

    function updateSaleItemSelect() {
        const saleSelect = document.getElementById('sale-item-select');
        // Only run if the select element exists
        if (!saleSelect) {
             // console.log("Sale item select not found on this page.");
             return;
        }
        // console.log("Updating sale item select...");

        const currentSelection = saleSelect.value; // Preserve selection if possible
        saleSelect.innerHTML = '<option value="">-- Select Item --</option>'; // Clear and add default
        const sortedKeys = Object.keys(stock).sort((a, b) => { /* ... same sort ... */
            const itemA = stock[a]; const itemB = stock[b]; if (!itemA || !itemB) return 0; if (itemA.name < itemB.name) return -1; if (itemA.name > itemB.name) return 1; if (itemA.size < itemB.size) return -1; if (itemA.size > itemB.size) return 1; return 0;
        });

        sortedKeys.forEach(itemKey => {
            const item = stock[itemKey];
            if (item && item.qty > 0) { // Only list items with quantity > 0
                const option = document.createElement('option');
                option.value = itemKey;
                option.textContent = `${item.name} - Size: ${item.size} (In Stock: ${item.qty}, Price: ${(item.price || 0).toFixed(2)})`;
                saleSelect.appendChild(option);
            }
        });

        // Try to restore previous selection if it still exists and is valid
        if (stock[currentSelection] && stock[currentSelection].qty > 0) {
            saleSelect.value = currentSelection;
        }
    }


    function updateSalesLog() {
        const logList = document.getElementById('sales-log-list');
        const totalRevenueSpan = document.getElementById('total-sales-revenue');
        const noSalesLi = document.getElementById('no-sales-log-entry');

        // Only run if the elements exist
        if (!logList || !totalRevenueSpan || !noSalesLi) {
            // console.log("Sales log elements not found on this page.");
             return;
        }
        // console.log("Updating sales log...");

        logList.innerHTML = ''; // Clear existing list items only
        let totalRevenue = 0;

        if (sales.length > 0) {
            noSalesLi.style.display = 'none'; // Ensure message is hidden
             // Display latest sales first
            [...sales].reverse().forEach(sale => {
                const saleValue = (sale.qty || 0) * (sale.price || 0);
                totalRevenue += saleValue;
                const listItem = document.createElement('li');
                 // Ensure sale object has name and size
                 listItem.textContent = `${formatDateTime(sale.timestamp)} - Sold ${sale.qty} x ${sale.name || '?'} (Size: ${sale.size || '?'}) @ BDT ${(sale.price || 0).toFixed(2)} each = BDT ${saleValue.toFixed(2)}`;
                logList.appendChild(listItem);
            });
        } else {
            logList.appendChild(noSalesLi); // Add message element back if needed
            noSalesLi.style.display = ''; // Show the 'no sales' message
        }
        totalRevenueSpan.textContent = totalRevenue.toFixed(2);
    }

     function updateDashboardSummary() {
        const summaryStockValue = document.getElementById('summary-stock-value');
        const summarySalesRevenue = document.getElementById('summary-sales-revenue');
        const summaryItemCount = document.getElementById('summary-item-count');
        const summaryUnitCount = document.getElementById('summary-unit-count');

        if (!summaryStockValue || !summarySalesRevenue || !summaryItemCount || !summaryUnitCount) return;

        let totalStockVal = 0;
        let totalUnits = 0;
        let uniqueItems = Object.keys(stock).length;

        for (const key in stock) {
            const item = stock[key];
            totalStockVal += (item.qty || 0) * (item.price || 0);
            totalUnits += (item.qty || 0);
        }

        let totalRevenueVal = 0;
        sales.forEach(sale => {
            totalRevenueVal += (sale.qty || 0) * (sale.price || 0);
        });

        summaryStockValue.textContent = `BDT ${totalStockVal.toFixed(2)}`;
        summarySalesRevenue.textContent = `BDT ${totalRevenueVal.toFixed(2)}`;
        summaryItemCount.textContent = uniqueItems;
        summaryUnitCount.textContent = totalUnits;
    }


    // --- Event Listener Setups (with existence checks) ---

    // Manage Stock Page Logic
    const addStockBtn = document.getElementById('add-stock-btn');
    if (addStockBtn) {
        const itemNameInput = document.getElementById('item-name');
        const itemSizeInput = document.getElementById('item-size');
        const itemQtyInput = document.getElementById('item-qty');
        const itemPriceInput = document.getElementById('item-price');
        const itemSupplierInput = document.getElementById('item-supplier');
        const addStockMessageDiv = document.getElementById('add-stock-message');

        addStockBtn.addEventListener('click', () => {
            // console.log("Add stock button clicked"); // Debug
            // Ensure all input elements exist before reading values
            if (!itemNameInput || !itemSizeInput || !itemQtyInput || !itemPriceInput || !itemSupplierInput || !addStockMessageDiv) {
                 console.error("One or more input elements missing on manage stock page.");
                 return;
            }

            const name = itemNameInput.value.trim();
            const size = itemSizeInput.value.trim();
            const qtyToAdd = parseInt(itemQtyInput.value);
            const price = parseFloat(itemPriceInput.value);
            const supplier = itemSupplierInput.value.trim();
            const now = new Date().toISOString();

            // Enhanced Validation
            if (!name || !size || !supplier) {
                displayMessage(addStockMessageDiv, 'Error: Style Name, Size, and Supplier cannot be empty.', 'error'); return;
            }
            if (isNaN(qtyToAdd) || qtyToAdd <= 0) {
                displayMessage(addStockMessageDiv, 'Error: Please enter a valid quantity to add (> 0).', 'error'); return;
            }
            if (isNaN(price) || price < 0) {
                displayMessage(addStockMessageDiv, 'Error: Please enter a valid Unit Price (>= 0).', 'error'); return;
            }

            const itemKey = `${name}_${size}`;
            let message = '';
            if (stock[itemKey]) {
                stock[itemKey].qty += qtyToAdd;
                stock[itemKey].price = price;
                stock[itemKey].supplier = supplier;
                stock[itemKey].lastUpdated = now;
                message = `Success: Added ${qtyToAdd} to "${name} - Size: ${size}". New quantity: ${stock[itemKey].qty}. Details updated.`;
            } else {
                stock[itemKey] = { name, size, qty: qtyToAdd, price, supplier, lastUpdated: now };
                 message = `Success: Added new item "${name} - Size: ${size}" (${qtyToAdd} units).`;
            }

            saveData(); // Save immediately after modification
            displayMessage(addStockMessageDiv, message, 'success');

            // Clear inputs after adding
            itemNameInput.value = ''; itemSizeInput.value = ''; itemQtyInput.value = '';
            itemPriceInput.value = ''; itemSupplierInput.value = '';
            itemNameInput.focus();
        });
    } else {
         // console.log("Add stock button not found on this page.");
    }

    // Record Sale Page Logic
    const recordSaleBtn = document.getElementById('record-sale-btn');
    if (recordSaleBtn) {
        const saleItemSelect = document.getElementById('sale-item-select');
        const saleQtyInput = document.getElementById('sale-qty');
        const saleMessageDiv = document.getElementById('sale-message');

        // Populate the dropdown when the page loads
        updateSaleItemSelect();

        recordSaleBtn.addEventListener('click', () => {
            // console.log("Record sale button clicked"); // Debug
             if (!saleItemSelect || !saleQtyInput || !saleMessageDiv) {
                 console.error("One or more elements missing on record sale page.");
                 return;
             }

            const itemKey = saleItemSelect.value;
            const qtySold = parseInt(saleQtyInput.value);

            if (!itemKey) {
                displayMessage(saleMessageDiv, 'Error: Please select an item.', 'error'); return;
            }
            if (isNaN(qtySold) || qtySold <= 0) {
                displayMessage(saleMessageDiv, 'Error: Please enter a valid quantity (> 0).', 'error'); return;
            }

            const itemInStock = stock[itemKey];

            if (!itemInStock) {
                displayMessage(saleMessageDiv, `Error: Item key "${itemKey}" invalid state. Reload may be needed.`, 'error'); return;
            }
            if (itemInStock.qty < qtySold) {
                displayMessage(saleMessageDiv, `Error: Not enough stock for "${itemInStock.name} - Size: ${itemInStock.size}". Only ${itemInStock.qty} available.`, 'error'); return;
            }

            const priceAtSale = itemInStock.price;
            itemInStock.qty -= qtySold;

            const saleRecord = {
                // key: itemKey, // Optional: store key if needed later
                name: itemInStock.name,
                size: itemInStock.size,
                qty: qtySold,
                price: priceAtSale,
                timestamp: new Date().toISOString()
            };
            sales.push(saleRecord);

            saveData(); // Save immediately
            displayMessage(saleMessageDiv, `Success: Recorded sale of ${qtySold} x ${itemInStock.name} (Size: ${itemInStock.size}).`, 'success');

            // Update the dropdown to reflect new stock
            updateSaleItemSelect();
            // Clear sale quantity input
            saleQtyInput.value = '';
        });
    } else {
         // console.log("Record sale button not found on this page.");
    }


    // View Stock Page Logic (Search & Delete)
    const searchInput = document.getElementById('search-stock-input');
    const stockTableBodyForDelete = document.getElementById('stock-table-body'); // Need separate var for this scope

    if (searchInput && stockTableBodyForDelete) {
        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase().trim();
            filterStockTable(searchTerm);
        });

         // Event delegation for delete buttons
        stockTableBodyForDelete.addEventListener('click', (event) => {
             if (event.target.classList.contains('delete-btn')) {
                 const itemKey = event.target.getAttribute('data-item-key');
                 const item = stock[itemKey];
                 if (item && confirm(`Are you sure you want to delete ALL stock of "${item.name} - Size: ${item.size}"? This cannot be undone.`)) {
                     delete stock[itemKey]; // Remove item
                     saveData(); // Save changes
                     updateStockTable(); // Refresh the table on this page
                     // Note: updateSaleItemSelect() should be called if navigating back to sale page,
                     // but here we just update the current view.
                 }
             }
         });

    } else {
         // console.log("Search input or stock table body not found on this page.");
    }

     // Function for Search Filter Logic (needed on View Stock page)
     function filterStockTable(searchTerm) {
         const tableBody = document.getElementById('stock-table-body');
         const noStockRow = document.getElementById('no-stock-row');
         const noSearchResultsRow = document.getElementById('no-search-results-row');
         if (!tableBody || !noStockRow || !noSearchResultsRow) return; // Only run if table exists

         const rows = tableBody.querySelectorAll('tr:not(#no-stock-row):not(#no-search-results-row)');
         let matchFound = false;
         let hasStockItems = false;

         rows.forEach(row => {
             hasStockItems = true;
             const nameCell = row.cells[0]; const sizeCell = row.cells[1]; const supplierCell = row.cells[5];
             const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
             const sizeText = sizeCell ? sizeCell.textContent.toLowerCase() : '';
             const supplierText = supplierCell ? supplierCell.textContent.toLowerCase() : '';

             if (nameText.includes(searchTerm) || sizeText.includes(searchTerm) || supplierText.includes(searchTerm)) {
                 row.style.display = ''; matchFound = true;
             } else {
                 row.style.display = 'none';
             }
         });

         // Show/Hide messages based on state after filtering
         noStockRow.style.display = !hasStockItems ? '' : 'none';
         noSearchResultsRow.style.display = hasStockItems && !matchFound && searchTerm !== '' ? '' : 'none'; // Show only if searching and no results
     }


    // Logout Button Logic (Common to all pages except login)
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            sessionStorage.removeItem('isLoggedIn_EliteFootwear');
            // Optional: Clear localStorage too if desired on logout
            // localStorage.removeItem('eliteFootwear_stock');
            // localStorage.removeItem('eliteFootwear_sales');
            window.location.href = 'login.html';
        });
    }

    // --- Initial Load ---
    loadData(); // Load data when any authenticated page loads

    // Initial UI updates based on the current page
    updateDashboardSummary(); // Update dashboard if elements are present
    updateStockTable(); // Update stock table if elements are present
    updateSaleItemSelect(); // Populate sale dropdown if elements are present
    updateSalesLog(); // Update sales log if elements are present

}); // End DOMContentLoaded listener