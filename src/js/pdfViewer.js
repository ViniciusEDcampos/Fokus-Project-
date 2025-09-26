// ===== Estado =====
let historico = [];

// Lista de pdf's do usuario
function renderizarHistorico() {
  const lista = document.getElementById('historicoPDFs');
  lista.innerHTML = '';

  historico.forEach((item, index) => {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center mb-2';
    li.innerHTML = `
      <div class="d-flex align-items-center gap-2">
        <i class="fi fi-rs-document text-primary"></i>
        <span>${item.nome} (${item.tamanho} MB)</span>
      </div>
      <div>
        <button class="btn btn-sm btn-outline-primary me-1" onclick="visualizarPDF(${index})">
          <i class="fi fi-rs-eye"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="removerPDF(${item.id}, ${index})">&times;</button>
      </div>
    `;
    lista.appendChild(li);
  });
}

// ===== Backend helper =====
async function api(url, data) {
  const options = {
    method: data ? 'POST' : 'GET',
    credentials: 'include'
  };

  if (data) {
    options.body = data instanceof FormData ? data : new URLSearchParams(data);
  }

  const resp = await fetch(url, options);
  const text = await resp.text();

  let json;
  try {
    json = JSON.parse(text);
  } catch (e) {
    console.error("❌ Resposta inválida:", text);
    throw new Error("Erro no servidor");
  }

  if (!json.ok) throw new Error(json.msg || 'Erro');
  return json;
}

// "CRUD" para o pdf (listar, adicionar, apagar) 
async function carregarPDFs() {
  try {
    const json = await api('/src/api/pdf_api/pdf_listar.php');
    historico = json.items || [];
    renderizarHistorico();
  } catch (err) {
    console.error("Erro ao listar PDFs:", err);
  }
}

async function uploadPDF(file) {
  const fd = new FormData();
  fd.append('pdf', file);

  const json = await api('/src/api/pdf_api/pdf_upload.php', fd);
  // adiciona novo item no início do histórico
  if (json.arquivo) {
    historico.unshift(json.arquivo);
    renderizarHistorico();
    visualizarPDF(0); // já abre no iframe
  } else {
    //await espera uma resposta do servidor
    await carregarPDFs();
  }
}

async function excluirPDF(id) {
  await api('/src/api/pdf_api/pdf_excluir.php', { id });
  await carregarPDFs();
}

// funções usando o CRUD
function visualizarPDF(index) {
  const viewer = document.getElementById('pdfViewer');
  viewer.src = historico[index].url; // vem do php_view.php
  viewer.style.display = 'block';
}

function removerPDF(id, index) {
  excluirPDF(id)
    .then(() => {
      historico.splice(index, 1);
      renderizarHistorico();
      document.getElementById('pdfViewer').style.display = 'none';
    })
    .catch(err => alert(err.message));
}

// ===== Upload listener =====
document.getElementById('pdfUpload').addEventListener('change', function () {
  const file = this.files[0];
  if (file && file.type === 'application/pdf') {
    const fd = new FormData();
    fd.append('pdf', file); // testa a chave e garante que a chave bate com o PHP
    api('/src/api/pdf_api/pdf_upload.php', fd)
      .then(() => carregarPDFs()) // Função para recarregar a lista depois que foi feito um upload
      .catch(err => alert(err.message));
  }
});

// Função de tela cheia
function abrirTelaCheia() {
  const iframe = document.getElementById("pdfViewer");
  if (!iframe || iframe.style.display === "none") {
    alert("Nenhum PDF aberto para exibir em tela cheia.");
    return;
  }

  if (iframe.requestFullscreen) {
    iframe.requestFullscreen();
  } else if (iframe.mozRequestFullScreen) { // Firefox
    iframe.mozRequestFullScreen();
  } else if (iframe.webkitRequestFullscreen) { // Chrome, Safari, Opera
    iframe.webkitRequestFullscreen();
  } else if (iframe.msRequestFullscreen) { // IE/Edge
    iframe.msRequestFullscreen();
  }
}


// Carrega os PDF's ao Abrir o site
carregarPDFs();
