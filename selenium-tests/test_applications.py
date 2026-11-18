import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


BASE_URL = "http://localhost:8000"


@pytest.fixture
def driver():
    d = webdriver.Chrome()
    d.maximize_window()
    yield d
    d.quit()


def test_access_applications_page(driver):
    driver.get(f"{BASE_URL}/applications")
    WebDriverWait(driver, 10).until(
        lambda d: d.current_url.startswith(BASE_URL)
    )
    assert "/applications" in driver.current_url or "/login" in driver.current_url or "/jobs" in driver.current_url


def test_access_lamaran_alias(driver):
    driver.get(f"{BASE_URL}/lamaran")
    WebDriverWait(driver, 10).until(
        lambda d: d.current_url.startswith(BASE_URL)
    )
    assert "/lamaran" in driver.current_url or "/login" in driver.current_url or "/jobs" in driver.current_url


def test_error_500_when_opening_applications(driver):
    driver.get(f"{BASE_URL}/applications")
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.TAG_NAME, "body"))
    )
    body_text = driver.find_element(By.TAG_NAME, "body").text
    assert (
        "SQLSTATE" in body_text
        or "job_applications" in body_text
        or "Server Error" in body_text
        or "General error" in body_text
    )