"""
Pengujian Otomatis Selenium Python (White-Box Testing / UI Acceptance Test)
Modul: Lowongan Pekerjaan (Penyedia Pekerjaan)
Sistem: bisaKerja

Pemetaan Skenario & Acceptance Criteria:
- AC-01 / TS-01: Menampilkan Halaman & Tabel Lowongan Saya (Jumlah pelamar, status Aktif/Ditutup, tombol aksi)
- AC-02 / TS-02: Menampilkan Formulir Tambah Lowongan (Tombol "+ Tambah Lowongan" memunculkan modal dialog)
- AC-03 / TS-03: Mengisi Judul dan Kategori Pekerjaan (Field Judul & Dropdown Kategori wajib)
- AC-04 / TS-04: Mengisi Lokasi dan Tanggal Pelaksanaan (Field Lokasi wajib & batas minimal tanggal hari ini)
- AC-05 / TS-05: Mengisi Deskripsi dan Instruksi Pekerjaan (Field Catatan & Instruksi Khusus wajib)
- AC-06 / TS-06: Validasi Input Data Wajib (Mencegah pengiriman jika data kosong atau tanggal lampau)
- AC-07 / TS-07: Mengubah Data Lowongan (Halaman Kelola Lowongan dan pembaruan rincian data)
- AC-08 / TS-08: Mengubah Status Lowongan (Menutup lowongan aktif & membuka kembali lowongan yang ditutup)
- AC-09 / TS-09: Menutup Lowongan dengan Dialog Konfirmasi (Konfirmasi window.confirm sebelum penutupan)
"""

import datetime
import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC

BASE_URL = "http://127.0.0.1:8000/pemberi-kerja/lowongan"


@pytest.fixture
def driver():
    """Fixture driver browser Chrome untuk menjalankan setiap test case."""
    options = webdriver.ChromeOptions()
    options.add_argument("--start-maximized")
    options.add_argument("--disable-gpu")
    options.add_argument("--no-sandbox")
    # Uncomment baris berikut jika ingin menjalankan tanpa membuka jendela browser visual:
    # options.add_argument("--headless=new")

    d = webdriver.Chrome(options=options)
    d.implicitly_wait(3)
    d.get(BASE_URL)

    # Sinkronkan status akun aktif ke sessionStorage jika diperlukan oleh UI JavaScript
    d.execute_script("sessionStorage.setItem('user', JSON.stringify({status_akun: 'aktif'}));")

    yield d
    d.quit()


def buka_modal_tambah(driver):
    """Helper untuk membuka modal dialog Tambah Lowongan."""
    driver.get(BASE_URL)
    btn_tambah = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.ID, "btn-create-vacancy"))
    )
    btn_tambah.click()
    dialog = WebDriverWait(driver, 10).until(
        EC.visibility_of_element_located((By.ID, "create-vacancy-dialog"))
    )
    return dialog


def tutup_modal(driver):
    """Helper untuk menutup modal dialog."""
    close_btns = driver.find_elements(By.CSS_SELECTOR, "[data-dialog-close]")
    for btn in close_btns:
        if btn.is_displayed():
            btn.click()
            break


def isi_formulir(driver, judul="Tukang Bersih Kebun", kategori_id="1", lokasi="Jl. Sukajadi No. 10",
                 upah="150000", waktu_tipe="tomorrow", deskripsi="Membersihkan rumput halaman rumah."):
    """Helper mengisi seluruh field pada form tambah lowongan."""
    if judul is not None:
        el_judul = driver.find_element(By.ID, "judul")
        el_judul.clear()
        el_judul.send_keys(judul)

    if kategori_id is not None:
        el_kategori = Select(driver.find_element(By.ID, "id_kategori"))
        el_kategori.select_by_value(kategori_id)

    if lokasi is not None:
        el_lokasi = driver.find_element(By.ID, "lokasi")
        el_lokasi.clear()
        el_lokasi.send_keys(lokasi)

    if upah is not None:
        el_upah = driver.find_element(By.ID, "upah")
        el_upah.clear()
        el_upah.send_keys(upah)

    today = datetime.date.today()
    if waktu_tipe == "today":
        tanggal = today.strftime("%Y-%m-%d")
    elif waktu_tipe == "tomorrow":
        tanggal = (today + datetime.timedelta(days=1)).strftime("%Y-%m-%d")
    elif waktu_tipe == "yesterday":
        tanggal = (today - datetime.timedelta(days=1)).strftime("%Y-%m-%d")
    else:
        tanggal = waktu_tipe

    if tanggal:
        el_waktu = driver.find_element(By.ID, "waktu_pekerjaan")
        driver.execute_script("arguments[0].value = arguments[1];", el_waktu, tanggal)

    if deskripsi is not None:
        el_deskripsi = driver.find_element(By.ID, "deskripsi")
        el_deskripsi.clear()
        el_deskripsi.send_keys(deskripsi)


def submit_modal_tambah(driver):
    """Helper klik tombol submit Pasang Lowongan."""
    btn_submit = driver.find_element(
        By.XPATH, "//dialog[@id='create-vacancy-dialog']//button[@type='submit' and contains(., 'Pasang Lowongan')]"
    )
    btn_submit.click()


def pastikan_lowongan_aktif_tersedia(driver):
    """Helper memastikan ada lowongan aktif yang siap diedit/dikelola."""
    driver.get(BASE_URL)
    kelola_links = driver.find_elements(By.XPATH, "//table//a[contains(., 'Kelola')]")
    if not kelola_links:
        buka_modal_tambah(driver)
        isi_formulir(driver, judul="Lowongan Prasyarat Testing", upah="120000", waktu_tipe="tomorrow")
        submit_modal_tambah(driver)
        WebDriverWait(driver, 10).until(
            EC.visibility_of_element_located((By.XPATH, "//*[@role='status']"))
        )


# ==============================================================================
# TEST CASES: AC-01 s.d AC-09
# ==============================================================================

def test_ac01_menampilkan_daftar_lowongan(driver):
    """
    AC-01 / TS-01: Menampilkan Halaman & Daftar Lowongan Saya.
    Sistem harus menampilkan halaman Lowongan Saya yang memuat daftar lowongan yang telah
    dibuat oleh penyedia pekerjaan, lengkap dengan jumlah pelamar, status (Aktif/Ditutup),
    dan tombol aksi.
    """
    driver.get(BASE_URL)

    # 1. Verifikasi judul halaman / heading
    heading = WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.XPATH, "//h2[contains(., 'Lowongan Saya')]"))
    )
    assert heading.is_displayed(), "Heading 'Lowongan Saya' harus tampil."

    # 2. Verifikasi tabel daftar lowongan
    tabel = driver.find_element(By.TAG_NAME, "table")
    assert tabel.is_displayed(), "Tabel lowongan harus tampil."

    # 3. Verifikasi kolom header tabel: Pekerjaan, Jumlah pelamar, Status, Aksi
    header_text = driver.find_element(By.TAG_NAME, "thead").text
    assert "Pekerjaan" in header_text
    assert "Jumlah pelamar" in header_text
    assert "Status" in header_text
    assert "Aksi" in header_text


def test_ac02_menampilkan_formulir_tambah_lowongan(driver):
    """
    AC-02 / TS-02: Menampilkan Formulir Tambah Lowongan.
    Sistem harus menyediakan tombol "+ Tambah Lowongan" yang memunculkan jendela modal/dialog
    formulir pemasangan lowongan pekerjaan baru.
    """
    dialog = buka_modal_tambah(driver)
    assert dialog.is_displayed(), "Modal dialog create-vacancy-dialog harus terbuka."

    # Verifikasi judul modal
    modal_title = dialog.find_element(By.XPATH, ".//h2[contains(., 'Tambah Lowongan Pekerjaan')]")
    assert modal_title.is_displayed()

    # Verifikasi tombol tutup / batal
    tutup_modal(driver)


def test_ac03_mengisi_judul_dan_kategori_pekerjaan(driver):
    """
    AC-03 / TS-03: Mengisi Judul dan Kategori Pekerjaan.
    Sistem harus menyediakan field Judul Pekerjaan (wajib) dan pilihan dropdown Kategori Pekerjaan
    (wajib) yang dapat dipilih oleh penyedia pekerjaan.
    """
    dialog = buka_modal_tambah(driver)

    # 1. Field Judul Pekerjaan (wajib)
    el_judul = dialog.find_element(By.ID, "judul")
    assert el_judul.get_attribute("required") is not None, "Field Judul harus memiliki atribut required."
    el_judul.send_keys("Asisten Rumah Tangga Harian")
    assert el_judul.get_attribute("value") == "Asisten Rumah Tangga Harian"

    # 2. Dropdown Kategori Pekerjaan (wajib)
    el_kategori = dialog.find_element(By.ID, "id_kategori")
    assert el_kategori.get_attribute("required") is not None, "Dropdown Kategori harus memiliki atribut required."

    select_kategori = Select(el_kategori)
    options = [opt.text for opt in select_kategori.options]
    assert len(options) > 1, "Dropdown kategori harus memiliki opsi pilihan."

    # Pilih salah satu kategori
    select_kategori.select_by_index(1)
    assert select_kategori.first_selected_option.get_attribute("value") != ""

    tutup_modal(driver)


def test_ac04_mengisi_lokasi_dan_tanggal_pelaksanaan(driver):
    """
    AC-04 / TS-04: Mengisi Lokasi dan Tanggal Pelaksanaan.
    Sistem harus menyediakan field Lokasi & Patokan (wajib) serta tanggal pelaksanaan
    pekerjaan dengan batas minimal mulai dari tanggal hari ini.
    """
    dialog = buka_modal_tambah(driver)

    # 1. Field Lokasi & Patokan (wajib)
    el_lokasi = dialog.find_element(By.ID, "lokasi")
    assert el_lokasi.get_attribute("required") is not None, "Field Lokasi harus memiliki atribut required."
    el_lokasi.send_keys("Komp. Permata Indah Blok B3")
    assert el_lokasi.get_attribute("value") == "Komp. Permata Indah Blok B3"

    # 2. Field Tanggal Pelaksanaan dengan atribut min = hari ini
    el_waktu = dialog.find_element(By.ID, "waktu_pekerjaan")
    min_date = el_waktu.get_attribute("min")
    today_str = datetime.date.today().strftime("%Y-%m-%d")
    assert min_date == today_str, f"Atribut min harus tanggal hari ini ({today_str}), didapat: {min_date}"

    # Mengisi tanggal valid (besok)
    besok_str = (datetime.date.today() + datetime.timedelta(days=1)).strftime("%Y-%m-%d")
    driver.execute_script("arguments[0].value = arguments[1];", el_waktu, besok_str)
    assert el_waktu.get_attribute("value") == besok_str

    tutup_modal(driver)


def test_ac05_mengisi_deskripsi_dan_instruksi_pekerjaan(driver):
    """
    AC-05 / TS-05: Mengisi Deskripsi dan Instruksi Pekerjaan.
    Sistem harus menyediakan field Catatan & Instruksi Khusus (wajib) untuk menjelaskan
    rincian tugas dan peralatan yang dibutuhkan.
    """
    dialog = buka_modal_tambah(driver)

    el_deskripsi = dialog.find_element(By.ID, "deskripsi")
    assert el_deskripsi.get_attribute("required") is not None, "Field Catatan & Instruksi harus required."

    instruksi_sample = "Membawa lap microfiber dan sapu. Diutamakan tiba pukul 08.00 pagi."
    el_deskripsi.send_keys(instruksi_sample)
    assert el_deskripsi.get_attribute("value") == instruksi_sample

    tutup_modal(driver)


def test_ac06_validasi_input_data_wajib(driver):
    """
    AC-06 / TS-06: Validasi Input Data Wajib.
    Sistem harus memastikan seluruh field yang ditandai bintang (*) telah terisi sebelum
    formulir dapat dikirim, serta mencegah pengiriman jika terdapat data wajib yang kosong
    atau tanggal lampau.
    """
    dialog = buka_modal_tambah(driver)

    # Uji 1: Submit dengan Judul kosong -> form dicegah terkirim
    isi_formulir(driver, judul="", lokasi="Jl. Bandung", upah="100000", waktu_tipe="tomorrow", deskripsi="Instruksi")
    submit_modal_tambah(driver)

    el_judul = dialog.find_element(By.ID, "judul")
    # HTML5 validation: field required yang kosong akan mengembalikan valid=false
    is_valid = driver.execute_script("return arguments[0].validity.valid;", el_judul)
    assert not is_valid, "Input judul kosong harus membuat form tidak valid (HTML5 validation)."
    assert dialog.is_displayed(), "Modal harus tetap terbuka saat input wajib kosong."

    # Uji 2: Tanggal lampau (kemarin) -> rangeUnderflow aktif
    kemarin_str = (datetime.date.today() - datetime.timedelta(days=1)).strftime("%Y-%m-%d")
    el_waktu = dialog.find_element(By.ID, "waktu_pekerjaan")
    driver.execute_script("arguments[0].value = arguments[1];", el_waktu, kemarin_str)

    is_range_underflow = driver.execute_script("return arguments[0].validity.rangeUnderflow;", el_waktu)
    assert is_range_underflow, "Tanggal lampau harus menyebabkan rangeUnderflow pada input date."

    tutup_modal(driver)


def test_ac07_mengubah_data_lowongan(driver):
    """
    AC-07 / TS-07: Mengubah Data Lowongan.
    Sistem harus menyediakan halaman Kelola Lowongan yang memungkinkan penyedia pekerjaan
    memperbarui rincian informasi lowongan yang telah dibuat.
    """
    pastikan_lowongan_aktif_tersedia(driver)

    # 1. Klik aksi "Kelola" pada lowongan aktif pertama
    kelola_btn = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.XPATH, "//table//a[contains(., 'Kelola')]"))
    )
    kelola_btn.click()

    # 2. Verifikasi berada pada halaman Kelola Lowongan
    h1 = WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.XPATH, "//h1[contains(., 'Kelola Lowongan')]"))
    )
    assert h1.is_displayed()

    # 3. Ubah rincian deskripsi dan pastikan upah bulat
    el_upah = driver.find_element(By.ID, "upah")
    el_upah.clear()
    el_upah.send_keys("150000")

    el_deskripsi = driver.find_element(By.ID, "deskripsi")
    el_deskripsi.clear()
    teks_baru = f"Rincian diperbarui otomatis pada {datetime.datetime.now().strftime('%H:%M:%S')}"
    el_deskripsi.send_keys(teks_baru)

    # 4. Simpan perubahan
    btn_simpan = driver.find_element(By.XPATH, "//button[contains(., 'Simpan Perubahan')]")
    driver.execute_script("arguments[0].scrollIntoView(true);", btn_simpan)
    btn_simpan.click()

    # 5. Verifikasi kembali ke halaman daftar lowongan dengan alert sukses
    pesan_sukses = WebDriverWait(driver, 10).until(
        EC.visibility_of_element_located((By.XPATH, "//*[@role='status']"))
    ).text
    assert "berhasil diperbarui" in pesan_sukses.lower()


def test_ac08_mengubah_status_lowongan(driver):
    """
    AC-08 / TS-08: Mengubah Status Lowongan.
    Sistem harus memungkinkan penyedia pekerjaan menutup lowongan yang aktif agar tidak
    menerima pelamar baru, serta dapat membuka kembali lowongan yang telah ditutup.
    """
    pastikan_lowongan_aktif_tersedia(driver)

    # --- Bagian 1: Menutup lowongan aktif dari halaman Kelola ---
    kelola_btn = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.XPATH, "//table//a[contains(., 'Kelola')]"))
    )
    kelola_btn.click()

    btn_tutup = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.XPATH, "//form[contains(@action, '/status')]//button[contains(., 'Tutup Lowongan')]"))
    )
    btn_tutup.click()

    pesan_tutup = WebDriverWait(driver, 10).until(
        EC.visibility_of_element_located((By.XPATH, "//*[@role='status']"))
    ).text
    assert "ditutup" in pesan_tutup.lower()

    # --- Bagian 2: Membuka kembali lowongan yang ditutup dari tabel ---
    driver.get(BASE_URL)
    btn_buka = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.XPATH, "//table//button[contains(., 'Buka kembali')]"))
    )
    btn_buka.click()

    pesan_buka = WebDriverWait(driver, 10).until(
        EC.visibility_of_element_located((By.XPATH, "//*[@role='status']"))
    ).text
    assert "dibuka kembali" in pesan_buka.lower()


def test_ac09_menutup_lowongan_pekerjaan_dengan_konfirmasi(driver):
    """
    AC-09 / TS-09: Menutup Lowongan Pekerjaan dengan Dialog Konfirmasi.
    Sistem harus menyediakan tombol menutup lowongan yang dilengkapi dengan konfirmasi dialog
    sebelum aksi menutup diproses.
    """
    pastikan_lowongan_aktif_tersedia(driver)

    # 1. Buka halaman Kelola
    kelola_btn = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.XPATH, "//table//a[contains(., 'Kelola')]"))
    )
    kelola_btn.click()

    # 2. Cari tombol penutupan lowongan yang dilengkapi konfirmasi dialog (data-confirm)
    form_confirm = WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.XPATH, "//form[@method='POST' and @data-confirm]"))
    )
    assert form_confirm.get_attribute("data-confirm") is not None, "Form harus memiliki atribut data-confirm."

    btn_submit = form_confirm.find_element(By.XPATH, ".//button[@type='submit']")
    btn_submit.click()

    # 3. Tangani dialog konfirmasi browser (window.confirm)
    WebDriverWait(driver, 5).until(EC.alert_is_present())
    alert = driver.switch_to.alert
    teks_alert = alert.text
    assert len(teks_alert) > 0, "Dialog konfirmasi harus memiliki pesan peringatan."

    # Setujui / OK pada dialog konfirmasi
    alert.accept()

    # 4. Verifikasi aksi berhasil diproses dan muncul notifikasi sukses
    pesan_sukses = WebDriverWait(driver, 10).until(
        EC.visibility_of_element_located((By.XPATH, "//*[@role='status']"))
    ).text
    assert "ditutup" in pesan_sukses.lower() or "berhasil" in pesan_sukses.lower()
