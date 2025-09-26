document.addEventListener('DOMContentLoaded', () => {
  // ===== Helper HTTP =====
  async function api(url, data) {
    const resp = await fetch(url, {
      method: 'POST',
      headers: { 'Accept': 'application/json' },
      body: data instanceof FormData ? data : new URLSearchParams(data),
      credentials: 'include' // 🔑 envia cookie de sessão
    });

    const text = await resp.text(); // lê só uma vez
    let json;
    try {
      json = JSON.parse(text);
    } catch (e) {
      console.error("❌ Resposta não-JSON recebida:", text);
      throw new Error("Resposta inválida do servidor");
    }

    if (!json.ok) throw new Error(json.msg || 'Erro');
    return json;
  }

  // ===== Estado =====
  let tasks = [];
  let filter = 'all';             // 'all' | 'active' | 'completed'
  let currentPriority = 'all';    // 'all' | 'Baixa' | 'Média' | 'Alta'
  let adv = { materia: '', data: '', tempoMax: null };

  // ===== Seletores =====
  const $taskInput = document.getElementById('taskInput');
  const $taskPriority = document.getElementById('taskPriority');
  const $taskList = document.getElementById('taskList');
  const $addBtn = document.getElementById('addBtn');
  const $filters = document.querySelectorAll('.filter');
  const $priorityFilters = document.querySelectorAll('.priority-filter');
  const $bar = document.getElementById('bar');
  const $counter = document.getElementById('counter');

  const $taskDate = document.getElementById('taskDate');
  const $taskTime = document.getElementById('taskTime');
  const $taskNote = document.getElementById('taskNote');
  const $taskMateria = document.getElementById('taskMateria');

  const $filterMateria = document.getElementById('filterMateria');
  const $filterData = document.getElementById('filterData');
  const $filterTempo = document.getElementById('filterTempo');

  // ===== Funções de backend =====
  async function carregarDoServidor() {
    const params = {};
    if (filter === 'active') params.status = 'pendente';
    if (filter === 'completed') params.status = 'feito';
    if (currentPriority !== 'all') params.prioridade = currentPriority;
    if (adv.materia) params.materia = adv.materia;
    if (adv.data) params.data = adv.data;
    if (adv.tempoMax != null && !Number.isNaN(adv.tempoMax)) params.tempo_max = adv.tempoMax;

    const qs = new URLSearchParams(params).toString();
    const resp = await fetch('/src/api/listar.php' + (qs ? `?${qs}` : ''), {
      credentials: 'include',
      headers: { 'Accept': 'application/json' }
    });

    const text = await resp.text();
    let json;
    try {
      json = JSON.parse(text);
    } catch (e) {
      console.error("❌ Resposta não-JSON recebida:", text);
      throw new Error("Resposta inválida do servidor");
    }

    if (!json.ok) throw new Error(json.msg || 'Erro ao listar');
    tasks = json.items || [];
  }

  async function criarNoServidorViaForm() {
    const fd = new FormData();
    fd.append('titulo', ($taskInput?.value || '').trim());
    fd.append('prioridade', $taskPriority?.value || 'Baixa');
    fd.append('data_estudo', $taskDate?.value || '');
    fd.append('tempo_min', $taskTime?.value || '');
    fd.append('observacao', ($taskNote?.value || '').trim());
    fd.append('materia', ($taskMateria?.value || '').trim());

    await api('/src/api/criar.php', fd);
  }

  async function atualizarStatusServidor(id, status) {
    await api('/src/api/status.php', { id, status });
  }

  async function excluirServidor(id) {
    await api('/src/api/excluir.php', { id });
  }

  async function atualizarServidor(id, patch) {
    await api('/src/api/atualizar.php', { id, ...patch });
  }

  // ===== UI Utils =====
  function showToast(msgText, color) {
    const msg = document.createElement('div');
    msg.textContent = msgText;
    msg.style.position = 'fixed';
    msg.style.top = '15%';
    msg.style.right = '5%';
    msg.style.background = color;
    msg.style.color = '#fff';
    msg.style.padding = '10px 20px';
    msg.style.borderRadius = '10px';
    msg.style.zIndex = 1000;
    document.body.appendChild(msg);
    setTimeout(() => document.body.removeChild(msg), 1500);
  }

  function escapeHtml(s) {
    return (s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
  }

  // ===== Render =====
  function render() {
    $taskList.innerHTML = '';

    const visible = tasks.filter(t => {
      const statusOk =
        filter === 'all' ||
        (filter === 'active' && t.status !== 'feito') ||
        (filter === 'completed' && t.status === 'feito');

      const priorityOk = currentPriority === 'all' || t.prioridade === currentPriority;
      const materiaOk =
        !adv.materia ||
        (t.materia && t.materia.toLowerCase().includes(adv.materia.toLowerCase()));
      const dataOk = !adv.data || t.data_estudo === adv.data;
      const tempoOk = adv.tempoMax == null || (typeof t.tempo_min === 'number' && t.tempo_min <= adv.tempoMax);

      return statusOk && priorityOk && materiaOk && dataOk && tempoOk;
    });

    visible.forEach(t => {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.dataset.id = t.id;
      if (t.status === 'feito') li.classList.add('completed');

      const noteText = t.observacao ? ` — ${escapeHtml(t.observacao)}` : '';
      const metaBadges = [
  t.materia ? `<span class="badge text-bg-secondary ms-2">${escapeHtml(t.materia)}</span>` : '',
  t.data_estudo ? `<span class="badge text-bg-info ms-2">📅 ${t.data_estudo}</span>` : '',
  (t.tempo_min || t.tempo_min === 0) ? `<span class="badge text-bg-dark ms-2">⏱ ${t.tempo_min} min</span>` : '',
].join('');

      li.innerHTML = `
        <div class="form-check d-flex flex-column flex-md-row align-items-md-center gap-2">
          <div class="d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox" ${t.status === 'feito' ? 'checked' : ''} onchange="toggleComplete(${t.id})">
            <label class="form-check-label ${t.status === 'feito' ? 'text-decoration-line-through text-muted' : ''}">
              [${escapeHtml(t.prioridade || 'Baixa')}] ${escapeHtml(t.titulo)}${noteText} ${metaBadges}
            </label>
          </div>
        </div>
        <div>
          <button class="btn btn-sm btn-edit" onclick="editTaskInline(${t.id})">✏️</button>
          <button class="btn btn-sm btn-danger" onclick="deleteTask(${t.id})">🗑️</button>
        </div>
      `;
      $taskList.appendChild(li);
    });

    updateProgress();
  }

  function updateProgress() {
    const total = tasks.length;
    const done = tasks.filter(t => t.status === 'feito').length;
    const altaPrioridade = tasks.filter(t => t.prioridade === 'Alta').length;
    const pendentes = total - done;

    $bar.style.width = total ? (done / total * 100) + '%' : '0%';
    $counter.textContent = total === 1 ? '1 tarefa' : total + ' tarefas';

    document.getElementById('total-tarefas').textContent = total;
    document.getElementById('tarefas-concluidas').textContent = done;
    document.getElementById('tarefas-pendentes').textContent = pendentes;
    document.getElementById('tarefas-alta').textContent = altaPrioridade;
  }

  // ===== Ações =====
  window.toggleComplete = async function(id) {
    const t = tasks.find(x => x.id === id);
    if (!t) return;
    const novoStatus = t.status === 'feito' ? 'pendente' : 'feito';
    try {
      await atualizarStatusServidor(id, novoStatus);
      await carregarDoServidor();
      render();
      showToast(novoStatus === 'feito' ? 'Tarefa concluída ✅' : 'Tarefa marcada como ativa', novoStatus === 'feito' ? 'green' : 'orange');
    } catch (err) {
      showToast(err.message, 'red');
    }
  };

  window.deleteTask = async function(id) {
    try {
      await excluirServidor(id);
      await carregarDoServidor();
      render();
      showToast('Tarefa removida 🗑️', 'red');
    } catch (err) {
      showToast(err.message, 'red');
    }
  };

  window.editTaskInline = function(id) {
    const li = $taskList.querySelector(`li[data-id="${id}"]`);
    if (!li) return;
    if (li.querySelector('.edit-input')) return;

    const t = tasks.find(task => task.id === id);
    const label = li.querySelector('label');
    const divActions = li.querySelector('div:last-child');
    const container = li.querySelector('.form-check');

    label.style.display = 'none';
    divActions.style.display = 'none';

    // inputs inline
    const inputText = document.createElement('input');
    inputText.type = 'text';
    inputText.value = t.titulo || '';
    inputText.className = 'form-control form-control-sm edit-input d-inline';
    inputText.style.minWidth = '140px';

    const selectPriority = document.createElement('select');
    selectPriority.className = 'form-select form-select-sm edit-input d-inline ms-2';
    ['Baixa','Média','Alta'].forEach(p => {
      const option = document.createElement('option');
      option.value = p; option.textContent = p;
      if (p === (t.prioridade || 'Baixa')) option.selected = true;
      selectPriority.appendChild(option);
    });

    const inputNote = document.createElement('input');
    inputNote.type = 'text';
    inputNote.value = t.observacao || '';
    inputNote.placeholder = 'Observação (opcional)';
    inputNote.className = 'form-control form-control-sm edit-input d-inline ms-2';
    inputNote.style.minWidth = '140px';

    const inputMateria = document.createElement('input');
    inputMateria.type = 'text';
    inputMateria.value = t.materia || '';
    inputMateria.placeholder = 'Matéria';
    inputMateria.className = 'form-control form-control-sm edit-input d-inline ms-2';
    inputMateria.style.minWidth = '120px';

    const inputDate = document.createElement('input');
    inputDate.type = 'date';
    inputDate.value = t.data_estudo || '';
    inputDate.className = 'form-control form-control-sm edit-input d-inline ms-2';

    const inputTime = document.createElement('input');
    inputTime.type = 'number';
    inputTime.min = '0';
    inputTime.value = (t.tempo_min ?? '') === null ? '' : (t.tempo_min ?? '');
    inputTime.placeholder = 'Tempo (min)';
    inputTime.className = 'form-control form-control-sm edit-input d-inline ms-2';
    inputTime.style.maxWidth = '120px';

    const saveBtn = document.createElement('button');
    saveBtn.textContent = '💾';
    saveBtn.className = 'btn btn-sm btn-primary edit-input ms-2';

    [inputText, selectPriority, inputNote, inputMateria, inputDate, inputTime, saveBtn].forEach(el => container.appendChild(el));
    inputText.focus();

    const finalizeEdit = async () => {
      const patch = {
        titulo: inputText.value.trim(),
        prioridade: selectPriority.value,
        observacao: inputNote.value.trim(),
        materia: inputMateria.value.trim(),
        data_estudo: inputDate.value || '',
        tempo_min: inputTime.value === '' ? '' : Number(inputTime.value)
      };
      try {
        await atualizarServidor(id, patch);
        [inputText, selectPriority, inputNote, inputMateria, inputDate, inputTime, saveBtn]
          .forEach(el => container.removeChild(el));
        label.style.display = '';
        divActions.style.display = '';
        await carregarDoServidor();
        render();
        showToast('Tarefa editada ✏️', 'orange');
      } catch (err) {
        showToast(err.message, 'red');
      }
    };

    saveBtn.addEventListener('click', finalizeEdit);
    [inputText, inputNote, inputMateria, inputDate, inputTime, selectPriority].forEach(el => {
      el.addEventListener('keypress', e => { if (e.key === 'Enter') finalizeEdit(); });
    });
  };

  // ===== Criar task (botão/Enter) =====
  async function addTask(e) {
    e?.preventDefault();
    const text = ($taskInput?.value || '').trim();
    if (!text) return;
    try {
      await criarNoServidorViaForm();
      if ($taskInput) $taskInput.value = '';
      if ($taskPriority) $taskPriority.value = 'Baixa';
      if ($taskDate) $taskDate.value = '';
      if ($taskTime) $taskTime.value = '';
      if ($taskNote) $taskNote.value = '';
      if ($taskMateria) $taskMateria.value = '';
      await carregarDoServidor();
      render();
      showToast('Tarefa adicionada ✅', 'green');
    } catch (err) {
      showToast(err.message, 'red');
    }
  }

  // ===== Filtros =====
  $filters.forEach(btn => {
    btn.addEventListener('click', async () => {
      $filters.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const map = { all: 'all', active: 'active', completed: 'completed' };
      filter = map[btn.dataset.filter] ?? 'all';
      await carregarDoServidor();
      render();
    });
  });

  $priorityFilters.forEach(btn => {
    btn.addEventListener('click', async () => {
      $priorityFilters.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentPriority = btn.dataset.priority || 'all';
      await carregarDoServidor();
      render();
    });
  });

  if ($filterMateria) {
    $filterMateria.addEventListener('input', debounce(async () => {
      adv.materia = $filterMateria.value.trim();
      await carregarDoServidor();
      render();
    }, 300));
  }
  if ($filterData) {
    $filterData.addEventListener('change', async () => {
      adv.data = $filterData.value || '';
      await carregarDoServidor();
      render();
    });
  }
  if ($filterTempo) {
    $filterTempo.addEventListener('input', debounce(async () => {
      const v = $filterTempo.value;
      adv.tempoMax = v === '' ? null : Number(v);
      await carregarDoServidor();
      render();
    }, 300));
  }

  // ===== Botões / teclas =====
  $addBtn.addEventListener('click', addTask);
  $taskInput.addEventListener('keypress', e => { if (e.key === 'Enter') addTask(e); });

  // ===== Primeira carga =====
  (async () => {
    try {
      await carregarDoServidor();
      render();
    } catch (err) {
      showToast(err.message, 'red');
    }
  })();

  function debounce(fn, ms) {
    let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  }
});
