const form = document.getElementById("product-form");
const nameInput = document.getElementById("name");
const codeInput = document.getElementById("code-product");
const fornecedorInput = document.getElementById("fornecedor");
const priceInput = document.getElementById("price");
const descInput = document.getElementById("description");
const imageInput = document.getElementById("image");
const editIndex = document.getElementById("editIndex");
const productList = document.getElementById("product-list");

let products = JSON.parse(localStorage.getItem("products")) || [];

form.onsubmit = (e) => {
    e.preventDefault();

    const files = Array.from(imageInput.files).slice(0, 10);
    images = [];
    let loaded = 0;

    if (files.length === 0 && editIndex.value !== "") {
        images.push(...(products[editIndex.value]?.images || []));
        saveProduct();
    } else {
        files.forEach(file => {
            const reader = new FileReader();
            reader.onloadend = () => {
                images.push(reader.result);
                loaded++;
                if (loaded === files.length) saveProduct();
            };
            reader.readAsDataURL(file);
        });
    }
};

function saveProduct() {
    const product = {
        name: nameInput.value,
        code: codeInput.value,
        fornecedor: fornecedorInput.value,
        price: priceInput.value,
        description: descInput.value,
        images: images,
    };

    if (editIndex.value !== "") {
        products[editIndex.value] = product;
        editIndex.value = "";
    } else {
        products.push(product);
    }

    localStorage.setItem("products", JSON.stringify(products));
    form.reset();
    productList.innerHTML = "";
}

// EDITAR PRODUTO
function editProduct(index) {
    const product = products[index];
    nameInput.value = product.name;
    codeInput.value = product.code;
    fornecedorInput.value = product.fornecedor;
    priceInput.value = product.price;
    descInput.value = product.description;
    editIndex.value = index;
}

// EXCLUIR PRODUTO
function deleteProduct(index) {
    if (confirm("Tem certeza que deseja excluir este produto?")) {
        products.splice(index, 1);
        localStorage.setItem("products", JSON.stringify(products));
        productList.innerHTML = "";
    }
}

// FILTRO
function applyFilters() {
    const code = document.getElementById("search-code").value.toLowerCase();
    const fornecedor = document.getElementById("filter-fornecedor").value.toLowerCase();
    const nome = document.getElementById("filter-nome").value.toLowerCase();

    const filtered = products.filter(product => {
        return (
            (code === "" || product.code.toLowerCase().includes(code)) &&
            (fornecedor === "" || product.fornecedor.toLowerCase().includes(fornecedor)) &&
            (nome === "" || product.name.toLowerCase().includes(nome) || product.description.toLowerCase().includes(nome))
        );
    });

    renderFilteredProducts(filtered);
}

function renderFilteredProducts(filteredList) {
    productList.innerHTML = "";

    if (filteredList.length === 0) {
        productList.innerHTML = "<strong><p style='color: red;'> Humm...Nenhum produto encontrado:(</p></strong>";
        return;
    }

    filteredList.forEach((product, index) => {
        const card = document.createElement("div");
        card.className = "product-card";

        const imgContainer = document.createElement("div");
        imgContainer.className = "image-thumbs";

        (product.images || []).forEach((imgSrc, imgIndex) => {
            const thumb = document.createElement("img");
            thumb.src = imgSrc;
            thumb.alt = product.name;
            thumb.onclick = () => openImageViewer(product.images, imgIndex);
            imgContainer.appendChild(thumb);
        });

        const info = document.createElement("div");
        info.className = "product-info";
        info.innerHTML = `
            <h3>${product.name}</h3>
            <p><strong>Fornecedor:</strong> ${product.fornecedor}</p>
            <p><strong>Preço:</strong> R$ ${parseFloat(product.price).toFixed(2)}</p>
            <p><strong>Código:</strong> ${product.code}</p>
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

        card.appendChild(imgContainer);
        card.appendChild(info);
        card.appendChild(actions);

        productList.appendChild(card);
    });
}

function clearFilters() {
    document.getElementById("search-code").value = "";
    document.getElementById("filter-fornecedor").value = "";
    document.getElementById("filter-nome").value = "";
    productList.innerHTML = "";
}

//
// VISUALIZAÇÃO AMPLIADA DE IMAGEM
//
let currentImages = [];
let currentIndex = 0;

function openImageViewer(images, index) {
    currentImages = images;
    currentIndex = index;
    document.getElementById("viewer-img").src = images[index];
    document.getElementById("image-viewer").classList.remove("hidden");
}

function closeViewer() {
    document.getElementById("image-viewer").classList.add("hidden");
}

function prevImage() {
    if (currentIndex > 0) {
        currentIndex--;
        document.getElementById("viewer-img").src = currentImages[currentIndex];
    }
}

function nextImage() {
    if (currentIndex < currentImages.length - 1) {
        currentIndex++;
        document.getElementById("viewer-img").src = currentImages[currentIndex];
    }
}

document.getElementById("image-viewer").addEventListener("click", function (e) {
    if (e.target === this) {
        closeViewer();
    }
});
