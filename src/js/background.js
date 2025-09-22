const background = document.querySelector('.background');
const numShapes =  15; // mais formas para ficar cheio

for (let i = 0; i < numShapes; i++) {
  const shape = document.createElement('span');
  shape.classList.add('shape');

  // tamanho aleatório
  const size = Math.floor(Math.random() * 150) + 30; // 30px até 180px
  shape.style.width = `${size}px`;
  shape.style.height = `${size}px`;

  // posição aleatória
  shape.style.top = `${Math.random() * 100}%`;
  shape.style.left = `${Math.random() * 100}%`;

  // forma: círculo ou quadrado
  shape.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';

  // cor aleatória suave
  const colors = [
    'rgba(0,150,255,0.25)',
    'rgba(14, 12, 182, 0.25)',
    'rgba(0, 217, 255, 0.25)',
    'rgba(0, 183, 255, 0.25)',
    'rgba(100,100,255,0.25)'
  ];
  shape.style.background = colors[Math.floor(Math.random() * colors.length)];

  // animação com duração e delay aleatório
  const duration = Math.random() * 20 + 15; // entre 15s e 35s
  const delay = Math.random() * 10; // atraso inicial diferente
  shape.style.animationDuration = `${duration}s`;
  shape.style.animationDelay = `${delay}s`;

  background.appendChild(shape);
}