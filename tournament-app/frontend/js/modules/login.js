/**
 * login.js — Módulo de autenticación
 * Budokai System · Torneo de Artes Marciales
 *
 * Contrato con el backend (login_api.php):
 *   POST ?action=iniciar-sesion  →  { username, password }
 *   Respuesta OK  →  { status:"success", data:{ idUsuario, username, rol } }
 *   Respuesta ERR →  { status:"error",   message:"..." }
 *
 * Sesión guardada en sessionStorage:
 *   budokai_session = { idUsuario, username, rol, idSesion? }
 */

'use strict';

/* ── Configuración ──────────────────────────────────────────────── */
const LOGIN_CONFIG = {
  // URL del endpoint — ajusta el puerto si trabajas en local
  apiUrl: 'http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/login/login_api.php',

  // Máximo de intentos fallidos antes de bloquear (según RNF-04 del SRS)
  maxAttempts: 3,

  // Tiempo de bloqueo en segundos (10 min según RNF-04)
  lockoutSeconds: 600,

  // Clave en sessionStorage para la sesión activa
  sessionKey: 'budokai_session',

  // Clave en localStorage para el contador de intentos fallidos
  attemptsKey: 'budokai_failed_attempts',
  lockoutKey:  'budokai_lockout_until',

  // Ruta al dashboard tras login exitoso (relativa a /pages/)
  dashboardUrl: 'index.html',
};

/* ── Referencias DOM ────────────────────────────────────────────── */
const dom = {
  form:           () => document.getElementById('loginForm'),
  usernameInput:  () => document.getElementById('username'),
  passwordInput:  () => document.getElementById('password'),
  toggleBtn:      () => document.getElementById('togglePassword'),
  submitBtn:      () => document.getElementById('btnLogin'),
  errorBanner:    () => document.getElementById('errorBanner'),
  errorMsg:       () => document.getElementById('errorMsg'),
  attemptsInfo:   () => document.getElementById('attemptsInfo'),
  lockoutOverlay: () => document.getElementById('lockoutOverlay'),
  lockoutTimer:   () => document.getElementById('lockoutTimer'),
  toastSuccess:   () => document.getElementById('toastSuccess'),
};

/* ── Estado local ───────────────────────────────────────────────── */
let lockoutInterval = null;

/* ══════════════════════════════════════════════════════════════════
   INICIALIZACIÓN
   ══════════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  // Si ya hay sesión activa, redirigir directo al dashboard
  if (sessionStorage.getItem(LOGIN_CONFIG.sessionKey)) {
    redirectToDashboard();
    return;
  }

  initBackground();
  initTogglePassword();
  initForm();
  checkLockout();
  updateAttemptsUI();
});

/* ══════════════════════════════════════════════════════════════════
   FONDO — imagen o video
   ══════════════════════════════════════════════════════════════════ */
function initBackground() {
  const bgImg   = document.getElementById('bgImage');
  const bgVideo = document.getElementById('bgVideo');
  const bgFallback = document.getElementById('bgFallback');

  if (bgVideo) {
    bgVideo.addEventListener('error', () => {
      bgVideo.style.display = 'none';
      if (bgImg) tryImage(bgImg, bgFallback);
      else if (bgFallback) bgFallback.style.display = 'block';
    });
    return; // el video se maneja solo
  }

  if (bgImg) {
    tryImage(bgImg, bgFallback);
  } else if (bgFallback) {
    bgFallback.style.display = 'block';
  }
}

function tryImage(imgEl, fallbackEl) {
  imgEl.addEventListener('error', () => {
    imgEl.style.display = 'none';
    if (fallbackEl) fallbackEl.style.display = 'block';
  });
}

/* ══════════════════════════════════════════════════════════════════
   TOGGLE CONTRASEÑA
   ══════════════════════════════════════════════════════════════════ */
function initTogglePassword() {
  const btn   = dom.toggleBtn();
  const input = dom.passwordInput();
  if (!btn || !input) return;

  btn.addEventListener('click', () => {
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.textContent = isText ? '👁️' : '🙈';
    btn.setAttribute('aria-label', isText ? 'Mostrar contraseña' : 'Ocultar contraseña');
  });
}

/* ══════════════════════════════════════════════════════════════════
   FORMULARIO
   ══════════════════════════════════════════════════════════════════ */
function initForm() {
  const form = dom.form();
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    await handleLogin();
  });

  // Limpiar error al escribir
  [dom.usernameInput(), dom.passwordInput()].forEach(input => {
    if (!input) return;
    input.addEventListener('input', () => {
      clearError();
      input.classList.remove('is-error');
    });
  });
}

/* ══════════════════════════════════════════════════════════════════
   LÓGICA DE LOGIN
   ══════════════════════════════════════════════════════════════════ */
async function handleLogin() {
  // Verificar bloqueo activo
  if (isLockedOut()) {
    showError('Cuenta bloqueada temporalmente. Espera el contador.');
    return;
  }

  const username = dom.usernameInput()?.value.trim();
  const password = dom.passwordInput()?.value;

  // Validación básica en cliente
  if (!username || !password) {
    showError('Por favor completa todos los campos.');
    if (!username) dom.usernameInput()?.classList.add('is-error');
    if (!password) dom.passwordInput()?.classList.add('is-error');
    return;
  }

  setLoading(true);
  clearError();

  try {
    const response = await fetch(
      `${LOGIN_CONFIG.apiUrl}?action=iniciar-sesion`,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password }),
      }
    );

    const json = await response.json();

    if (response.ok && json.status === 'success') {
      // ── Login exitoso ──────────────────────────────────────────
      resetAttempts();
      saveSession(json.data);
      showToastSuccess(`Bienvenido, ${json.data.username}`);

      // Pequeña pausa para que el usuario vea el toast
      setTimeout(() => redirectToDashboard(), 1200);

    } else {
      // ── Credenciales inválidas ─────────────────────────────────
      const remaining = registerFailedAttempt();

      if (remaining <= 0) {
        startLockout();
      } else {
        const msg = response.status === 401
          ? 'Usuario o contraseña incorrectos.'
          : (json.message || 'Error al iniciar sesión.');
        showError(msg);
        dom.usernameInput()?.classList.add('is-error');
        dom.passwordInput()?.classList.add('is-error');
        updateAttemptsUI();
      }
    }

  } catch (err) {
    // Error de red / servidor caído
    showError('No se pudo conectar con el servidor. Verifica tu conexión.');
    console.error('[Login] Error de red:', err);
  } finally {
    setLoading(false);
  }
}

/* ══════════════════════════════════════════════════════════════════
   SESIÓN
   ══════════════════════════════════════════════════════════════════ */
function saveSession(data) {
  sessionStorage.setItem(LOGIN_CONFIG.sessionKey, JSON.stringify({
    idUsuario: data.idUsuario,
    username:  data.username,
    rol:       data.rol,
    idSesion:  data.idSesion ?? null,
  }));
}

function redirectToDashboard() {
  window.location.href = LOGIN_CONFIG.dashboardUrl;
}

/* ══════════════════════════════════════════════════════════════════
   INTENTOS FALLIDOS Y BLOQUEO
   ══════════════════════════════════════════════════════════════════ */
function getAttempts() {
  return parseInt(localStorage.getItem(LOGIN_CONFIG.attemptsKey) || '0', 10);
}

function registerFailedAttempt() {
  const current = getAttempts() + 1;
  localStorage.setItem(LOGIN_CONFIG.attemptsKey, String(current));
  return LOGIN_CONFIG.maxAttempts - current;
}

function resetAttempts() {
  localStorage.removeItem(LOGIN_CONFIG.attemptsKey);
  localStorage.removeItem(LOGIN_CONFIG.lockoutKey);
}

function isLockedOut() {
  const until = parseInt(localStorage.getItem(LOGIN_CONFIG.lockoutKey) || '0', 10);
  return Date.now() < until;
}

function startLockout() {
  const until = Date.now() + LOGIN_CONFIG.lockoutSeconds * 1000;
  localStorage.setItem(LOGIN_CONFIG.lockoutKey, String(until));
  showLockout();
}

function checkLockout() {
  if (isLockedOut()) showLockout();
}

function showLockout() {
  const overlay = dom.lockoutOverlay();
  if (!overlay) return;
  overlay.classList.add('visible');
  setFormDisabled(true);
  startLockoutTimer();
}

function startLockoutTimer() {
  if (lockoutInterval) clearInterval(lockoutInterval);

  const timerEl = dom.lockoutTimer();

  const tick = () => {
    const until = parseInt(localStorage.getItem(LOGIN_CONFIG.lockoutKey) || '0', 10);
    const remaining = Math.max(0, Math.ceil((until - Date.now()) / 1000));

    if (timerEl) {
      const m = Math.floor(remaining / 60).toString().padStart(2, '0');
      const s = (remaining % 60).toString().padStart(2, '0');
      timerEl.textContent = `${m}:${s}`;
    }

    if (remaining <= 0) {
      clearInterval(lockoutInterval);
      lockoutInterval = null;
      resetAttempts();
      const overlay = dom.lockoutOverlay();
      if (overlay) overlay.classList.remove('visible');
      setFormDisabled(false);
      updateAttemptsUI();
    }
  };

  tick();
  lockoutInterval = setInterval(tick, 1000);
}

/* ══════════════════════════════════════════════════════════════════
   UI HELPERS
   ══════════════════════════════════════════════════════════════════ */
function setLoading(loading) {
  const btn = dom.submitBtn();
  if (!btn) return;
  btn.disabled = loading;
  btn.classList.toggle('loading', loading);
}

function setFormDisabled(disabled) {
  const inputs = [dom.usernameInput(), dom.passwordInput(), dom.submitBtn()];
  inputs.forEach(el => { if (el) el.disabled = disabled; });
}

function showError(msg) {
  const banner = dom.errorBanner();
  const msgEl  = dom.errorMsg();
  if (!banner || !msgEl) return;
  msgEl.textContent = msg;
  banner.classList.add('visible');
  // Re-trigger animation
  banner.style.animation = 'none';
  banner.offsetHeight; // reflow
  banner.style.animation = '';
}

function clearError() {
  const banner = dom.errorBanner();
  if (banner) banner.classList.remove('visible');
}

function updateAttemptsUI() {
  const el = dom.attemptsInfo();
  if (!el) return;

  const attempts  = getAttempts();
  const remaining = LOGIN_CONFIG.maxAttempts - attempts;

  if (attempts === 0) {
    el.textContent = `Intentos restantes: ${LOGIN_CONFIG.maxAttempts}`;
    el.className = 'attempts-info';
    return;
  }

  el.textContent = `Intentos restantes: ${remaining}`;
  el.className = 'attempts-info' + (remaining === 1 ? ' danger' : remaining === 2 ? ' warning' : '');
}

function showToastSuccess(msg) {
  const toast = dom.toastSuccess();
  if (!toast) return;
  toast.querySelector('.toast-msg').textContent = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3000);
}
