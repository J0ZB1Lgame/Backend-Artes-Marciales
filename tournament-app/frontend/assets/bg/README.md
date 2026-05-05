# Fondo del Login — Budokai System

Coloca aquí tu imagen o video de fondo para la pantalla de login.

## Imagen de fondo
- Nombre esperado: `login-bg.jpg` (también acepta `.png` o `.webp`)
- Resolución recomendada: 1920×1080 px o superior
- El archivo debe llamarse exactamente `login-bg.jpg` para que el CSS lo tome automáticamente.
  Si usas otro nombre, actualiza la variable `--bg-image` en `login.css`.

## Video de fondo (animación corta)
- Nombre esperado: `login-bg.mp4` (también acepta `.webm`)
- Duración recomendada: 10–30 segundos en loop
- Resolución recomendada: 1920×1080 px
- Para activar el video en lugar de la imagen, cambia en `login.html` el comentario
  que indica `<!-- VIDEO BG -->` y descomenta el bloque `<video>`.

## Notas
- Si no hay ningún archivo aquí, el fondo mostrará un gradiente oscuro por defecto.
- Los archivos de esta carpeta están en `.gitignore` para no subir assets pesados al repo.
  Cada desarrollador debe colocar su propio archivo localmente.
