const form = document.getElementById("product-form");
const nameInput = document.getElementById("name");
const codeInput = document.getElementById("code-product");
const numberQuant = document.getElementById("quant")
const fornecedorInput = document.getElementById("fornecedor");
const priceInput = document.getElementById("price");
const descInput = document.getElementById("description");
const imageInput = document.getElementById("image");
const productList = document.getElementById("product-list");
const viewAllButton = document.getElementById("view-all-btn");

let products = [];
let currentImages = [];
let currentIndex = 0;
let editProductId = null;
let imagensAtuais = [];

async function fetchProducts() {
    try {
        const res = await fetch("listar_produtos.php");
        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`Erro ao buscar produtos: ${res.status} - ${errorText}`);
        }
        const data = await res.json();
        products = Array.isArray(data) ? data : [];
        if (products.length === 0) {
            console.log("Nenhum produto encontrado para o empresa_id atual.");
        }
        renderFilteredProducts(products);
    } catch (error) {
        console.error("Erro detalhado:", error);
        productList.innerHTML = `<p style='color: red;'>Erro ao carregar produtos: ${error.message}</p>`;
    }
}

form.onsubmit = async (e) => {
    e.preventDefault();

    if (!nameInput.value || !fornecedorInput.value || !codeInput.value || !priceInput.value || !document.getElementById("quant").value || !descInput.value) {
        alert("Preencha todos os campos obrigatórios.");
        return;
    }

    const formData = new FormData();
    formData.append("name", nameInput.value);
    formData.append("fornecedor", fornecedorInput.value);
    formData.append("codigo", codeInput.value);
    formData.append("preco", priceInput.value);
    formData.append("data_entrada", document.getElementById("date-insert").value);
    formData.append("quantidade", document.getElementById("quant").value);
    formData.append("descricao", descInput.value);

    

    if (editProductId) {
        formData.append("id", editProductId);
        console.log("Enviando edição para ID:", editProductId);
    }

    Array.from(imageInput.files).forEach(file => {
        formData.append("imagens[]", file);
    });

    try {
        const url = editProductId ? "editar_produto.php" : "salvar_produtos.php";
        const res = await fetch(url, {
            method: "POST",
            body: formData
        });
        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`Erro ao salvar/editar produto: ${res.status} - ${errorText}`);
        }
        const data = await res.json();
        alert(data.success || data.error || "Operação concluída");
        form.reset();
        editProductId = null;
        fetchProducts();
        fetchHistorico();
    } catch (error) {
        console.error("Erro detalhado:", error);
        alert("Erro ao salvar/editar o produto.");
    }
};

function applyFilters() {
    const code = document.getElementById("search-code").value.toLowerCase();
    const fornecedor = document.getElementById("filter-fornecedor").value.toLowerCase();
    const nome = document.getElementById("filter-nome").value.toLowerCase();

    const filtered = products.filter(product => {
        return (
            (code === "" || product.codigo.toLowerCase().includes(code)) &&
            (fornecedor === "" || product.fornecedor.toLowerCase().includes(fornecedor)) &&
            (nome === "" || product.nome.toLowerCase().includes(nome) || product.descricao.toLowerCase().includes(nome))
        );
    });

    renderFilteredProducts(filtered);
}

function clearFilters() {
    document.getElementById("search-code").value = "";
    document.getElementById("filter-fornecedor").value = "";
    document.getElementById("filter-nome").value = "";
    productList.style.display = "none"; 
}

function renderFilteredProducts(filteredList) {
    productList.innerHTML = "";

    if (filteredList.length === 0) {
        productList.style.display = "none"; 
        productList.innerHTML = "<strong><p style='color: red;'>Humm... Nenhum produto encontrado :(</p></strong>";
        return;
    }

    productList.style.display = "block"; 

    filteredList.forEach((product) => {
        const card = document.createElement("div");
        card.className = "product-card";

        const imgContainer = document.createElement("div");
        imgContainer.className = "image-thumbs";

        const imagens = product.imagens || [];
        imagens.forEach((imgSrc, imgIndex) => {
            const thumb = document.createElement("img");
            thumb.src = imgSrc;
            thumb.alt = product.nome;
            thumb.onclick = () => openImageViewer(imagens, imgIndex);
            imgContainer.appendChild(thumb);
        });

        const info = document.createElement("div");
        info.className = "product-info";
        info.innerHTML = `
            <h3>${product.nome}</h3>
            <p><strong>Fornecedor:</strong> ${product.fornecedor}</p>
            <p><strong>Preço:</strong> R$ ${parseFloat(product.preco).toFixed(2)}</p>
            <p><strong>Código:</strong> ${product.codigo}</p>
            <p><strong>Quantidade:</strong> 
                <span style="color: ${product.quantidade <= 5 ? 'red' : 'inherit'};">
                    ${product.quantidade} ${product.quantidade <= 5 ? 'restantes' : ''}
                </span>
            </p>
            <p>${product.descricao}</p>
        `;

        const actions = document.createElement("div");
        actions.className = "product-actions";

        const editBtn = document.createElement("button");
        editBtn.innerHTML = '<i class="fa-solid fa-pen"></i> Editar';
        editBtn.onclick = () => editProduct(product.id);

        const deleteBtn = document.createElement("button");
        deleteBtn.innerHTML = '<i class="fa-solid fa-trash"></i> Excluir';
        deleteBtn.onclick = () => deleteProduct(product.id);

        actions.appendChild(editBtn);
        actions.appendChild(deleteBtn);

        card.appendChild(imgContainer);
        card.appendChild(info);
        card.appendChild(actions);

        productList.appendChild(card);
    });
}


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
    if (e.target === this) closeViewer();
});

function alternarFormulario(tipo) {
    document.getElementById("form-produto").style.display = tipo === 'produto' ? 'block' : 'none';
    document.getElementById("form-saida").style.display = tipo === 'saida' ? 'block' : 'none';
}

function preencherPorCodigo() {
    const codigo = document.getElementById('saida-codigo').value.trim();
    const produto = products.find(p => p.codigo === codigo);

    if (produto) {
        document.getElementById('saida-nome').value = produto.nome;
        document.getElementById('saida-fornecedor').value = produto.fornecedor;
        document.getElementById('saida-preco').value = produto.preco;
        document.getElementById('saida-desc').value = produto.descricao;
    } else {
        document.getElementById('saida-nome').value = '';
        document.getElementById('saida-fornecedor').value = '';
        document.getElementById('saida-preco').value = '';
        document.getElementById('saida-desc').value = '';
    }
}

async function registrarSaida() {
    const codigo = document.getElementById('saida-codigo').value.trim();
    const comprador = document.getElementById('saida-comprador').value.trim();
    const data = document.getElementById('date').value;
    const quantidade = parseInt(document.getElementById('saida-quantidade').value.trim());

    console.log("Dados de saída:", { codigo, comprador, data, quantidade }); // Depuração

    if (!codigo || !comprador || isNaN(quantidade) || quantidade <= 0) {
        alert("Preencha todos os campos corretamente com valores válidos.");
        return;
    }

    try {
        const res = await fetch("registrar_saida.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ codigo, comprador, data, quantidade })
        });

        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`Erro ao registrar saída: ${res.status} - ${errorText}`);
        }
        const responseData = await res.json(); // Renomeado para evitar conflito
        alert(responseData.success || responseData.error || "Operação concluída");
        document.querySelector("#form-saida form").reset();
        preencherPorCodigo();
        fetchProducts();
        fetchHistorico();
    } catch (error) {
        console.error("Erro detalhado:", error);
        alert("Registrando Saida");
    }
}

async function editProduct(productId) {
    const product = products.find(p => p.id === productId);
    if (product) {
        nameInput.value = product.nome || '';
        codeInput.value = product.codigo || '';
        fornecedorInput.value = product.fornecedor || '';
        priceInput.value = product.preco || '';
        descInput.value = product.descricao || '';
        document.getElementById("quant").value = product.quantidade || '';
        document.getElementById("date-insert").value = product.data_registro ? product.data_registro.split(' ')[0] : '';
        editProductId = productId;
        console.log("Editando produto ID:", productId);
    } else {
        alert("Produto não encontrado para edição.");
    }
}

async function deleteProduct(productId) {
    if (confirm("Tem certeza que deseja excluir este produto?")) {
        try {
            const res = await fetch(`excluir_produto.php?id=${productId}`, {
                method: "GET"
            });
            if (!res.ok) throw new Error("Erro ao excluir produto");
            const data = await res.json();
            alert(data.success || data.error || "Produto excluído");
            fetchProducts();
            fetchHistorico();
        } catch (error) {
            console.error(error);
            alert("A excluir o produto...");
        }
    }
}

function viewAll() {
    renderFilteredProducts(products);
}

if (viewAllButton) {
    viewAllButton.addEventListener("click", viewAll);
}

fetchProducts();
fetchHistorico();