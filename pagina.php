<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TAM — Sistema de Gestión</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --red:   #c0392b;
    --red2:  #e74c3c;
    --dark:  #0d0d0d;
    --dark2: #161616;
    --dark3: #1f1f1f;
    --dark4: #2a2a2a;
    --gray:  #555;
    --light: #f0ece4;
    --muted: #888;
    --border:#2a2a2a;
    --font-display:'Bebas Neue',sans-serif;
    --font-body:'DM Sans',sans-serif;
  }

  body { font-family:var(--font-body); background:var(--dark); color:var(--light); min-height:100vh; overflow-x:hidden; }

  /* LOGIN */
  #login-screen { min-height:100vh; display:flex; align-items:center; justify-content:center; position:relative; }
  #login-screen::before {
    content:'TAM'; font-family:var(--font-display); font-size:clamp(120px,25vw,260px);
    color:rgba(192,57,43,0.06); position:absolute; top:50%; left:50%;
    transform:translate(-50%,-50%); pointer-events:none; letter-spacing:.05em; white-space:nowrap;
  }

  .login-box { position:relative; width:100%; max-width:400px; padding:2.5rem; border:1px solid var(--border); background:var(--dark2); }
  .login-box::before { content:''; position:absolute; top:0; left:0; width:60px; height:3px; background:var(--red); }
  .login-logo { font-family:var(--font-display); font-size:2.8rem; letter-spacing:.1em; margin-bottom:.2rem; }
  .login-sub  { font-size:.75rem; color:var(--muted); letter-spacing:.15em; text-transform:uppercase; margin-bottom:2rem; }

  .tabs { display:flex; margin-bottom:1.5rem; border-bottom:1px solid var(--border); }
  .tab-btn { flex:1; padding:.6rem; background:none; border:none; color:var(--muted); font-family:var(--font-body); font-size:.8rem; letter-spacing:.1em; text-transform:uppercase; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px; transition:color .2s,border-color .2s; }
  .tab-btn.active { color:var(--light); border-bottom-color:var(--red); }
  .tab-panel { display:none; }
  .tab-panel.active { display:block; }

  label { display:block; font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:var(--muted); margin-bottom:.4rem; margin-top:1rem; }
  input[type=text],input[type=password] { width:100%; padding:.7rem .9rem; background:var(--dark3); border:1px solid var(--border); color:var(--light); font-family:var(--font-body); font-size:.9rem; outline:none; transition:border-color .2s; }
  input[type=text]:focus,input[type=password]:focus { border-color:var(--red); }
  input[type=number] { width:100%; padding:.7rem .9rem; background:var(--dark3); border:1px solid var(--border); color:var(--light); font-family:var(--font-body); font-size:.9rem; outline:none; }
  select { width:100%; padding:.7rem .9rem; background:var(--dark3); border:1px solid var(--border); color:var(--light); font-family:var(--font-body); font-size:.9rem; outline:none; cursor:pointer; }

  .btn { width:100%; margin-top:1.5rem; padding:.8rem; background:var(--red); border:none; color:var(--light); font-family:var(--font-display); font-size:1.1rem; letter-spacing:.15em; cursor:pointer; transition:background .2s,transform .1s; }
  .btn:hover { background:var(--red2); }
  .btn:active { transform:scale(.98); }

  .msg { margin-top:1rem; padding:.7rem .9rem; font-size:.82rem; display:none; }
  .msg.ok  { background:rgba(39,174,96,.15); color:#2ecc71; border-left:3px solid #2ecc71; }
  .msg.err { background:rgba(192,57,43,.15); color:var(--red2); border-left:3px solid var(--red); }

  /* PANEL */
  #panel-screen { display:none; min-height:100vh; }

  .topbar { display:flex; align-items:center; justify-content:space-between; padding:1rem 2rem; background:var(--dark2); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:10; }
  .topbar-logo { font-family:var(--font-display); font-size:1.6rem; letter-spacing:.1em; }
  .topbar-logo span { color:var(--red); }
  .topbar-right { display:flex; align-items:center; gap:1rem; }
  .user-badge { font-size:.75rem; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); }
  .user-badge strong { color:var(--light); }
  .btn-logout { padding:.4rem 1rem; background:none; border:1px solid var(--border); color:var(--muted); font-family:var(--font-body); font-size:.75rem; letter-spacing:.1em; text-transform:uppercase; cursor:pointer; transition:all .2s; }
  .btn-logout:hover { border-color:var(--red); color:var(--red2); }

  .panel-body { display:grid; grid-template-columns:220px 1fr; min-height:calc(100vh - 57px); }

  .sidebar { background:var(--dark2); border-right:1px solid var(--border); padding:1.5rem 0; }
  .sidebar-section { font-size:.65rem; letter-spacing:.15em; text-transform:uppercase; color:var(--gray); padding:0 1.5rem; margin-bottom:.5rem; margin-top:1.2rem; }
  .nav-item { display:flex; align-items:center; gap:.7rem; padding:.65rem 1.5rem; font-size:.85rem; color:var(--muted); cursor:pointer; transition:all .15s; border-left:2px solid transparent; }
  .nav-item:hover { color:var(--light); background:var(--dark3); }
  .nav-item.active { color:var(--light); background:var(--dark3); border-left-color:var(--red); }
  .nav-dot { width:6px; height:6px; border-radius:50%; background:var(--gray); flex-shrink:0; }
  .nav-item.active .nav-dot { background:var(--red); }

  .main { padding:2rem; overflow-y:auto; }

  .section-header { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid var(--border); }
  .section-title { font-family:var(--font-display); font-size:2rem; letter-spacing:.08em; }
  .section-title span { color:var(--red); }

  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:0 1.5rem; }
  .form-full { grid-column:1/-1; }

  .form-card { background:var(--dark2); border:1px solid var(--border); padding:1.5rem; margin-bottom:1.5rem; position:relative; }
  .form-card::before { content:''; position:absolute; top:0; left:0; width:40px; height:2px; background:var(--red); }
  .form-card h3 { font-family:var(--font-display); font-size:1.1rem; letter-spacing:.1em; margin-bottom:1rem; }

  .btn-sm { padding:.55rem 1.2rem; background:var(--red); border:none; color:var(--light); font-family:var(--font-display); font-size:.9rem; letter-spacing:.1em; cursor:pointer; transition:background .2s; margin-top:1rem; }
  .btn-sm:hover { background:var(--red2); }
  .btn-sm.outline { background:none; border:1px solid var(--border); color:var(--muted); margin-left:.5rem; }
  .btn-sm.outline:hover { border-color:var(--red); color:var(--light); }

  .table-wrap { overflow-x:auto; }
  table { width:100%; border-collapse:collapse; font-size:.85rem; }
  thead tr { border-bottom:1px solid var(--border); }
  th { text-align:left; padding:.7rem 1rem; font-size:.65rem; letter-spacing:.15em; text-transform:uppercase; color:var(--muted); font-weight:400; }
  td { padding:.8rem 1rem; border-bottom:1px solid rgba(255,255,255,.04); color:var(--light); }
  tr:hover td { background:var(--dark3); }

  .badge { display:inline-block; padding:.2rem .6rem; font-size:.68rem; letter-spacing:.08em; text-transform:uppercase; }
  .badge.activo   { background:rgba(39,174,96,.15); color:#2ecc71; }
  .badge.inactivo { background:rgba(192,57,43,.15); color:var(--red2); }

  .action-btns { display:flex; gap:.4rem; }
  .btn-action { padding:.3rem .7rem; font-size:.72rem; letter-spacing:.08em; text-transform:uppercase; border:1px solid var(--border); background:none; color:var(--muted); cursor:pointer; transition:all .15s; }
  .btn-action:hover { border-color:var(--red); color:var(--red2); }

  .view { display:none; }
  .view.active { display:block; }

  .tipo-selector { display:flex; margin-bottom:1.5rem; }
  .tipo-btn { padding:.5rem 1.2rem; background:none; border:1px solid var(--border); color:var(--muted); font-family:var(--font-body); font-size:.75rem; letter-spacing:.1em; text-transform:uppercase; cursor:pointer; transition:all .15s; margin-right:-1px; }
  .tipo-btn.active { background:var(--red); border-color:var(--red); color:var(--light); }

  .token-display { font-family:monospace; font-size:.72rem; color:var(--muted); word-break:break-all; background:var(--dark3); padding:.8rem; border:1px solid var(--border); margin-top:.5rem; }
  .copy-btn { margin-top:.5rem; padding:.35rem .9rem; background:none; border:1px solid var(--border); color:var(--muted); font-size:.72rem; letter-spacing:.08em; cursor:pointer; transition:all .15s; }
  .copy-btn:hover { border-color:var(--red); color:var(--light); }
</style>
</head>
<body>

<!-- LOGIN -->
<div id="login-screen">
  <div class="login-box">
    <div class="login-logo">TAM</div>
    <div class="login-sub">Torneo de Artes Marciales</div>

    <div class="tabs">
      <button class="tab-btn active" onclick="switchTab('login')">Ingresar</button>
      <button class="tab-btn" onclick="switchTab('register')">Crear usuario</button>
    </div>

    <!-- Login -->
    <div class="tab-panel active" id="tab-login">
      <label>Usuario</label>
      <input type="text" id="l-user" placeholder="username" />
      <label>Contraseña</label>
      <input type="password" id="l-pass" placeholder="••••••••" />
      <button class="btn" onclick="doLogin()">Entrar</button>
      <div class="msg" id="msg-login"></div>
    </div>

    <!-- Registro público — usa POST /auth/registro sin token -->
    <div class="tab-panel" id="tab-register">
      <label>Username</label>
      <input type="text" id="r-user" placeholder="nuevo_usuario" />
      <label>Contraseña</label>
      <input type="password" id="r-pass" placeholder="••••••••" />
      <label>Rol</label>
      <select id="r-rol">
        <option value="Administrador">Administrador</option>
        <option value="Coordinador">Coordinador</option>
        <option value="Juez">Juez</option>
        <option value="Médico">Médico</option>
      </select>
      <button class="btn" onclick="doRegistro()">Crear usuario</button>
      <div class="msg" id="msg-register"></div>
    </div>
  </div>
</div>

<!-- PANEL -->
<div id="panel-screen">
  <div class="topbar">
    <div class="topbar-logo">TAM <span>·</span> Panel</div>
    <div class="topbar-right">
      <div class="user-badge">
        <strong id="panel-user">—</strong> &nbsp;|&nbsp; <strong id="panel-rol">—</strong>
      </div>
      <button class="btn-logout" onclick="doLogout()">Cerrar sesión</button>
    </div>
  </div>

  <div class="panel-body">
    <nav class="sidebar">
      <div class="sidebar-section">Staff</div>
      <div class="nav-item active" onclick="showView('staff', this)">
        <div class="nav-dot"></div> Gestión de Staff
      </div>
      <div class="sidebar-section">Sesión</div>
      <div class="nav-item" onclick="showView('sesion', this)">
        <div class="nav-dot"></div> Mi sesión
      </div>
    </nav>

    <main class="main">

      <!-- STAFF -->
      <div class="view active" id="view-staff">
        <div class="section-header">
          <div class="section-title">Gestión de <span>Staff</span></div>
        </div>

        <div class="tipo-selector">
          <button class="tipo-btn active" onclick="setTipo('torneo', this)">Torneo</button>
          <button class="tipo-btn" onclick="setTipo('combate', this)">Combate</button>
          <button class="tipo-btn" onclick="setTipo('juez', this)">Juez</button>
        </div>

        <!-- Formulario -->
        <div class="form-card">
          <h3>Registrar miembro</h3>
          <div class="form-grid">
            <div>
              <label>Nombre</label>
              <input type="text" id="s-nombre" placeholder="Nombre completo" />
            </div>
            <div>
              <label>Turno</label>
              <input type="text" id="s-turno" placeholder="Mañana / Tarde / Noche" />
            </div>
            <div id="zona-field">
              <label>Zona</label>
              <input type="text" id="s-zona" placeholder="Zona A, B, C..." />
            </div>
            <div id="combate-field" style="display:none">
              <label>ID Combate</label>
              <input type="number" id="s-combate" placeholder="ID del combate" />
            </div>
            <div>
              <label>Username</label>
              <input type="text" id="s-username" placeholder="usuario123" />
            </div>
            <div>
              <label>Contraseña</label>
              <input type="password" id="s-password" placeholder="••••••••" />
            </div>
            <div>
              <label>Rol</label>
              <select id="s-rol">
                <option value="Coordinador">Coordinador</option>
                <option value="Juez">Juez</option>
                <option value="Médico">Médico</option>
                <option value="Administrador">Administrador</option>
              </select>
            </div>
          </div>
          <button class="btn-sm" onclick="registrarStaff()">Registrar</button>
          <div class="msg" id="msg-staff"></div>
        </div>

        <!-- Lista -->
        <div class="form-card">
          <h3>Lista — <span id="tipo-label" style="color:var(--red)">Torneo</span></h3>
          <div style="margin-bottom:1rem">
            <button class="btn-sm outline" style="margin-top:0" onclick="listarStaff()">↻ Actualizar</button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th><th>Nombre</th><th>Turno</th>
                  <th>Username</th><th>Rol</th><th>Estado</th><th>Acciones</th>
                </tr>
              </thead>
              <tbody id="staff-tbody">
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">Carga la lista con ↻</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- SESIÓN -->
      <div class="view" id="view-sesion">
        <div class="section-header">
          <div class="section-title">Mi <span>Sesión</span></div>
        </div>

        <div class="form-card" style="max-width:480px">
          <h3>Usuario activo</h3>
          <div id="me-data" style="margin-top:1rem;font-size:.88rem;line-height:2.2;color:var(--muted)">—</div>
        </div>

        <div class="form-card" style="max-width:480px">
          <h3>Token de sesión</h3>
          <p style="font-size:.78rem;color:var(--muted);margin-bottom:.5rem">
            Copia este token para usarlo en Postman o en el front como <code>Authorization: Bearer &lt;token&gt;</code>
          </p>
          <div class="token-display" id="token-display">—</div>
          <button class="copy-btn" onclick="copiarToken()">Copiar token</button>
          <div class="msg" id="msg-sesion"></div>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
  const BASE = 'http://localhost/backend-artes-marciales';
  let token = '';
  let tipoActual = 'torneo';

  function showMsg(id, text, ok) {
    const el = document.getElementById(id);
    el.textContent = text;
    el.className = 'msg ' + (ok ? 'ok' : 'err');
    el.style.display = 'block';
    setTimeout(() => el.style.display = 'none', 4000);
  }

  async function api(method, path, body) {
    const opts = {
      method,
      headers: {
        'Content-Type': 'application/json',
        ...(token ? { 'Authorization': 'Bearer ' + token } : {})
      }
    };
    if (body) opts.body = JSON.stringify(body);
    try {
      const r = await fetch(BASE + path, opts);
      return await r.json();
    } catch(e) {
      return { success: false, message: 'Error de red: ' + e.message };
    }
  }

  function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach((b,i) =>
      b.classList.toggle('active', (i===0&&tab==='login')||(i===1&&tab==='register'))
    );
    document.getElementById('tab-login').classList.toggle('active', tab==='login');
    document.getElementById('tab-register').classList.toggle('active', tab==='register');
  }

  async function doLogin() {
    const username = document.getElementById('l-user').value.trim();
    const password = document.getElementById('l-pass').value.trim();
    if (!username || !password) return showMsg('msg-login','Completa los campos.',false);

    const res = await api('POST', '/auth/login', { username, password });
    if (!res.success) return showMsg('msg-login', res.message, false);

    token = res.data.token;
    document.getElementById('panel-user').textContent = res.data.usuario.username;
    document.getElementById('panel-rol').textContent  = res.data.usuario.rol;
    document.getElementById('token-display').textContent = token;
    document.getElementById('login-screen').style.display = 'none';
    document.getElementById('panel-screen').style.display = 'block';
    cargarMe();
  }

  // Registro público — POST /auth/registro sin token
  async function doRegistro() {
    const username = document.getElementById('r-user').value.trim();
    const password = document.getElementById('r-pass').value.trim();
    const rol      = document.getElementById('r-rol').value;
    if (!username || !password) return showMsg('msg-register','Completa los campos.',false);

    const res = await api('POST', '/auth/registro', { username, password, rol });
    showMsg('msg-register', res.success ? '¡Usuario creado! Ya puedes ingresar.' : res.message, res.success);
    if (res.success) {
      document.getElementById('r-user').value = '';
      document.getElementById('r-pass').value = '';
    }
  }

  async function doLogout() {
    await api('POST', '/auth/logout');
    token = '';
    document.getElementById('login-screen').style.display = 'flex';
    document.getElementById('panel-screen').style.display = 'none';
    document.getElementById('l-user').value = '';
    document.getElementById('l-pass').value = '';
  }

  function showView(name, el) {
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    document.getElementById('view-' + name).classList.add('active');
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    el.classList.add('active');
  }

  function setTipo(tipo, btn) {
    tipoActual = tipo;
    document.querySelectorAll('.tipo-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tipo-label').textContent = tipo.charAt(0).toUpperCase() + tipo.slice(1);
    document.getElementById('zona-field').style.display    = tipo==='torneo'  ? 'block' : 'none';
    document.getElementById('combate-field').style.display = tipo==='combate' ? 'block' : 'none';
    document.getElementById('staff-tbody').innerHTML =
      '<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">Carga la lista con ↻</td></tr>';
  }

  async function registrarStaff() {
    const body = {
      nombre:   document.getElementById('s-nombre').value.trim(),
      turno:    document.getElementById('s-turno').value.trim(),
      username: document.getElementById('s-username').value.trim(),
      password: document.getElementById('s-password').value.trim(),
      rol:      document.getElementById('s-rol').value,
    };
    if (tipoActual==='torneo')  body.zona      = document.getElementById('s-zona').value.trim();
    if (tipoActual==='combate') body.idCombate = parseInt(document.getElementById('s-combate').value)||null;

    if (!body.nombre||!body.turno||!body.username||!body.password)
      return showMsg('msg-staff','Completa todos los campos requeridos.',false);

    const res = await api('POST', `/staff/${tipoActual}/registrar`, body);
    showMsg('msg-staff', res.success ? `¡Registrado! ID: ${res.data?.staffId}` : res.message, res.success);
    if (res.success) listarStaff();
  }

  async function listarStaff() {
    const res = await api('GET', `/staff/${tipoActual}/listar`);
    const tbody = document.getElementById('staff-tbody');
    if (!res.success || !res.data?.length) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">Sin miembros registrados.</td></tr>';
      return;
    }
    tbody.innerHTML = res.data.map(m => `
      <tr>
        <td>${m.id}</td>
        <td>${m.nombre}</td>
        <td>${m.turno}</td>
        <td>${m.username}</td>
        <td>${m.rol}</td>
        <td><span class="badge ${m.estado?'activo':'inactivo'}">${m.estado?'Activo':'Inactivo'}</span></td>
        <td>
          <div class="action-btns">
            <button class="btn-action" onclick="eliminarStaff(${m.id})">Eliminar</button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  async function eliminarStaff(id) {
    if (!confirm('¿Eliminar este miembro?')) return;
    const res = await api('DELETE', `/staff/${tipoActual}/eliminar?id=${id}`);
    showMsg('msg-staff', res.message, res.success);
    if (res.success) listarStaff();
  }

  async function cargarMe() {
    const res = await api('GET', '/auth/me');
    if (!res.success) return;
    const u = res.data;
    document.getElementById('me-data').innerHTML = `
      <div><span>ID Usuario:</span> <strong style="color:var(--light)">${u.idUsuario}</strong></div>
      <div><span>Username:</span>  <strong style="color:var(--light)">${u.username}</strong></div>
      <div><span>Rol:</span>       <strong style="color:var(--light)">${u.rol}</strong></div>
    `;
  }

  function copiarToken() {
    if (!token) return showMsg('msg-sesion','No hay token activo.',false);
    navigator.clipboard.writeText(token).then(() =>
      showMsg('msg-sesion','¡Token copiado al portapapeles!',true)
    );
  }

  document.addEventListener('keydown', e => {
    if (e.key==='Enter' && document.getElementById('login-screen').style.display!=='none') doLogin();
  });
</script>
</body>
</html>