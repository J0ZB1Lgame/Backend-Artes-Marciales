// frontend/js/modules/login.js
// Maneja el mini-juego y el formulario de autenticación de login.html

const cloudGame = document.getElementById('cloudGame');
const startBtn  = document.getElementById('startGameBtn');
const pauseBtn  = document.getElementById('pauseGameBtn');
const endBtn    = document.getElementById('endGameBtn');

let gameInterval;
let obstacleInterval;
let gameRunning = false;
let gamePaused  = false;

/* =========================
   MENSAJES
========================= */
function showMessage(text, type = 'info') {
    const old = document.querySelector('.system-message');
    if (old) old.remove();

    const msg = document.createElement('div');
    msg.className = `system-message ${type}`;
    msg.innerHTML = text;
    document.body.appendChild(msg);

    setTimeout(() => msg.classList.add('show'), 10);
    setTimeout(() => {
        msg.classList.remove('show');
        setTimeout(() => msg.remove(), 400);
    }, 3500);
}

/* =========================
   JUGADOR
========================= */
const player = document.createElement('div');
player.classList.add('player-cloud');

let playerY  = 80;
let velocity = 0;
const gravity   = 0.6;
const jumpForce = -10;

function setupPlayer() {
    player.style.left = '40px';
    player.style.top  = playerY + 'px';
    cloudGame.appendChild(player);
}

function updatePlayer() {
    velocity += gravity;
    playerY  += velocity;
    if (playerY < 0)                            { playerY = 0; velocity = 0; }
    if (playerY > cloudGame.clientHeight - 45)  { playerY = cloudGame.clientHeight - 45; velocity = 0; }
    player.style.top = playerY + 'px';
}

/* =========================
   SALTO
========================= */
function jump() { velocity = jumpForce; }

document.addEventListener('keydown', e => {
    if (!gameRunning) return;
    if (e.code === 'ArrowUp' || e.code === 'Space' || e.code === 'KeyW') jump();
});

/* =========================
   OBSTÁCULOS
========================= */
function createObstacle() {
    const obstacle = document.createElement('div');
    obstacle.classList.add('obstacle');

    const h = 50 + Math.random() * 120;
    obstacle.style.height = h + 'px';
    obstacle.style.left   = cloudGame.clientWidth + 'px';

    if (Math.random() > 0.5) {
        obstacle.style.top           = '0px';
        obstacle.style.borderRadius  = '0 0 14px 14px';
    } else {
        obstacle.style.bottom        = '0px';
        obstacle.style.borderRadius  = '14px 14px 0 0';
    }
    cloudGame.appendChild(obstacle);

    let ox = cloudGame.clientWidth;
    const move = setInterval(() => {
        if (!gameRunning || gamePaused) return;
        ox -= 10;
        obstacle.style.left = ox + 'px';

        const pr = player.getBoundingClientRect();
        const or = obstacle.getBoundingClientRect();
        const hit = pr.left < or.right && pr.right > or.left && pr.top < or.bottom && pr.bottom > or.top;
        if (hit) {
            createExplosion();
            endGame();
            showMessage('💥 Has perdido', 'error');
            clearInterval(move);
        }
        if (ox < -60) { obstacle.remove(); clearInterval(move); }
    }, 20);
}

/* =========================
   EXPLOSIÓN
========================= */
function createExplosion() {
    for (let i = 0; i < 15; i++) {
        const p = document.createElement('div');
        p.classList.add('particle');
        p.style.left = player.offsetLeft + 20 + 'px';
        p.style.top  = player.offsetTop  + 20 + 'px';
        cloudGame.appendChild(p);
        const x = (Math.random() - 0.5) * 200;
        const y = (Math.random() - 0.5) * 200;
        p.animate(
            [{ transform: 'translate(0,0) scale(1)', opacity: 1 },
             { transform: `translate(${x}px,${y}px) scale(0)`, opacity: 0 }],
            { duration: 700, easing: 'ease-out' }
        );
        setTimeout(() => p.remove(), 700);
    }
}

/* =========================
   CONTROLES JUEGO
========================= */
function startGame() {
    endGame();
    gameRunning = true;
    gamePaused  = false;
    pauseBtn.textContent = 'Pausar';
    playerY  = 80;
    velocity = 0;
    cloudGame.innerHTML = '';
    setupPlayer();
    gameInterval    = setInterval(() => { if (!gamePaused) updatePlayer(); }, 20);
    obstacleInterval = setInterval(() => { if (!gamePaused) createObstacle(); }, 1200);
    showMessage('☁️ Juego iniciado', 'success');
}

function pauseGame() {
    if (!gameRunning) return;
    gamePaused = !gamePaused;
    pauseBtn.textContent = gamePaused ? 'Continuar' : 'Pausar';
    showMessage(gamePaused ? '⏸ Juego pausado' : '▶ Juego reanudado', 'info');
}

function endGame() {
    gameRunning = false;
    clearInterval(gameInterval);
    clearInterval(obstacleInterval);
    document.querySelectorAll('.obstacle').forEach(o => o.remove());
}

startBtn.addEventListener('click', startGame);
pauseBtn.addEventListener('click', pauseGame);
endBtn.addEventListener('click', () => { endGame(); showMessage('🛑 Juego terminado', 'info'); });

setupPlayer();

/* =========================
   LOGIN
========================= */
const loginForm = document.getElementById('loginForm');
let attempts = 3;

loginForm.addEventListener('submit', async e => {
    e.preventDefault();
    if (attempts <= 0) { showMessage('🚫 Sistema bloqueado', 'error'); return; }

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const btnLogin = document.getElementById('btnLogin');
    btnLogin.disabled = true;

    try {
        // apiPost usa BASE_URL definido en api.js (cargado antes que este script)
        const data = await apiPost(EP_LOGIN + '?action=iniciar-sesion', { username, password });

        // El backend devuelve {idUsuario, username, rol} en data cuando el login es exitoso
        sessionStorage.setItem(SESSION_KEY, JSON.stringify({
            idUsuario: data.idUsuario ?? 1,
            username:  data.username  ?? username,
            rol:       data.rol       ?? 'Administrador',
            idSesion:  data.idSesion  ?? null,
        }));

        showMessage('✅ Acceso concedido', 'success');
        setTimeout(() => { window.location.href = '../pages/index.html'; }, 1200);

    } catch (err) {
        attempts--;
        document.getElementById('attemptsInfo').textContent = `Intentos restantes: ${attempts}`;
        showMessage(`❌ ${err.message || 'Credenciales incorrectas'}`, 'error');
        btnLogin.disabled = false;
    }
});
