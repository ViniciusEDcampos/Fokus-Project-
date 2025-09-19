const btnTema = document.getElementById('btn-tema');
const navigation = document.getElementById('navigation');
const userBox = document.querySelector('.user');
const icon = btnTema.querySelector('i');

btnTema.onclick = () => {
  document.body.classList.toggle('dark-mode');
  navigation.classList.toggle('dark-mode');
  userBox.classList.toggle('dark-mode');

  // Troca o icone do header
  if (document.body.classList.contains('dark-mode')) {
    icon.classList.remove('fi-rr-moon');
    icon.classList.add('fi-rr-sun');
    icon.style.color = "yellow"; 
  } else {
    icon.classList.remove('fi-rr-sun');
    icon.classList.add('fi-rr-moon');
    icon.style.color = "white";
  }
};

  const showTaskFormBtn = document.getElementById('showTaskFormBtn');
  const taskForm = document.getElementById('taskForm');
  const addTaskBtn = document.getElementById('addBtn');
  const taskInput = document.getElementById('taskInput');
  const taskPriority = document.getElementById('taskPriority');
  const taskList = document.getElementById('taskList');

let tasks = JSON.parse(localStorage.getItem('fokusTasks') || '[]');

// Mostrar/esconder formulário ao clicar no botão
showTaskFormBtn.addEventListener('click', () => {
  taskForm.style.display = taskForm.style.display === 'none' ? 'flex' : 'none';
  if(taskForm.style.display === 'flex') taskInput.focus(); // foca o input
});

// Adicionar tarefa
addTaskBtn.addEventListener('click', () => {
  const taskText = taskInput.value.trim();
  const priority = taskPriority.value;

  if(!taskText) return; // não adiciona vazio

  const task = { text: taskText, priority };
  tasks.push(task);
  localStorage.setItem('fokusTasks', JSON.stringify(tasks));

  renderTasks();
  taskInput.value = '';
  taskForm.style.display = 'none'; // fecha o formulário
});

// Renderizar tarefas
function renderTasks() {
  taskList.innerHTML = '';
  tasks.forEach((task, index) => {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.textContent = task.text;

    const badge = document.createElement('span');
    badge.className = 'badge rounded-pill';
    if(task.priority === 'Baixa') badge.classList.add('bg-success');
    else if(task.priority === 'Média') badge.classList.add('bg-warning');
    else badge.classList.add('bg-danger');
    badge.textContent = task.priority;

    li.appendChild(badge);
    taskList.appendChild(li);
  });

  // Atualiza contador
  document.getElementById('counter').textContent = `${tasks.length} tarefas`;
}

// Renderiza ao carregar
renderTasks();




