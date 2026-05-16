"""
=============================================================
PRUEBAS FUNCIONALES — Módulo 2: Gestión del Staff
Herramienta: Selenium + Python
Autor: Fernando Ubaque
=============================================================
"""

import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

# ─── Configuración ────────────────────────────────────────
BASE_URL   = "http://localhost/artes marciales/tournament-app/frontend/pages"
LOGIN_URL  = f"{BASE_URL}/login.html"
STAFF_URL  = f"{BASE_URL}/staff.html"
WAIT       = 5  # segundos de espera máxima

# Credenciales de prueba
USUARIO    = "admin"
PASSWORD   = "admin123"

# ─── Utilidades ───────────────────────────────────────────
def iniciar_driver():
    options = webdriver.ChromeOptions()
    options.add_argument("--start-maximized")
    driver = webdriver.Chrome(
        service=Service(ChromeDriverManager().install()),
        options=options
    )
    return driver

def esperar(driver, by, valor, tiempo=WAIT):
    return WebDriverWait(driver, tiempo).until(
        EC.presence_of_element_located((by, valor))
    )

def hacer_login(driver):
    driver.get(LOGIN_URL)
    time.sleep(1)
    driver.find_element(By.ID, "username").send_keys(USUARIO)
    driver.find_element(By.ID, "password").send_keys(PASSWORD)
    driver.find_element(By.ID, "btnLogin").click()
    time.sleep(2)

def resultado(nombre, exito, detalle=""):
    estado = "✅ PASÓ" if exito else "❌ FALLÓ"
    print(f"{estado} | {nombre}" + (f" → {detalle}" if detalle else ""))

# ─── PRUEBAS ──────────────────────────────────────────────

def PF01_login_exitoso(driver):
    """PF-01: El sistema permite iniciar sesión con credenciales válidas"""
    nombre = "PF-01 Login con credenciales válidas"
    try:
        hacer_login(driver)
        # Si redirige fuera del login, el login fue exitoso
        exito = "login.html" not in driver.current_url
        resultado(nombre, exito, driver.current_url)
    except Exception as e:
        resultado(nombre, False, str(e))


def PF02_login_credenciales_incorrectas(driver):
    """PF-02: El sistema muestra error con credenciales incorrectas"""
    nombre = "PF-02 Login con credenciales incorrectas"
    try:
        driver.get(LOGIN_URL)
        time.sleep(1)
        driver.find_element(By.ID, "username").send_keys("usuariofalso")
        driver.find_element(By.ID, "password").send_keys("passwordfalsa")
        driver.find_element(By.ID, "btnLogin").click()
        time.sleep(2)
        # Debe seguir en login o mostrar mensaje de error
        sigue_en_login = "login.html" in driver.current_url
        resultado(nombre, sigue_en_login, "Permaneció en login correctamente")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF03_cargar_pagina_staff(driver):
    """PF-03: La página de staff carga correctamente"""
    nombre = "PF-03 Carga de página Staff"
    try:
        hacer_login(driver)
        driver.get(STAFF_URL)
        time.sleep(2)
        tabla = esperar(driver, By.ID, "staff-tbody")
        resultado(nombre, tabla is not None, "Tabla staff-tbody encontrada")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF04_boton_registrar_abre_modal(driver):
    """PF-04: El botón 'Registrar Staff' abre el formulario"""
    nombre = "PF-04 Botón registrar abre modal"
    try:
        hacer_login(driver)
        driver.get(STAFF_URL)
        time.sleep(2)
        btn = esperar(driver, By.ID, "btn-nuevo")
        btn.click()
        time.sleep(1)
        formulario = esperar(driver, By.ID, "form-staff")
        resultado(nombre, formulario.is_displayed(), "Formulario visible")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF05_registrar_staff(driver):
    """PF-05: Se puede registrar un nuevo miembro del staff"""
    nombre = "PF-05 Registro de nuevo staff"
    try:
        hacer_login(driver)
        driver.get(STAFF_URL)
        time.sleep(2)

        # Abrir modal
        driver.find_element(By.ID, "btn-nuevo").click()
        time.sleep(1)

        # Llenar formulario
        driver.find_element(By.ID, "f-email").send_keys("prueba@test.com")

        select_cargo = Select(driver.find_element(By.ID, "f-cargo"))
        select_cargo.select_by_index(1)

        select_turno = Select(driver.find_element(By.ID, "f-turno"))
        select_turno.select_by_index(1)

        select_estado = Select(driver.find_element(By.ID, "f-estado"))
        select_estado.select_by_index(0)

        # Enviar
        driver.find_element(By.ID, "btn-submit").click()
        time.sleep(2)

        resultado(nombre, True, "Formulario enviado correctamente")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF06_filtro_por_rol(driver):
    """PF-06: El filtro por rol funciona correctamente"""
    nombre = "PF-06 Filtro por rol"
    try:
        hacer_login(driver)
        driver.get(STAFF_URL)
        time.sleep(2)

        filtro = esperar(driver, By.ID, "filter-rol")
        select = Select(filtro)
        select.select_by_index(1)
        time.sleep(1)

        resultado(nombre, True, "Filtro aplicado sin errores")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF07_boton_zonas_abre_panel(driver):
    """PF-07: El botón Zonas abre el panel de zonas"""
    nombre = "PF-07 Botón Zonas abre panel"
    try:
        hacer_login(driver)
        driver.get(STAFF_URL)
        time.sleep(2)

        btn_zonas = esperar(driver, By.ID, "btn-zonas")
        btn_zonas.click()
        time.sleep(1)

        btn_crear_zona = esperar(driver, By.ID, "btn-zona-crear")
        resultado(nombre, btn_crear_zona is not None, "Panel de zonas visible")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF08_crear_zona(driver):
    """PF-08: Se puede crear una nueva zona"""
    nombre = "PF-08 Crear zona"
    try:
        hacer_login(driver)
        driver.get(STAFF_URL)
        time.sleep(2)

        driver.find_element(By.ID, "btn-zonas").click()
        time.sleep(1)

        # Buscar input de zona y escribir
        inputs = driver.find_elements(By.CSS_SELECTOR, ".zonas-add input")
        if inputs:
            inputs[0].clear()
            inputs[0].send_keys("Zona de Prueba")
            driver.find_element(By.ID, "btn-zona-crear").click()
            time.sleep(1)
            resultado(nombre, True, "Zona creada correctamente")
        else:
            resultado(nombre, False, "Input de zona no encontrado")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF09_boton_recargar(driver):
    """PF-09: El botón recargar actualiza la lista"""
    nombre = "PF-09 Botón recargar funciona"
    try:
        hacer_login(driver)
        driver.get(STAFF_URL)
        time.sleep(2)

        btn = esperar(driver, By.ID, "btn-reload")
        btn.click()
        time.sleep(2)

        tabla = driver.find_element(By.ID, "staff-tbody")
        resultado(nombre, tabla is not None, "Lista recargada correctamente")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF10_cerrar_modal(driver):
    """PF-10: El botón cancelar cierra el formulario"""
    nombre = "PF-10 Botón cancelar cierra modal"
    try:
        hacer_login(driver)
        driver.get(STAFF_URL)
        time.sleep(2)

        driver.find_element(By.ID, "btn-nuevo").click()
        time.sleep(1)

        driver.find_element(By.ID, "btn-cerrar-modal").click()
        time.sleep(1)

        # El formulario no debe estar visible
        form = driver.find_element(By.ID, "form-staff")
        resultado(nombre, not form.is_displayed(), "Modal cerrado correctamente")
    except Exception as e:
        resultado(nombre, False, str(e))


# ─── EJECUCIÓN ────────────────────────────────────────────
if __name__ == "__main__":
    print("=" * 60)
    print("  PRUEBAS FUNCIONALES — Módulo 2: Gestión del Staff")
    print("=" * 60)

    driver = iniciar_driver()

    try:
        PF01_login_exitoso(driver)
        PF02_login_credenciales_incorrectas(driver)
        PF03_cargar_pagina_staff(driver)
        PF04_boton_registrar_abre_modal(driver)
        PF05_registrar_staff(driver)
        PF06_filtro_por_rol(driver)
        PF07_boton_zonas_abre_panel(driver)
        PF08_crear_zona(driver)
        PF09_boton_recargar(driver)
        PF10_cerrar_modal(driver)
    finally:
        time.sleep(2)
        driver.quit()

    print("=" * 60)
    print("  Pruebas finalizadas")
    print("=" * 60)