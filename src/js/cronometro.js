let timer;
let isRunning = false;
let timeRemaining = 25 * 60; // Tempo restante do cronômetro em segundos (25 minutos)
let selectedTime = 25; // Tempo de estudo selecionado pelo usuário
let totalSegundosAcumulados = 0; // Total de tempo acumulado até o momento

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
circleProgress.setAttribute("stroke-dasharray", circumference);
circleProgress.setAttribute("stroke-dashoffset", circumference);

// Função para salvar o tempo total no banco de dados (chamada após reiniciar ou finalizar o cronômetro)
function salvarTempo(segundos, observacao = "") {
    fetch("salvar_tempo.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `segundos=${segundos}&observacao=${encodeURIComponent(observacao)}`
    })
    .then((r) => r.json())
    .then((resp) => {
        console.log("Resposta do servidor:", resp);
        carregarEstatisticas(); // Atualiza o painel depois de salvar
    })
    .catch((err) => console.error("Erro ao salvar:", err));
}

// Função para carregar as estatísticas do dia
function carregarEstatisticas() {
    fetch("estatisticas_hoje.php")
        .then((res) => res.json())
        .then((data) => {
            if (data.ok) {
                totalTimeText.textContent = data.hhmm + "h"; // Atualiza o tempo total
            }
        })
        .catch((err) => console.error("Erro ao carregar estatísticas:", err));
}

// Função para atualizar a barra de progresso
function updateProgress() {
    const percent = (selectedTime * 60 - timeRemaining) / (selectedTime * 60);
    const offset = circumference * (1 - percent);
    circleProgress.setAttribute("stroke-dashoffset", offset);
}

// Função para formatar o tempo em minutos:segundos
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes < 10 ? "0" : ""}${minutes}:${secs < 10 ? "0" : ""}${secs}`;
}

// Função para alterar os botões de tempo (15min, 25min, etc.)
function toggleOptionButtons(disabled) {
    optionButtons.forEach((btn) => {
        btn.disabled = disabled;
        btn.style.opacity = disabled ? "0.5" : "1";
        btn.style.cursor = disabled ? "not-allowed" : "pointer";
    });
}

// Função para alterar o botão de início/pausa
function updateStartButton(state) {
    if (state === "paused") {
        startButton.classList.remove("running-red");
        startButton.classList.add("running-blue");
        startButton.innerHTML = '<ion-icon name="play-outline"></ion-icon><p>Iniciar</p>';
        subtitle.textContent = "Pronto para começar";
        statusText.textContent = "Pausado";
    } else if (state === "running") {
        startButton.classList.remove("running-blue");
        startButton.classList.add("running-red");
        startButton.innerHTML = '<ion-icon name="pause-outline"></ion-icon><p>Pausar</p>';
        subtitle.textContent = "Focando nos estudos...";
        statusText.textContent = "Em andamento";
    }
}

// Função para iniciar o cronômetro
function startTimer() {
    if (isRunning) return;
    isRunning = true;
    updateStartButton("running");
    toggleOptionButtons(true);

    timer = setInterval(() => {
        if (timeRemaining > 0) {
            timeRemaining--;
            timeDisplay.textContent = formatTime(timeRemaining);
            updateProgress();
        } else {
            clearInterval(timer);
            isRunning = false;
            updateStartButton("paused");
            toggleOptionButtons(false);

            // Salva o tempo após o cronômetro terminar
            salvarTempo(selectedTime * 60, "Sessão concluída");

            timeRemaining = selectedTime * 60;
            updateProgress();

            alert("Tempo esgotado! Faça uma pausa.");
        }
    }, 1000);
}

// Função para parar o cronômetro
function stopTimer() {
    if (timer) clearInterval(timer);

    // Calcula quanto tempo foi rodado até parar
    let tempoRodado = selectedTime * 60 - timeRemaining;
    if (tempoRodado > 0) {
        salvarTempo(tempoRodado, "Sessão interrompida");
    }

    isRunning = false;
    updateStartButton("paused");
}

// Função para reiniciar o cronômetro
function resetTimer() {
    if (timer) clearInterval(timer);

    // Calcula o tempo rodado até reiniciar
    let tempoRodado = selectedTime * 60 - timeRemaining;
    if (tempoRodado > 0) {
        salvarTempo(tempoRodado, "Sessão reiniciada");
    }

    isRunning = false;
    timeRemaining = selectedTime * 60;
    timeDisplay.textContent = formatTime(timeRemaining);
    updateStartButton("paused");
    toggleOptionButtons(false);
    updateProgress();
}

// Função para selecionar o tempo de estudo (15, 25, 45 minutos)
function selectTime(e) {
    selectedTime = parseInt(e.target.dataset.time);
    timeRemaining = selectedTime * 60;
    timeDisplay.textContent = formatTime(timeRemaining);

    // Atualiza sessão atual
    sessionText.textContent = selectedTime + "min";

    optionButtons.forEach((btn) => btn.classList.remove("active"));
    e.target.classList.add("active");
    updateProgress();
}

// Evento de clique no botão de iniciar/pausar
startButton.addEventListener("click", () => {
    if (isRunning) stopTimer();
    else startTimer();
});

// Evento de clique no botão de reiniciar
resetButton.addEventListener("click", resetTimer);

// Evento de clique nos botões de tempo (15, 25, 45 minutos)
optionButtons.forEach((btn) => btn.addEventListener("click", selectTime));

// Inicializa o cronômetro na página
window.addEventListener("DOMContentLoaded", () => {
    const defaultBtn = document.querySelector(".opcoes button.active") || optionButtons[1];
    defaultBtn.click();
    updateStartButton("paused");
    carregarEstatisticas();
});
