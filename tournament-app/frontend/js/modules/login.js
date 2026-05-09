const cloudGame = document.getElementById('cloudGame');

const startBtn = document.getElementById('startGameBtn');
const pauseBtn = document.getElementById('pauseGameBtn');
const endBtn = document.getElementById('endGameBtn');

let gameInterval;
let obstacleInterval;

let gameRunning = false;
let gamePaused = false;

/* =========================
   MENSAJES
========================= */

function showMessage(text, type = 'info') {

    const oldMessage =
        document.querySelector('.system-message');

    if (oldMessage) {
        oldMessage.remove();
    }

    const message =
        document.createElement('div');

    message.classList.add(
        'system-message'
    );

    message.classList.add(type);

    message.innerHTML = text;

    document.body.appendChild(message);

    setTimeout(() => {
        message.classList.add('show');
    }, 10);

    setTimeout(() => {

        message.classList.remove('show');

        setTimeout(() => {
            message.remove();
        }, 400);

    }, 3500);
}

/* =========================
   JUGADOR
========================= */

const player =
    document.createElement('div');

player.classList.add(
    'player-cloud'
);

let playerY = 80;
let velocity = 0;

const gravity = 0.6;
const jumpForce = -10;

function setupPlayer() {

    player.style.left = '40px';

    player.style.top =
        playerY + 'px';

    cloudGame.appendChild(player);
}

function updatePlayer() {

    velocity += gravity;

    playerY += velocity;

    if (playerY < 0) {

        playerY = 0;
        velocity = 0;
    }

    const maxY =
        cloudGame.clientHeight - 45;

    if (playerY > maxY) {

        playerY = maxY;
        velocity = 0;
    }

    player.style.top =
        playerY + 'px';
}

/* =========================
   SALTO
========================= */

function jump() {

    velocity = jumpForce;
}

/* =========================
   CONTROLES
========================= */

document.addEventListener(
    'keydown',
    e => {

        if (!gameRunning) return;

        if (
            e.code === 'ArrowUp' ||
            e.code === 'Space' ||
            e.code === 'KeyW'
        ) {
            jump();
        }
    });

/* =========================
   OBSTACULOS
========================= */

function createObstacle() {

    const obstacle =
        document.createElement('div');

    obstacle.classList.add(
        'obstacle'
    );

    const obstacleHeight =
        50 + Math.random() * 120;

    obstacle.style.height =
        obstacleHeight + 'px';

    obstacle.style.left =
        cloudGame.clientWidth + 'px';

    const positionType =
        Math.random() > 0.5 ?
        'top' :
        'bottom';

    if (positionType === 'top') {

        obstacle.style.top = '0px';

        obstacle.style.bottom =
            'auto';

        obstacle.style.borderRadius =
            '0 0 14px 14px';

    } else {

        obstacle.style.bottom = '0px';

        obstacle.style.top = 'auto';

        obstacle.style.borderRadius =
            '14px 14px 0 0';
    }

    cloudGame.appendChild(
        obstacle
    );

    let obstacleX =
        cloudGame.clientWidth;

    const obstacleMove =
        setInterval(() => {

            if (!gameRunning ||
                gamePaused
            ) return;

            obstacleX -= 10;

            obstacle.style.left =
                obstacleX + 'px';

            const playerRect =
                player.getBoundingClientRect();

            const obstacleRect =
                obstacle.getBoundingClientRect();

            const collision =
                playerRect.left <
                obstacleRect.right &&

                playerRect.right >
                obstacleRect.left &&

                playerRect.top <
                obstacleRect.bottom &&

                playerRect.bottom >
                obstacleRect.top;

            if (collision) {

                createExplosion();

                endGame();

                showMessage(
                    '💥 Has perdido',
                    'error'
                );

                clearInterval(
                    obstacleMove
                );
            }

            if (obstacleX < -60) {

                obstacle.remove();

                clearInterval(
                    obstacleMove
                );
            }

        }, 20);
}

/* =========================
   PARTICULAS
========================= */

function createExplosion() {

    for (let i = 0; i < 15; i++) {

        const particle =
            document.createElement('div');

        particle.classList.add(
            'particle'
        );

        particle.style.left =
            player.offsetLeft + 20 + 'px';

        particle.style.top =
            player.offsetTop + 20 + 'px';

        cloudGame.appendChild(
            particle
        );

        const x =
            (Math.random() - 0.5) *
            200;

        const y =
            (Math.random() - 0.5) *
            200;

        particle.animate(
            [{
                    transform: 'translate(0,0) scale(1)',

                    opacity: 1
                },
                {
                    transform: `translate(${x}px,${y}px) scale(0)`,

                    opacity: 0
                }
            ], {
                duration: 700,
                easing: 'ease-out'
            }
        );

        setTimeout(() => {
            particle.remove();
        }, 700);
    }
}

/* =========================
   CONTROLES JUEGO
========================= */

function startGame() {

    endGame();

    gameRunning = true;

    gamePaused = false;

    pauseBtn.textContent =
        'Pausar';

    playerY = 80;

    velocity = 0;

    cloudGame.innerHTML = '';

    setupPlayer();

    gameInterval =
        setInterval(() => {

            if (!gamePaused) {

                updatePlayer();
            }

        }, 20);

    obstacleInterval =
        setInterval(() => {

            if (!gamePaused) {

                createObstacle();
            }

        }, 1200);

    showMessage(
        '☁️ Juego iniciado',
        'success'
    );
}

function pauseGame() {

    if (!gameRunning) return;

    gamePaused = !gamePaused;

    pauseBtn.textContent =
        gamePaused ?
        'Continuar' :
        'Pausar';

    showMessage(
        gamePaused ?
        '⏸ Juego pausado' :
        '▶ Juego reanudado',
        'info'
    );
}

function endGame() {

    gameRunning = false;

    clearInterval(gameInterval);

    clearInterval(
        obstacleInterval
    );

    const obstacles =
        document.querySelectorAll(
            '.obstacle'
        );

    obstacles.forEach(o =>
        o.remove()
    );
}

/* =========================
   BOTONES
========================= */

startBtn.addEventListener(
    'click',
    startGame
);

pauseBtn.addEventListener(
    'click',
    pauseGame
);

endBtn.addEventListener(
    'click',
    () => {

        endGame();

        showMessage(
            '🛑 Juego terminado',
            'info'
        );
    });

/* =========================
   INICIALIZAR
========================= */

setupPlayer();

/* =========================
   LOGIN
========================= */

const loginForm =
    document.getElementById(
        'loginForm'
    );

let attempts = 3;

loginForm.addEventListener(
    'submit',
    async e => {

        e.preventDefault();

        if (attempts <= 0) {

            showMessage(
                '🚫 Sistema bloqueado',
                'error'
            );

            return;
        }

        const username =
            document.getElementById(
                'username'
            ).value;

        const password =
            document.getElementById(
                'password'
            ).value;

        try {

            const response =
                await fetch(
                    'http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/login/login_api.php?action=iniciar-sesion', {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json'
                        },

                        body: JSON.stringify({
                            username,
                            password
                        })
                    }
                );

            const data =
                await response.json();

            console.log(data);

            if (data.success) {

                sessionStorage.setItem(
                    'user',
                    JSON.stringify({
                        username: username,
                        rol: data.rol || 'Administrador'
                    })
                );

                showMessage(
                    '✅ Login correcto',
                    'success'
                );

                setTimeout(() => {

                    window.location.href =
                        '../pages/index.html';

                }, 1500);

            } else {

                attempts--;

                document.getElementById(
                        'attemptsInfo'
                    ).textContent =
                    `Intentos restantes: ${attempts}`;

                showMessage(
                    `❌ ${data.message || 'Credenciales incorrectas'}`,
                    'error'
                );
            }

        } catch (error) {

            console.error(error);

            /* =========================
               MODO LOCAL DESARROLLO
            ========================= */

            sessionStorage.setItem(
                'user',
                JSON.stringify({
                    username: 'Admin',
                    rol: 'Modo Local'
                })
            );

            showMessage(
                '⚠ Backend desconectado - Entrando en modo local',
                'info'
            );

            setTimeout(() => {

                window.location.href =
                    '../pages/index.html';

            }, 1500);
        }
    });