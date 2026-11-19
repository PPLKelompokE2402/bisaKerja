
import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


@pytest.fixture
def driver():
    browser = webdriver.Chrome()
    browser.maximize_window()
    yield browser
    browser.quit()


def test_halaman_profil_tampil(driver):
    driver.get("http://127.0.0.1:8001/profile")

    assert "Profil" in driver.page_source


def test_form_biodata_tersedia(driver):
    driver.get("http://127.0.0.1:8001/profile")

    field_nama = driver.find_element(By.NAME, "nama")
    field_telepon = driver.find_element(By.NAME, "nomor_telepon")
    field_alamat = driver.find_element(By.NAME, "alamat")
    field_keahlian = driver.find_element(By.NAME, "keahlian")
    field_pengalaman = driver.find_element(By.NAME, "pengalaman_kerja")

    assert field_nama.is_displayed()
    assert field_telepon.is_displayed()
    assert field_alamat.is_displayed()
    assert field_keahlian.is_displayed()
    assert field_pengalaman.is_displayed()


def test_nama_wajib_diisi(driver):
    driver.get("http://127.0.0.1:8001/profile")

    field_nama = driver.find_element(By.NAME, "nama")

    field_nama.clear()

    validasi = driver.execute_script(
        "return arguments[0].checkValidity();",
        field_nama
    )

    assert validasi is False


def test_dropdown_jenis_kelamin(driver):
    driver.get("http://127.0.0.1:8001/profile")

    dropdown = driver.find_element(
        By.NAME, "jenis_kelamin"
    )

    assert dropdown.is_displayed()
    assert dropdown.tag_name == "select"


def test_input_upload_cv_tersedia(driver):
    driver.get("http://127.0.0.1:8001/profile")

    input_cv = driver.find_element(By.NAME, "cv")

    assert input_cv.is_displayed()
    assert input_cv.get_attribute("type") == "file"
    assert ".pdf" in input_cv.get_attribute("accept")
