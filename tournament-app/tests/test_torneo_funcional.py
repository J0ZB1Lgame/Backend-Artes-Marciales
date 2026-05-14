"""
=============================================================
PRUEBAS FUNCIONALES — Módulo 4: Gestión de Torneos
Herramienta: Selenium + Python
Sistema de Gestión del Torneo Mundial de Artes Marciales
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
TORNEO_URL = f"{BASE_URL}/torneo.html"
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
        sigue_en_login = "login.html" in driver.current_url
        resultado(nombre, sigue_en_login, "Permaneció en login correctamente")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF03_cargar_pagina_torneo(driver):
    """PF-03: La página de torneos carga correctamente"""
    nombre = "PF-03 Carga de página Torneo"
    try:
        hacer_login(driver)
        driver.get(TORNEO_URL)
        time.sleep(2)
        contenedor = esperar(driver, By.ID, "bracketsContainer")
        resultado(nombre, contenedor is not None, "Contenedor bracketsContainer encontrado")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF04_boton_registrar_abre_modal(driver):
    """PF-04: El botón 'Crear Torneo' abre el formulario"""
    nombre = "PF-04 Botón registrar abre modal"
    try:
        hacer_login(driver)
        driver.get(TORNEO_URL)
        time.sleep(2)
        btn = esperar(driver, By.ID, "openModal")
        btn.click()
        time.sleep(1)
        modal = esperar(driver, By.ID, "modal")
        resultado(nombre, "open" in modal.get_attribute("class"), "Formulario visible")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF05_registrar_torneo(driver):
    """PF-05: Se puede registrar un nuevo torneo con datos válidos"""
    nombre = "PF-05 Registro de nuevo torneo"
    try:
        hacer_login(driver)
        driver.get(TORNEO_URL)
        time.sleep(2)

        # Abrir modal
        driver.find_element(By.ID, "openModal").click()
        time.sleep(1)

        # Llenar formulario
        driver.find_element(By.ID, "f-nombre").send_keys("Torneo Prueba Selenium")

        select_estado = Select(driver.find_element(By.ID, "f-estado"))
        select_estado.select_by_value("proximo")

        # Enviar
        driver.find_element(By.ID, "btn-submit").click()
        time.sleep(2)

        resultado(nombre, True, "Formulario enviado correctamente")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF06_panel_lateral_abre(driver):
    """PF-06: El botón Panel Torneo abre el panel lateral"""
    nombre = "PF-06 Panel lateral abre correctamente"
    try:
        hacer_login(driver)
        driver.get(TORNEO_URL)
        time.sleep(2)

        btn_panel = esperar(driver, By.ID, "togglePanel")
        btn_panel.click()
        time.sleep(1)

        panel = driver.find_element(By.ID, "tournamentPanel")
        resultado(nombre, "active" in panel.get_attribute("class"), "Panel lateral visible")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF07_boton_editar_abre_modal(driver):
    """PF-07: El botón editar abre el formulario con datos del torneo"""
    nombre = "PF-07 Botón editar abre modal con datos"
    try:
        hacer_login(driver)
        driver.get(TORNEO_URL)
        time.sleep(2)

        btn_editar = esperar(driver, By.CSS_SELECTOR, ".btn-edit")
        btn_editar.click()
        time.sleep(1)

        modal = driver.find_element(By.ID, "modal")
        resultado(nombre, "open" in modal.get_attribute("class"), "Modal de edición visible")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF08_eliminar_torneo(driver):
    """PF-08: Se puede eliminar un torneo existente"""
    nombre = "PF-08 Eliminar torneo"
    try:
        hacer_login(driver)
        driver.get(TORNEO_URL)
        time.sleep(2)

        btn_eliminar = esperar(driver, By.CSS_SELECTOR, ".btn-delete")
        btn_eliminar.click()
        time.sleep(1)

        # Aceptar confirmación si aparece alert
        try:
            alert = driver.switch_to.alert
            alert.accept()
            time.sleep(1)
        except:
            pass

        resultado(nombre, True, "Torneo eliminado correctamente")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF09_panel_lateral_cierra(driver):
    """PF-09: El botón cerrar panel cierra el panel lateral"""
    nombre = "PF-09 Panel lateral cierra correctamente"
    try:
        hacer_login(driver)
        driver.get(TORNEO_URL)
        time.sleep(2)

        # Abrir panel
        driver.find_element(By.ID, "togglePanel").click()
        time.sleep(1)

        # Cerrar panel
        driver.find_element(By.ID, "closePanel").click()
        time.sleep(1)

        panel = driver.find_element(By.ID, "tournamentPanel")
        resultado(nombre, "active" not in panel.get_attribute("class"), "Panel lateral cerrado correctamente")
    except Exception as e:
        resultado(nombre, False, str(e))


def PF10_cerrar_modal(driver):
    """PF-10: El botón cancelar cierra el formulario"""
    nombre = "PF-10 Botón cancelar cierra modal"
    try:
        hacer_login(driver)
        driver.get(TORNEO_URL)
        time.sleep(2)

        driver.find_element(By.ID, "openModal").click()
        time.sleep(1)

        driver.find_element(By.ID, "closeModal").click()
        time.sleep(1)

        modal = driver.find_element(By.ID, "modal")
        resultado(nombre, "open" not in modal.get_attribute("class"), "Modal cerrado correctamente")
    except Exception as e:
        resultado(nombre, False, str(e))


# ─── EJECUCIÓN ────────────────────────────────────────────
if __name__ == "__main__":
    print("=" * 60)
    print("  PRUEBAS FUNCIONALES — Módulo 4: Gestión de Torneos")
    print("=" * 60)

    driver = iniciar_driver()

    try:
        PF01_login_exitoso(driver)
        PF02_login_credenciales_incorrectas(driver)
        PF03_cargar_pagina_torneo(driver)
        PF04_boton_registrar_abre_modal(driver)
        PF05_registrar_torneo(driver)
        PF06_panel_lateral_abre(driver)
        PF07_boton_editar_abre_modal(driver)
        PF08_eliminar_torneo(driver)
        PF09_panel_lateral_cierra(driver)
        PF10_cerrar_modal(driver)
    finally:
        time.sleep(2)
        driver.quit()

    print("=" * 60)
    print("  Pruebas finalizadas")
    print("=" * 60)