document.addEventListener('DOMContentLoaded', () => {
  let tasks = JSON.parse(localStorage.getItem('fokusTasks') || '[]');
  let filter = 'all';
  let currentPriority = 'all';
 
  // Seletores principais já existentes
  const $taskInput = document.getElementById('taskInput');
  const $taskPriority = document.getElementById('taskPriority');
  const $taskList = document.getElementById('taskList');
  const $addBtn = document.getElementById('addBtn');
  const $filters = document.querySelectorAll('.filter');
  const $priorityFilters = document.querySelectorAll('.priority-filter');
  const $bar = document.getElementById('bar');
  const $counter = document.getElementById('counter');
 
  // Novos campos que já existem no seu HTML
  const $taskDate = document.getElementById('taskDate');
  const $taskTime = document.getElementById('taskTime');
  const $taskNote = document.getElementById('taskNote');
  const $taskMateria = document.getElementById('taskMateria');
 
  // Filtros avançados já existentes no HTML
  const $filterMateria = document.getElementById('filterMateria');
  const $filterData = document.getElementById('filterData');
  const $filterTempo = document.getElementById('filterTempo');
 
  // Estado de filtros avançados
  let adv = { materia: '', data: '', tempoMax: null };
 
  // Salvar no localstorage 
  function save() {
    localStorage.setItem('fokusTasks', JSON.stringify(tasks));
  }
 
  // Mensagem Para o usuario
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
 
  // ====== Render ======
  function render() {
    $taskList.innerHTML = '';
 
    const visible = tasks.filter(t => {
      // filtro status
      const statusOk =
        filter === 'all' ||
        (filter === 'active' && !t.completed) ||
        (filter === 'completed' && t.completed);
 
      // filtro prioridade
      const priorityOk = currentPriority === 'all' || t.priority === currentPriority;
 
      // filtros avançados
      const materiaOk =
        !adv.materia ||
        (t.materia && t.materia.toLowerCase().includes(adv.materia.toLowerCase()));
 
      const dataOk = !adv.data || t.date === adv.data;
      const tempoOk = adv.tempoMax == null || (typeof t.time === 'number' && t.time <= adv.tempoMax);
 
      return statusOk && priorityOk && materiaOk && dataOk && tempoOk;
    });
 
    visible.forEach(t => {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.dataset.id = t.id; // para buscar com segurança depois
      if (t.completed) li.classList.add('completed');
 
      const noteText = t.note ? ` — ${t.note}` : '';
      const metaBadges = [
        t.materia ? `<span class="badge text-bg-secondary ms-2">${t.materia}</span>` : '',
        t.date ? `<span class="badge text-bg-info ms-2">📅 ${t.date}</span>` : '',
        (t.time || t.time === 0) ? `<span class="badge text-bg-dark ms-2">⏱ ${t.time} min</span>` : ''
      ].join('');
 
      li.innerHTML = `
        <div class="form-check d-flex flex-column flex-md-row align-items-md-center gap-2">
          <div class="d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox" ${t.completed ? 'checked' : ''} onchange="toggleComplete(${t.id})">
            <label class="form-check-label ${t.completed ? 'text-decoration-line-through text-muted' : ''}">
              [${t.priority}] ${t.text}${noteText} ${metaBadges}
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
 
  //  Criar task 
  function addTask(e) {
    e?.preventDefault(); // evita submit do form
    const text = ($taskInput?.value || '').trim();
    const priority = $taskPriority?.value || 'Baixa';
    const date = $taskDate?.value || '';
    const time = $taskTime?.value ? Number($taskTime.value) : null;
    const note = ($taskNote?.value || '').trim();
    const materia = ($taskMateria?.value || '').trim();
 
    if (!text) return;
 
    const task = {
      id: Date.now(),
      text,
      completed: false,
      priority,
      note,         // agora salva observação
      date,         // salva data (YYYY-MM-DD)
      time,         // salva tempo (number ou null)
      materia       // salva matéria
    };
 
    tasks.unshift(task);
 
    // limpa campos existentes sem mexer no HTML
    if ($taskInput) $taskInput.value = '';
    if ($taskPriority) $taskPriority.value = 'Baixa';
    if ($taskDate) $taskDate.value = '';
    if ($taskTime) $taskTime.value = '';
    if ($taskNote) $taskNote.value = '';
    if ($taskMateria) $taskMateria.value = '';
 
    save();
    render();
    showToast('Tarefa adicionada ✅', 'green');
  }
 
  // Status da Tarefa
  window.toggleComplete = function(id) {
    const t = tasks.find(t => t.id === id);
    if (t) {
      t.completed = !t.completed;
      save();
      render();
      showToast(t.completed ? 'Tarefa concluída ✅' : 'Tarefa marcada como ativa', t.completed ? 'green' : 'orange');
    }
  };
 
  // Remover tarefas
  window.deleteTask = function(id) {
    tasks = tasks.filter(t => t.id !== id);
    save();
    render();
    showToast('Tarefa removida 🗑️', 'red');
  };
 
  // Editar tarefa
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
 
    const inputText = document.createElement('input');
    inputText.type = 'text';
    inputText.value = t.text;
    inputText.className = 'form-control form-control-sm edit-input d-inline';
    inputText.style.minWidth = '140px';
 
    const selectPriority = document.createElement('select');
    selectPriority.className = 'form-select form-select-sm edit-input d-inline ms-2';
    ['Baixa','Média','Alta'].forEach(p => {
      const option = document.createElement('option');
      option.value = p;
      option.textContent = p;
      if (p === t.priority) option.selected = true;
      selectPriority.appendChild(option);
    });
 
    const inputNote = document.createElement('input');
    inputNote.type = 'text';
    inputNote.value = t.note || '';
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
    inputDate.value = t.date || '';
    inputDate.className = 'form-control form-control-sm edit-input d-inline ms-2';
 
    const inputTime = document.createElement('input');
    inputTime.type = 'number';
    inputTime.min = '0';
    inputTime.value = (t.time ?? '') === null ? '' : t.time ?? '';
    inputTime.placeholder = 'Tempo (min)';
    inputTime.className = 'form-control form-control-sm edit-input d-inline ms-2';
    inputTime.style.maxWidth = '120px';
 
    const saveBtn = document.createElement('button');
    saveBtn.textContent = '💾';
    saveBtn.className = 'btn btn-sm btn-primary edit-input ms-2';
 
    container.appendChild(inputText);
    container.appendChild(selectPriority);
    container.appendChild(inputNote);
    container.appendChild(inputMateria);
    container.appendChild(inputDate);
    container.appendChild(inputTime);
    container.appendChild(saveBtn);
 
    inputText.focus();
 
    const finalizeEdit = () => {
      const newText = inputText.value.trim();
      if (!newText) return;
 
      t.text = newText;
      t.priority = selectPriority.value;
      t.note = inputNote.value.trim();
      t.materia = inputMateria.value.trim();
      t.date = inputDate.value || '';
      t.time = inputTime.value === '' ? null : Number(inputTime.value);
 
      [inputText, selectPriority, inputNote, inputMateria, inputDate, inputTime, saveBtn]
        .forEach(el => container.removeChild(el));
 
      label.style.display = '';
      divActions.style.display = '';
 
      const metaBadges = [
        t.materia ? `<span class="badge text-bg-secondary ms-2">${t.materia}</span>` : '',
        t.date ? `<span class="badge text-bg-info ms-2">📅 ${t.date}</span>` : '',
        (t.time || t.time === 0) ? `<span class="badge text-bg-dark ms-2">⏱ ${t.time} min</span>` : ''
      ].join('');
 
      label.innerHTML = `[${t.priority}] ${t.text}${t.note ? ` — ${t.note}` : ''} ${metaBadges}`;
 
      save();
      showToast('Tarefa editada ✏️', 'orange');
    };
 
    saveBtn.addEventListener('click', finalizeEdit);
    [inputText, inputNote, inputMateria, inputDate, inputTime, selectPriority].forEach(el => {
      el.addEventListener('keypress', e => { if (e.key === 'Enter') finalizeEdit(); });
    });
  };
 
  // Progresso e contadores
  function updateProgress() {
    const total = tasks.length;
    const done = tasks.filter(t => t.completed).length;
    const altaPrioridade = tasks.filter(t => t.priority === 'Alta').length;
    const pendentes = total - done;
 
    $bar.style.width = total ? (done / total * 100) + '%' : '0%';
    $counter.textContent = total === 1 ? '1 tarefa' : total + ' tarefas';
 
    document.getElementById('total-tarefas').textContent = total;
    document.getElementById('tarefas-concluidas').textContent = done;
    document.getElementById('tarefas-pendentes').textContent = pendentes;
    document.getElementById('tarefas-alta').textContent = altaPrioridade;
  }
 
  // ====== Listeners ======
  // Botão adicionar (evita submit do form)
  $addBtn.addEventListener('click', (e) => addTask(e));
 
  // Enter no input principal também adiciona
  $taskInput.addEventListener('keypress', e => { if (e.key === 'Enter') addTask(e); });
 
  // Filtros status
  $filters.forEach(btn => {
    btn.addEventListener('click', () => {
      $filters.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      filter = btn.dataset.filter;
      render();
    });
  });
 
  // Filtros prioridade
  $priorityFilters.forEach(btn => {
    btn.addEventListener('click', () => {
      $priorityFilters.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentPriority = btn.dataset.priority;
      render();
    });
  });
 
  // Filtros avançados (matéria, data, tempo)
  if ($filterMateria) {
    $filterMateria.addEventListener('input', () => {
      adv.materia = $filterMateria.value.trim();
      render();
    });
  }
  if ($filterData) {
    $filterData.addEventListener('change', () => {
      adv.data = $filterData.value || '';
      render();
    });
  }
  if ($filterTempo) {
    $filterTempo.addEventListener('input', () => {
      const v = $filterTempo.value;
      adv.tempoMax = v === '' ? null : Number(v);
      render();
    });
  }
 
  // First paint
  render();
});