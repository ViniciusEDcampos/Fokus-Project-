if (!window.cronometroInicializado) {
  window.cronometroInicializado = true;
 
  let timer;
  let isRunning = false; // Controla se o cronômetro está rodando
  let isFinished = false; // Flag para garantir que o tempo só é salvo uma vez
  let timeRemaining = 25 * 60; // Tempo em segundos (inicialmente 25 minutos)
  let selectedTime = 25;       // Minutos selecionados (inicialmente 25 minutos)
 
  const timeDisplay = document.querySelector(".timer h2");
  const startButton = document.querySelector(".start");
  const resetButton = document.querySelector(".reset");
  const optionButtons = document.querySelectorAll(".opcoes button");
  const subtitle = document.querySelector(".timer h3");
  const totalTimeText = document.getElementById("tempo-total");
  const sessionText = document.getElementById("tempo-sessao");
  const statusText = document.getElementById("status-sessao");
 
  const circleProgress = document.querySelector(".progress-ring__progress");
  const radius = 124;
  const circumference = 2 * Math.PI * radius;
  if (circleProgress) {
    circleProgress.setAttribute("stroke-dasharray", circumference);
    circleProgress.setAttribute("stroke-dashoffset", circumference);
  }
 
  // ==================== LOCAL STORAGE ====================
  function salvarEstado() {
    localStorage.setItem("cronometroEstado", JSON.stringify({
      timeRemaining,
      isRunning,
      selectedTime,
      startTimestamp: Date.now()
    }));
  }
 
  function carregarEstado() {
    const dados = localStorage.getItem("cronometroEstado");
    if (!dados) return;
 
    const estado = JSON.parse(dados);
    selectedTime = estado.selectedTime || 25;
 
    if (estado.isRunning) {
      const decorrido = Math.floor((Date.now() - estado.startTimestamp) / 1000);
      timeRemaining = estado.timeRemaining - decorrido;
      if (timeRemaining < 0) timeRemaining = 0;
      isRunning = true;
      startTimer(); // Continua rodando
    } else {
      timeRemaining = estado.timeRemaining;
      isRunning = false;
      updateStartButton("paused");
    }
 
    if (timeDisplay) timeDisplay.textContent = formatTime(timeRemaining);
    updateProgress();
  }
 
  // ==================== BACKEND ====================
  function salvarTempo(segundos, observacao = "") {
    return fetch("cronometro.php?action=salvar", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `segundos=${segundos}&observacao=${encodeURIComponent(observacao)}`
    })
      .then((r) => r.json())
      .then((resp) => {
        console.log("Resposta do servidor:", resp);
        carregarEstatisticas();
        return resp;
      })
      .catch((err) => console.error("Erro ao salvar:", err));
  }
 
  function carregarEstatisticas() {
    fetch("cronometro.php?action=estatisticas")
      .then((res) => res.json())
      .then((data) => {
        if (data.ok) {
          if (totalTimeText)
            totalTimeText.textContent = data.tempo_formatado || `${data.total_minutos} min`;
          if (sessionText)
            sessionText.textContent = selectedTime + "min";
        }
      })
      .catch((err) => console.error("Erro ao carregar estatísticas:", err));
  }
 
  // ==================== VISUAL ====================
  function updateProgress() {
    if (!circleProgress) return;
    const percent = (selectedTime * 60 - timeRemaining) / (selectedTime * 60);
    const offset = circumference * (1 - percent);
    circleProgress.setAttribute("stroke-dashoffset", offset);
  }
 
  function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes < 10 ? "0" : ""}${minutes}:${secs < 10 ? "0" : ""}${secs}`;
  }
 
  function toggleOptionButtons(disabled) {
    optionButtons.forEach((btn) => {
      btn.disabled = disabled;
      btn.style.opacity = disabled ? "0.5" : "1";
      btn.style.cursor = disabled ? "not-allowed" : "pointer";
    });
  }
 
  function updateStartButton(state) {
    if (!startButton) return;
    if (state === "paused") {
      startButton.classList.remove("running-red");
      startButton.classList.add("running-blue");
      startButton.innerHTML = '<ion-icon name="play-outline"></ion-icon><p>Iniciar</p>';
      if (subtitle) subtitle.textContent = "Pronto para começar";
      if (statusText) statusText.textContent = "Pausado";
    } else if (state === "running") {
      startButton.classList.remove("running-blue");
      startButton.classList.add("running-red");
      startButton.innerHTML = '<ion-icon name="pause-outline"></ion-icon><p>Pausar</p>';
      if (subtitle) subtitle.textContent = "Focando nos estudos...";
      if (statusText) statusText.textContent = "Em andamento";
    }
  }
 
  // ==================== CONTROLE ====================
  function startTimer() {
    if (isRunning || isFinished) return; // Impede que o cronômetro inicie novamente se já terminou ou foi pausado
 
    isRunning = true;
    updateStartButton("running");
    toggleOptionButtons(true);
 
    timer = setInterval(() => {
      if (timeRemaining > 0) {
        timeRemaining--;
        if (timeDisplay) timeDisplay.textContent = formatTime(timeRemaining);
        updateProgress();
        salvarEstado(); // 🔥 Guarda o estado no navegador
      } else {
        clearInterval(timer);
        isRunning = false;
        isFinished = true; // Marcar como terminado
        updateStartButton("paused");
        toggleOptionButtons(false);
 
        salvarTempo(selectedTime * 60, "Sessão concluída") // Salva o tempo ao final
          .then(() => carregarEstatisticas());
 
        timeRemaining = selectedTime * 60;
        updateProgress();
        localStorage.removeItem("cronometroEstado");
 
        alert("Tempo esgotado! Faça uma pausa.");
      }
    }, 1000);
  }
 
  function stopTimer() {
    if (timer) clearInterval(timer);
 
    let tempoRodado = selectedTime * 60 - timeRemaining;
 
    // Evita salvar tempo duplicado
    if (tempoRodado > 0 && !isFinished) {
      salvarTempo(tempoRodado, "Sessão pausada")
        .then(() => carregarEstatisticas());
    }
 
    isRunning = false;
    isFinished = false; // Reseta a flag
    updateStartButton("paused");
    salvarEstado(); // 🔥 Salva estado parado
  }
 
  function resetTimer() {
    if (timer) clearInterval(timer);
 
    let tempoRodado = selectedTime * 60 - timeRemaining;
    if (tempoRodado > 0 && !isFinished) {
      salvarTempo(tempoRodado, "Sessão reiniciada");
    }
 
    isRunning = false;
    isFinished = false; // Reseta a flag
    timeRemaining = selectedTime * 60;
    if (timeDisplay) timeDisplay.textContent = formatTime(timeRemaining);
    updateStartButton("paused");
    toggleOptionButtons(false);
    updateProgress();
    salvarEstado();
    if (statusText) statusText.textContent = "Pronto";
  }
 
  function selectTime(e) {
    selectedTime = parseInt(e.target.dataset.time);
    timeRemaining = selectedTime * 60;
    if (timeDisplay) timeDisplay.textContent = formatTime(timeRemaining);
    if (sessionText) sessionText.textContent = selectedTime + "min";
 
    optionButtons.forEach((btn) => btn.classList.remove("active"));
    e.target.classList.add("active");
    updateProgress();
    salvarEstado();
  }
 
  // ==================== EVENTOS ====================
  if (startButton) {
    startButton.addEventListener("click", () => {
      if (isRunning) stopTimer();
      else startTimer();
    });
  }
 
  if (resetButton) resetButton.addEventListener("click", resetTimer);
 
  optionButtons.forEach((btn) => btn.addEventListener("click", selectTime));
 
  window.addEventListener("DOMContentLoaded", () => {
    carregarEstado(); // 🔥 Recupera se tinha cronômetro rodando
    const defaultBtn = document.querySelector(".opcoes button.active") || optionButtons[1];
    if (defaultBtn) defaultBtn.click();
    updateStartButton("paused");
    carregarEstatisticas();
    if (statusText) statusText.textContent = "Pronto";
  });
}