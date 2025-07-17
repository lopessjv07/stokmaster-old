const form = document.getElementById("product-form");
const nameInput = document.getElementById("name");
const priceInput = document.getElementById("price");
const descInput = document.getElementById("description");
const imageInput = document.getElementById("image");
const editIndex = document.getElementById("editIndex");
const productList = document.getElementById("product-list");

let products = JSON.parse(localStorage.getItem("products")) || [];

function renderProducts() {
    productList.innerHTML = "";

    products.forEach((product, index) => {
        const card = document.createElement("div");
        card.className = "product-card";

        const img = document.createElement("img");
        img.src = product.image || "https://via.placeholder.com/100";
        img.alt = product.name;

        const info = document.createElement("div");
        info.className = "product-info";
        info.innerHTML = `
<h3>${product.name}</h3>
<p>R$ ${parseFloat(product.price).toFixed(2)}</p>
<p>${product.description}</p>
`;

        const actions = document.createElement("div");
        actions.className = "product-actions";

        const editBtn = document.createElement("button");
        editBtn.innerHTML = '<i class="fa-solid fa-pen"></i> Editar';
        editBtn.onclick = () => editProduct(index);

        const deleteBtn = document.createElement("button");
        deleteBtn.innerHTML = '<i class="fa-solid fa-trash"></i> Excluir';
        deleteBtn.onclick = () => deleteProduct(index);

        actions.appendChild(editBtn);
        actions.appendChild(deleteBtn);

        card.appendChild(img);
        card.appendChild(info);
        card.appendChild(actions);

        productList.appendChild(card);
    });
}

function editProduct(index) {
    const product = products[index];
    nameInput.value = product.name;
    priceInput.value = product.price;
    descInput.value = product.description;
    editIndex.value = index;
}

function deleteProduct(index) {
    if (confirm("Tem certeza que deseja excluir este produto?")) {
        products.splice(index, 1);
        localStorage.setItem("products", JSON.stringify(products));
        renderProducts();
    }
}

form.onsubmit = (e) => {
    e.preventDefault();

    const reader = new FileReader();
    const file = imageInput.files[0];

    reader.onloadend = () => {
        const product = {
            name: nameInput.value,
            price: priceInput.value,
            description: descInput.value,
            image: file ? reader.result : products[editIndex.value]?.image || null,
        };

        if (editIndex.value !== "") {
            products[editIndex.value] = product;
            editIndex.value = "";
        } else {
            products.push(product);
        }

        localStorage.setItem("products", JSON.stringify(products));
        form.reset();
        renderProducts();
    };

    if (file) {
        reader.readAsDataURL(file);
    } else {
        reader.onloadend();
    }
};

renderProducts();