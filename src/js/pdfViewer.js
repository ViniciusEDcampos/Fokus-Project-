// Array para guardar os PDFs
let historico = JSON.parse(localStorage.getItem('historicoPDFs') || '[]');

// Função para renderizar o histórico
function renderizarHistorico() {
  const lista = document.getElementById('historicoPDFs');
  lista.innerHTML = '';
  historico.forEach((item, index) => {
    const li = document.createElement('lii');
    li.className = 'list-group-item d-flex justify-content-between align-items-center mb-3';
    li.innerHTML = `
      <i class="fi fi-rs-document" id="iconePage"></i>
      <span id="a">${item.nome} (${item.tamanho} MB)</span>
      <div>
        <button class="btn btn-sm btn-outline-primary me-1" onclick="visualizarPDF(${index})"><i class="fi fi-rs-eye"></i></button>
        <button class="btn btn-sm btn-outline-danger" onclick="removerPDF(${index})">&times;</button>
      </div>
    `;
    lista.appendChild(li);
  });
}

// Função para visualizar PDF
function visualizarPDF(index) {
  const viewer = document.getElementById('pdfViewer');
  viewer.src = historico[index].url;
  viewer.style.display = 'block';
}

// Função para remover PDF do histórico
function removerPDF(index) {
  historico.splice(index, 1);
  localStorage.setItem('historicoPDFs', JSON.stringify(historico));
  renderizarHistorico();
}

// Evento de upload
document.getElementById('pdfUpload').addEventListener('change', function () {
  const file = this.files[0];
  if (file && file.type === 'application/pdf') {
    const url = URL.createObjectURL(file);
    const viewer = document.getElementById('pdfViewer');
    viewer.src = url;
    viewer.style.display = "block";

    // Adicionar ao histórico
    historico.unshift({ nome: file.name, tamanho: (file.size / (1024*1024)).toFixed(2), url: url });
    if(historico.length > 5) historico.pop(); // manter apenas 5 últimos
    localStorage.setItem('historicoPDFs', JSON.stringify(historico));
    renderizarHistorico();
  }
});

// Renderizar histórico inicial
renderizarHistorico();