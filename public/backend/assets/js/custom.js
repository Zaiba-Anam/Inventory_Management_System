document.addEventListener("DOMContentLoaded", function () {
    let productSearchInput = document.getElementById("product_search");
    let warehouseDropdown = document.getElementById("warehouse_id");
    let productList = document.getElementById("product_list");
    let warehouseError = document.getElementById("warehouse_error");
    let orderItemsTableBody = document.querySelector("tbody");

    productSearchInput.addEventListener("keyup", function () {
        let query = this.value;
        let warehouse_id = warehouseDropdown.value;

        if (!warehouse_id) {
            warehouseError.classList.remove("d-none");
            productList.innerHTML = "";
            return;
        } else {
            warehouseError.classList.add("d-none");
        }
        if (query.length > 1) {
            fetchProducts(query, warehouse_id);
        } else {
            productList.innerHTML = "";
        }
    });

    function fetchProducts(query, warehouse_id) {
        fetch(
            productSearchUrl +
                "?query=" +
                query +
                "&warehouse_id=" +
                warehouse_id,
        )
            .then((response) => response.json())
            .then((data) => {
                productList.innerHTML = "";
                if (data.length > 0) {
                    data.forEach((product) => {
                        let item = `<a href="#" class="list-group-item list-group-item-action product-item"
                            data-id="${product.id}"
                            data-code="${product.code}"
                            data-name="${product.name}"
                            data-cost="${product.price}"
                            data-stock="${product.product_qty}">
                            <span class="mdi mdi-text-search"></span>
                            ${product.code} - ${product.name}
                            </a> `;
                        productList.innerHTML += item;
                        // console.log(item);
                    });

                    // add event listener for product selection
                    document
                        .querySelectorAll(".product-item")
                        .forEach((item) => {
                            item.addEventListener("click", function (e) {
                                e.preventDefault();
                                addProductToTable(this);
                            });
                        });
                } else {
                    productList.innerHTML =
                        '<p class="text-muted">No Product Found</p>';
                }
            });
    }

    ///// Add Product in to the table
    function addProductToTable(productElement) {
        let productId = productElement.getAttribute("data-id");
        let productCode = productElement.getAttribute("data-code");
        let productName = productElement.getAttribute("data-name");
        let netUnitCost = parseFloat(productElement.getAttribute("data-cost"));
        let stock = parseInt(productElement.getAttribute("data-stock"));

        // Check if product already exists in table
        if (document.querySelector(`tr[data-id="${productId}"]`)) {
            alert("Product already added.");
            return;
        }

        let row = `
      <tr data-id="${productId}">
          <td>
              ${productCode} - ${productName}
              <button type="button" class="btn btn-primary btn-sm edit-discount-btn"
                  data-id="${productId}"
                  data-name="${productName}"
                  data-cost="${netUnitCost}"
                  data-bs-toggle="modal">
                  <span class="mdi mdi-book-edit "></span>
              </button>
              <input type="hidden" name="products[${productId}][id]" value="${productId}">
              <input type="hidden" name="products[${productId}][name]" value="${productName}">
              <input type="hidden" name="products[${productId}][code]" value="${productCode}">
          </td>
          <td>${netUnitCost.toFixed(2)}
              <input type="hidden" name="products[${productId}][cost]" value="${netUnitCost}">
          </td>
          <td style="color:#ffc121">${stock}</td>
          <td>
              <div class="input-group">
                  <button class="btn btn-outline-secondary decrement-qty" type="button">−</button>
                  <input type="text" class="form-control text-center qty-input"
                      name="products[${productId}][quantity]" value="1" min="1" max="${stock}"
                      data-cost="${netUnitCost}" style="width: 30px;">
                  <button class="btn btn-outline-secondary increment-qty" type="button">+</button>
              </div>
          </td>
          <td>
              <input type="number" class="form-control discount-input"
                  name="products[${productId}][discount]" value="0" min="0" style="width:100px">
          </td>
          <td class="subtotal">${netUnitCost.toFixed(2)}</td>
          <td><button class="btn btn-danger btn-sm remove-product"><span class="mdi mdi-delete-circle mdi-18px"></span></button></td>
      </tr>
  `;

        orderItemsTableBody.innerHTML += row;
        productList.innerHTML = "";
        productSearchInput.value = "";

        updateEvents();
        updateGrandTotal();
    }

    // Delegated handlers: attach ONCE, inside DOMContentLoaded
    orderItemsTableBody.addEventListener("click", function (e) {
        const inc = e.target.closest(".increment-qty");
        const dec = e.target.closest(".decrement-qty");
        const del = e.target.closest(".remove-product, .remove-item");

        if (inc || dec) {
            const row = (inc || dec).closest("tr");
            const input = row.querySelector(".qty-input");
            let value = parseInt(input.value) || 1;
            const min = parseInt(input.getAttribute("min")) || 1;

            value = inc ? value + 1 : Math.max(min, value - 1);
            input.value = value;
            updateSubtotal(row);
        }

        if (del) {
            del.closest("tr").remove();
            updateGrandTotal();
        }
    });

    orderItemsTableBody.addEventListener("input", function (e) {
        if (e.target.matches(".qty-input, .discount-input")) {
            updateSubtotal(e.target.closest("tr"));
        }
    });

    function updateSubtotal(row) {
        let qty = parseFloat(row.querySelector(".qty-input").value);
        let discount =
            parseFloat(row.querySelector(".discount-input").value) || 0;
        let netUnitCost = parseFloat(
            row.querySelector(".qty-input").dataset.cost,
        );

        // Calculate subtotal after discount
        let subtotal = netUnitCost * qty - discount;
        row.querySelector(".subtotal").innerText = subtotal.toFixed(2);

        // Update Grand Total
        updateGrandTotal();
    }

    // Grand total update function
    function updateGrandTotal() {
        let grandTotal = 0;

        // Calculate subtotal sum
        document.querySelectorAll(".subtotal").forEach(function (item) {
            grandTotal += parseFloat(item.textContent) || 0;
        });

        // Get discount and shipping values
        let discount =
            parseFloat(document.getElementById("inputDiscount").value) || 0;
        let shipping =
            parseFloat(document.getElementById("inputShipping").value) || 0;

        // Apply discount and add shipping cost
        grandTotal = grandTotal - discount + shipping;

        // Ensure grand total is not negative
        if (grandTotal < 0) {
            grandTotal = 0;
        }

        // Update Grand Total display
        document.getElementById("grandTotal").textContent =
            `TK ${grandTotal.toFixed(2)}`;

        document.querySelector("input[name='grand_total']").value =
            grandTotal.toFixed(2);
    }

    /// Start Modal

    // this is modal, instead to html
  let modal = document.createElement("div");
  modal.id = "customModal";
  modal.style.position = "fixed";
  modal.style.top = "0";
  modal.style.left = "0";
  modal.style.width = "100%";
  modal.style.height = "100%";
  modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
  modal.style.display = "none";
  modal.style.justifyContent = "center";
  modal.style.alignItems = "center";
  modal.style.zIndex = "1000";

  modal.innerHTML = `
      <div style="background: white; padding: 20px; border-radius: 5px; width: 400px;">
          <h3 id="modalTitle"></h3>
          <label>Product Price: <span class="text-danger">*</span></label>
          <input type="text" id="modalPrice" class="form-control" />

          <label>Discount Type: <span class="text-danger">*</span></label>
          <select id="modalDiscountType" class="form-control">
              <option value="">Select Discount</option>
              <option value="fixed">Fixed</option>
              <option value="percentage">Percentage</option>
          </select>

          <label>Discount: <span class="text-danger">*</span></label>
          <input type="text" id="modalDiscount" class="form-control" value="0.00" />

          <div style="margin-top: 15px; text-align: right;">
              <button id="closeModal" class="btn btn-secondary">Close</button>
              <button id="saveChanges" class="btn btn-primary">Save Changes</button>
          </div>
      </div>
  `;

  document.body.appendChild(modal);

  // Function to show modal
  function showModal(productName, productPrice) {
        document.getElementById("modalTitle").innerText = productName;
        document.getElementById("modalPrice").value = "TK " + productPrice;
        modal.style.display = "flex";
  }

  // Event listener to open modal
  document.addEventListener("click", function (event) {
        if (event.target.closest(".edit-discount-btn")) {
              let button = event.target.closest(".edit-discount-btn");
              let productId = button.getAttribute("data-id");
              let productName = button.getAttribute("data-name");
              let productPrice = button.getAttribute("data-cost");

              // Set modal values
              document.getElementById("modalTitle").innerText = productName;
              document.getElementById("modalPrice").value = "TK " + productPrice;
              modal.setAttribute("data-id", productId); // Store productId in modal

              // Show modal
              modal.style.display = "flex";
        }
  });

  // Close modal event
  document.getElementById("closeModal").addEventListener("click", function () {
        modal.style.display = "none";
  });


  // Save changes event
  document.getElementById("saveChanges").addEventListener("click", function () {
    let updatedPrice = parseFloat(document.getElementById("modalPrice").value.replace("TK ", ""));
    let discountValue = parseFloat(document.getElementById("modalDiscount").value) || 0;
    let discountType = document.getElementById("modalDiscountType").value;
    let productId = modal.getAttribute("data-id");
    let row = document.querySelector(`tr[data-id="${productId}"]`);

    if (row) {
          let priceCell = row.querySelector("td:nth-child(2)");
          let qtyInput = row.querySelector(".qty-input");
          let discountInput = row.querySelector(".discount-input");
          let subtotalCell = row.querySelector(".subtotal");

          // Update price in table
          priceCell.innerText = updatedPrice.toFixed(2);
          qtyInput.setAttribute("data-cost", updatedPrice);

          // Set discount value
          discountInput.value = discountValue.toFixed(2);

          // Apply discount calculation
          let qty = parseFloat(qtyInput.value);
          let discountAmount = discountType === "percentage" ? (updatedPrice * qty * (discountValue / 100)) : discountValue;
          let subtotal = (updatedPrice * qty) - discountAmount;

          subtotalCell.innerText = subtotal.toFixed(2);

          modal.style.display = "none"; // Close modal
          updateGrandTotal();
    }
});

  // Event listeners for discount and shipping input change
  document.getElementById("inputDiscount").addEventListener("input", updateGrandTotal);
  document.getElementById("inputShipping").addEventListener("input", updateGrandTotal);

  
document.getElementById("inputDiscount").addEventListener("input", function () {
    document.getElementById("displayDiscount").textContent = this.value || 0;
});
document.getElementById("inputShipping").addEventListener("input", function () {
    document.getElementById("shippingDisplay").textContent = this.value || 0;
});



    

});

/*
Line-by-Line Explanation

This JavaScript powers the product search box on your Create Purchase page. In plain terms: when the user types in the search box, it asks the server for matching products and shows them as a clickable list.

Part 1: Setup
js
document.addEventListener("DOMContentLoaded", function() {

Wait until the whole HTML page has loaded before running the code. Otherwise, the script might look for elements that don’t exist yet.

js
let productSearchInput = document.getElementById('product_search');

Finds the search input box (id="product_search") and stores it in a variable.

js
let warehouseDropdown = document.getElementById('warehouse_id');

Finds the Warehouse <select> dropdown.

js
let product_list = document.getElementById('product_list');

Finds the empty <div> under the search box where the search results will be shown.

js
let warehouseError = document.getElementById('warehouse_error')

Finds the red error text (“Please select the first warehouse”), which is hidden by default.

js
let orderItemsTableBody = document.querySelector('tbody')

Finds the first <tbody> on the page. Meant for the order items table, where selected products will be added. Note: it isn’t used anywhere yet in this code.

Part 2: Typing in the search box
js
productSearchInput.addEventListener("keyup", function() {

Every time the user releases a key inside the search box, run the function below.

js
let query = this.value;

Gets the text the user has typed so far. (this = the search input.)

js
let warehouse_id = warehouseDropdown.value;

Gets the currently selected warehouse ID (empty if none selected).

js
if(!warehouse_id) {
    warehouseError.classList.remove('d-none');
    product_list.innerHTML = "";
    return;
} else {
    warehouseError.classList.add('d-none');
}
If no warehouse is selected: show the red error message, clear the results list, and return (stop here, don’t search).
Otherwise: hide the error message.

d-none is a Bootstrap class that hides an element, so removing it shows the element and adding it hides it.

js
if(query.length > 1) {
    fetchProducts(query, warehouse_id);
} else {
    product_list.innerHTML = "";
}
If the user typed 2 or more characters, call fetchProducts() to search.
Otherwise, clear the results list.
js
});

Ends the keyup function.

Part 3: Fetching products from the server
js
function fetchProducts(query, warehouse_id) {

Defines a function that takes the search text and the warehouse ID.

js
fetch(productSearchUrl + "?query=" + query + "&warehouse_id=" + warehouse_id)

Sends a request to your server. productSearchUrl comes from the <script> in your Blade file (route('purchase.product.search')). The final URL looks like:

/purchase/product/search?query=phone&warehouse_id=2
js
.then(response => response.json())

When the server replies, convert the response into a JavaScript array/object (from JSON).

js
.then(data => {

Now data holds the list of matching products. Run the code below with it.

js
product_list.innerHTML = "";

Clear any old results first.

js
if(data.length > 0) {

If at least one product was found...

js
data.forEach(product => {

...loop through each product one by one.

js
let item = `<a href="#" class="list-group-item list-group-item-action product-item"
    data-id="${product.id}" data-code="${product.code}"
    data-name="${product.name}" data-cost="${product.price}"
    data-stock="${product.product_qty}">
    <i class="mdi mdi-text-search"></i>
    ${product.code} - ${product.name} </a>`;

Builds an HTML link for each product using a template string (backticks). It:

Styles it as a Bootstrap list item.
Stores product details in data- attributes (id, code, name, cost, stock) so you can read them later when the user clicks it.
Shows text like P1001 - Samsung Phone.
js
product_list.innerHTML += item;

Adds that link to the results list.

js
console.log(item);

Prints the HTML in the browser console for debugging. You can remove this later.

js
});

Ends the loop.

Part 4: Click handling
js
document.querySelectorAll(".product-item").forEach(item => {

Finds all the product links that were just created and loops through them.

js
item.addEventListener("click", function(e){
    e.preventDefault();
});

When a product is clicked, preventDefault() stops the link from jumping to # at the top of the page. Right now, this is all it does. It doesn’t add the product to the table yet.

Part 5: No results
js
} else {
    product_list.innerHTML = '<p class="text-muted">No Product Found!</p>'
}

If the server returned an empty list, show “No Product Found!” in grey text.

The remaining }) lines just close the open functions and blocks.
*/

/*
Line-by-Line Explanation of addProductToTable

In plain terms: when the user clicks a product in the suggestion list, this function reads that product’s details, builds a new table row (as HTML text), and adds it to the Order Items table.

Part 1: Reading the product’s data
js
function addProductToTable(productElement) {

Defines a function. productElement is the suggestion link the user clicked (the <a class="product-item">).

js
let productId = productElement.getAttribute("data-id");
let productCode = productElement.getAttribute("data-code");
let productName = productElement.getAttribute("data-name");

Reads the product’s id, code and name from the data- attributes on the clicked link. These came from the search results. getAttribute always returns text.

js
let netUnitCost = parseFloat(productElement.getAttribute("data-cost"));

Reads the cost and converts it from text to a decimal number with parseFloat. This is needed so .toFixed(2) works later.

js
let stock = parseInt(productElement.getAttribute("data-stock"));

Reads the stock and converts it to a whole number with parseInt.

Part 2: Duplicate check
js
if (document.querySelector(`tr[data-id="${productId}"]`)) {
    alert("Product already added.");
    return;
}

Searches the page for a table row (<tr>) whose data-id matches this product. If one exists, it shows a popup “Product already added.” and return stops the function, so the product isn’t added twice.

Part 3: Building the row
js
let row = `
<tr data-id="${productId}">

Starts an HTML string using backticks (a template string). ${...} inserts a variable’s value. The data-id on the row is what the duplicate check above looks for.

Column 1: Product name
js
<td>
    ${productCode} - ${productName}

Shows text like P1001 - Samsung Phone.

js
<button type="button" class="btn btn-primary btn-sm edit-discount-btn"
    data-id="${productId}" data-name="${productName}" data-cost="${netUnitCost}"
    data-bs-toggle="modal">
    <span class="mdi mdi-book-edit "></span>
</button>

An “edit” button with an icon. It stores the product info in data- attributes and has data-bs-toggle="modal", meant to open a popup. But there’s no data-bs-target, so it won’t open anything yet.

js
<input type="hidden" name="products[${productId}][id]" value="${productId}">
<input type="hidden" name="products[${productId}][name]" value="${productName}">
<input type="hidden" name="products[${productId}][code]" value="${productCode}">

Three invisible inputs that send the id, name and code to the server when the form is submitted. The products[5][id] naming makes Laravel receive them as an array grouped per product.

Column 2: Net unit cost
js
<td>${netUnitCost.toFixed(2)}
    <input type="hidden" name="products[${productId}][cost]" value="${netUnitCost}">
</td>

Shows the cost with 2 decimals (e.g. 150.00) and sends the raw cost as a hidden input.

Column 3: Stock
js
<td style="color:#ffc121">${stock}</td>

Shows the current stock in yellow/orange. For display only.

Column 4: Quantity
js
<div class="input-group">
    <button class="btn btn-outline-secondary decrement-qty" type="button">−</button>

The “minus” button. type="button" stops it from submitting the form. The class decrement-qty lets other code find it.

js
<input type="text" class="form-control text-center qty-input"
    name="products[${productId}][quantity]" value="1" min="1" max="${stock}"
    data-cost="${netUnitCost}" style="width: 30px;">

The quantity box:

Starts at 1.
name=... sends it to the server.
data-cost keeps the cost handy for calculating the subtotal.
min/max do nothing on type="text". Use type="number" for them to work.
width: 30px is very narrow, so 2-3 digit quantities will be cut off.
js
<button class="btn btn-outline-secondary increment-qty" type="button">+</button>

The “plus” button.

Column 5: Discount
js
<input type="number" class="form-control discount-input"
    name="products[${productId}][discount]" value="0" min="0" style="width:100px">

A number box for this product’s discount, starting at 0 and not allowing negatives.

Column 6: Subtotal
js
<td class="subtotal">${netUnitCost.toFixed(2)}</td>

Shows the row total. It starts as cost × 1, and should be updated by your calculation code when qty or discount changes.

Column 7: Remove
js
<td><button class="btn btn-danger btn-sm remove-product">...</button></td>

A red delete button. It has no type="button", so inside a form it acts as a submit button and would submit the whole form when clicked. Add type="button".

js
</tr>
`;

Ends the row and the template string.

Part 4: Putting the row on the page
js
orderItemsTableBody.innerHTML += row;

Appends the new row to the table body. Problem: += rebuilds the entire table body, which erases values the user already typed in other rows (qty, discount). Use this instead:

js
orderItemsTableBody.insertAdjacentHTML("beforeend", row);
js
productList.innerHTML = "";

Clears the suggestion list. Bug: your variable is named product_list (with an underscore), so productList is undefined and the function crashes here with a ReferenceError.

js
productSearchInput.value = "";

Clears the search box so the user can search for the next product.
*/

/*
Part 1: updateEvents() (attaching events to rows)
js
function updateEvents() {

Starts a function that attaches listeners to the qty, discount, +, − and remove controls.

js
document.querySelectorAll(".qty-input, .discount-input").forEach((input) => {
    input.addEventListener("input", function () {

Finds every qty box and discount box on the page, loops through them, and runs the code below whenever the user types in one.

js
let row = this.closest("tr");

this is the box being typed in. closest("tr") finds the table row it belongs to.

js
let qty = parseInt(row.querySelector(".qty-input").value) || 1;

Reads the quantity from that row as a whole number. If it’s empty or invalid, it uses 1.

js
let unitCost = parseFloat(row.querySelector(".qty-input").getAttribute("data-cost")) || 0;

Reads the unit cost saved in the qty box’s data-cost attribute. If it’s invalid, it uses 0.

js
let discount = parseFloat(row.querySelector(".discount-input").value) || 0;

Reads the row’s discount amount, or 0 if empty.

js
let subtotal = unitCost * qty - discount;
row.querySelector(".subtotal").textContent = subtotal.toFixed(2);

Calculates cost × qty − discount and shows it in the Subtotal cell with 2 decimals.

js
updateGrandTotal();

Recalculates the final total.

Increment (+) button

js
document.querySelectorAll(".increment-qty").forEach((button) => {
    button.addEventListener("click", function () {

Finds every + button and runs code when it’s clicked.

js
let input = this.closest(".input-group").querySelector(".qty-input");

From the clicked button, goes up to its input-group wrapper and finds the qty box next to it.

js
let max = parseInt(input.getAttribute("max"));
let value = parseInt(input.value);
if (value < max) {
    input.value = value + 1;
    updateSubtotal(this.closest("tr"));
}

Reads the max (the stock) and the current qty. If qty is below the max, it adds 1 and recalculates that row.

Decrement (−) button works the same way, but it checks against min and subtracts 1.

Remove button

js
document.querySelectorAll(".remove-product").forEach((button) => {
    button.addEventListener("click", function () {
        this.closest("tr").remove();
        updateGrandTotal();
    });
});

When the red button is clicked, it deletes that row and recalculates the total.

Part 2: updateSubtotal(row)
js
let qty = parseFloat(row.querySelector(".qty-input").value);
let discount = parseFloat(row.querySelector(".discount-input").value) || 0;
let netUnitCost = parseFloat(row.querySelector(".qty-input").dataset.cost);

Reads qty, discount and unit cost from the row. dataset.cost is a shortcut for data-cost.

js
let subtotal = netUnitCost * qty - discount;
row.querySelector(".subtotal").innerText = subtotal.toFixed(2);
updateGrandTotal();

Calculates the row subtotal, shows it, then updates the grand total.

Part 3: updateGrandTotal()
js
let grandTotal = 0;
document.querySelectorAll(".subtotal").forEach(function (item) {
    grandTotal += parseFloat(item.textContent) || 0;
});

Starts at 0 and adds up every row’s subtotal.

js
let discount = parseFloat(document.getElementById("inputDiscount").value) || 0;
let shipping = parseFloat(document.getElementById("inputShipping").value) || 0;

Reads the order-level Discount and Shipping boxes.

js
grandTotal = grandTotal - discount + shipping;
if (grandTotal < 0) { grandTotal = 0; }

Subtracts discount, adds shipping, and never lets the total go below 0.

js
document.getElementById("grandTotal").textContent = `AED ${grandTotal.toFixed(2)}`;
document.querySelector("input[name='grand_total']").value = grandTotal.toFixed(2);

Shows the total on screen and also stores it in the hidden input so it gets submitted with the form.

Part 4: The modal (popup)
js
let modal = document.createElement("div");
modal.id = "customModal";

Creates a new empty <div> in memory and gives it an id.

js
modal.style.position = "fixed";
modal.style.top = "0"; modal.style.left = "0";
modal.style.width = "100%"; modal.style.height = "100%";

Makes it cover the whole screen, staying in place even when scrolling.

js
modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)";

Gives it a semi-transparent black background (the dimmed overlay).

js
modal.style.display = "none";
modal.style.justifyContent = "center";
modal.style.alignItems = "center";
modal.style.zIndex = "1000";

Hides it at first. When shown as flex, its content is centered. zIndex puts it above everything else.

js
modal.innerHTML = `...`;

Fills it with the white popup box: a title, a price input, a discount type dropdown, a discount input, and Close and Save buttons.

js
document.body.appendChild(modal);

Adds the hidden modal to the page.

js
function showModal(productName, productPrice) { ... }

A helper that sets the title and price and shows the modal. It’s never used, because the click listener below does the same work itself. You can delete it.

Opening the modal

js
document.addEventListener("click", function (event) {
    if (event.target.closest(".edit-discount-btn")) {

Listens for clicks anywhere on the page. If the click was on (or inside) an edit button, continue. This works for rows added later.

js
let button = event.target.closest(".edit-discount-btn");
let productId = button.getAttribute("data-id");
let productName = button.getAttribute("data-name");
let productPrice = button.getAttribute("data-cost");

Gets the button and reads the product’s id, name and cost from it.

js
document.getElementById("modalTitle").innerText = productName;
document.getElementById("modalPrice").value = "AED " + productPrice;
modal.setAttribute("data-id", productId);
modal.style.display = "flex";

Puts the name and price into the popup, remembers which product it’s editing, and shows it.

Close button

js
document.getElementById("closeModal").addEventListener("click", function () {
    modal.style.display = "none";
});

Hides the modal.

Save Changes button

js
let updatedPrice = parseFloat(document.getElementById("modalPrice").value.replace("AED ", ""));

Reads the price box, removes the “AED “ text, and converts it to a number.

js
let discountValue = parseFloat(document.getElementById("modalDiscount").value) || 0;
let discountType = document.getElementById("modalDiscountType").value;
let productId = modal.getAttribute("data-id");
let row = document.querySelector(`tr[data-id="${productId}"]`);

Reads the discount and its type, then finds the table row for the product being edited.

js
if (row) {
    let priceCell = row.querySelector("td:nth-child(2)");
    let qtyInput = row.querySelector(".qty-input");
    let discountInput = row.querySelector(".discount-input");
    let subtotalCell = row.querySelector(".subtotal");

If the row exists, grabs its price cell (2nd column), qty box, discount box and subtotal cell.

js
priceCell.innerText = updatedPrice.toFixed(2);
qtyInput.setAttribute("data-cost", updatedPrice);
discountInput.value = discountValue.toFixed(2);

Shows the new price, saves it for later calculations, and puts the discount into the row’s discount box.

js
let qty = parseFloat(qtyInput.value);
let discountAmount = discountType === "percentage" ? updatedPrice * qty * (discountValue / 100)    : discountValue;
let subtotal = updatedPrice * qty - discountAmount;

For a percentage discount it works out the amount as a % of price × qty. For fixed it uses the number as is. Then it calculates the subtotal.

js
subtotalCell.innerText = subtotal.toFixed(2);
modal.style.display = "none";
updateGrandTotal();

Shows the subtotal, closes the modal and updates the grand total.

Part 5: Bottom listeners
js
document.getElementById("inputDiscount").addEventListener("input", updateGrandTotal);
document.getElementById("inputShipping").addEventListener("input", updateGrandTotal);

When the order-level Discount or Shipping changes, recalculate the grand total.

js
document.getElementById("inputDiscount").addEventListener("input", function () {
    document.getElementById("displayDiscount").textContent = this.value || 0;
});

Copies the typed discount into the summary table. The Shipping block does the same for shippingDisplay.
*/
