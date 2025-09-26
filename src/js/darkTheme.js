const btnTema = document.getElementById('btn-tema');
const navigation = document.getElementById('navigation');
const userBox = document.querySelector('.user');
const icon = btnTema.querySelector('i');
const footer = document.getElementById('footer');

// Função para aplicar o tema
function applyTheme(mode) {
  if (mode === 'dark') {
    document.body.classList.add('dark-mode');
    navigation.classList.add('dark-mode');
    userBox.classList.add('dark-mode');
    footer.classList.add('dark-mode');

    icon.classList.remove('fi-rr-moon');
    icon.classList.add('fi-rr-sun');
    icon.style.color = "yellow";
  } else {
    document.body.classList.remove('dark-mode');
    navigation.classList.remove('dark-mode');
    userBox.classList.remove('dark-mode');
    footer.classList.remove('dark-mode')

    icon.classList.remove('fi-rr-sun');
    icon.classList.add('fi-rr-moon');
    icon.style.color = "white";
  }

  // salva no localStorage
  localStorage.setItem('tema', mode);
}

// Alternar tema no clique
btnTema.onclick = () => {
  const currentTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  applyTheme(newTheme);
};

// Aplicar tema salvo ao carregar a página
window.onload = () => {
  const tema = localStorage.getItem('tema') || 'light'; // padrão light
  applyTheme(tema);
};

// Renderiza ao carregar
renderTasks();


