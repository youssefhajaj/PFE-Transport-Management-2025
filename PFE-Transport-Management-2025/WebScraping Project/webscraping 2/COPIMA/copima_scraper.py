import os
import signal
import sys
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import csv
import re
from bs4 import BeautifulSoup

scraped_data = []  # Store scraped data
driver = None  # Global driver variable


def save_data_to_csv(filename="combined_data.csv"):
    """Save scraped data to CSV file."""
    global scraped_data
    if scraped_data:
        # Check if file exists and has content to determine if header is needed
        file_exists = os.path.exists(filename) and os.path.getsize(filename) > 0

        with open(filename, "a", newline="", encoding="utf-8") as file:
            writer = csv.writer(file)
            if not file_exists:
                writer.writerow([
                    "providerNum", "productRef", "productDesignation", "productPrice",
                    "productProReduc", "productCltReduc", "productQty",
                    "productAlert", "productDescription"
                ])
            writer.writerows(scraped_data)
        print(f"✓ Data appended to {filename}")
        scraped_data = []

def signal_handler(sig, frame):
    """Handle Ctrl+C (SIGINT) and save the scraped data before exiting."""
    print("\n🔴 Stopping the scraper... Saving data.")
    save_data_to_csv()
    if driver:
        driver.quit()
    sys.exit(0)

signal.signal(signal.SIGINT, signal_handler)

def wait_for_listings(driver, timeout=30):
    """Wait until more than 20 listings are loaded."""
    try:
        WebDriverWait(driver, timeout).until(
            lambda d: len(d.find_elements(By.CSS_SELECTOR, ".MuiGrid-root.MuiGrid-item")) > 20
        )
        time.sleep(6)  # Let elements stabilize
        return True
    except:
        return False

def scroll_until_next_button(driver):
    """Scroll until the 'Next Page' button appears."""
    for _ in range(5):
        try:
            next_button = WebDriverWait(driver, 5).until(
                EC.presence_of_element_located((By.XPATH, "//button[@aria-label='Go to next page']"))
            )
            if "Mui-disabled" in next_button.get_attribute("class"):
                return None
            driver.execute_script("arguments[0].scrollIntoView();", next_button)
            return next_button
        except:
            driver.execute_script("window.scrollBy(0, 500);")
            time.sleep(1)
    return None

def clean_price(price_text):
    if not price_text:
        return 0

    # Remove non-breaking space (&nbsp; or \xa0), " DH", and spaces for thousands
    price_text = price_text.replace("\xa0", "").replace(" DH", "").replace(" ", "").strip()

    # Extract numeric part, ensuring it captures decimals
    match = re.search(r'\d+,\d+', price_text)  # Matches numbers with a decimal comma
    if not match:
        return 0

    price_text = match.group(0).replace(",", ".")  # Convert comma to dot

    try:
        return round(float(price_text), 2)
    except ValueError:
        return 0


def scrape_listings(driver):
    """Scrape product listings and store data, only for available products."""
    global scraped_data
    html = driver.page_source
    soup = BeautifulSoup(html, "html.parser")
    listings = soup.select(".MuiGrid-root.MuiGrid-item")

    print(f"Found {len(listings)} listings")
    if not listings:
        return False

    for index, listing in enumerate(listings, start=1):
        try:
            # Check availability first - look for either available or unavailable classes
            available_elem = listing.select_one(".MuiTypography-body1.css-p5i9wd")
            unavailable_elem = listing.select_one(".MuiTypography-body1.css-1fhh3wc")

            # Skip if unavailable element exists or if available element doesn't exist
            if unavailable_elem or not available_elem:
                continue  # Skip to next listing if not available

            def clean_text(elem):
                if elem:
                    text = elem.get_text().replace(u'\xa0', ' ').strip()
                    return re.sub(r'[^a-zA-Z0-9\s]', '', text)  # Keep only letters, numbers, and spaces
                return ""

            ref_elem = listing.select_one(".MuiTypography-body1.css-rklvnv, .MuiTypography-body1.css-18qb0v8")
            reference = ref_elem.get_text().replace("Réf: ", "") if ref_elem else ""

            libelle_elem = listing.select_one(".MuiTypography-body1.css-1z113ii")
            libelle = clean_text(libelle_elem) if libelle_elem else ''

            prix_public_elem = listing.select_one(
                ".MuiTypography-body1.css-1czf31h , .MuiTypography-body1.css-ztr5ft , .MuiTypography-body1.css-19fzyvq")
            prix_public_text = prix_public_elem.get_text(strip=True) if prix_public_elem else ""
            prix_public = clean_price(prix_public_text)

            prix_remise_elem = listing.select_one(
                ".MuiTypography-body1.css-16axz3u , .MuiTypography-body1.css-1w6r5di , .MuiTypography-body1.css-12t8m56")
            prix_remise_text = prix_remise_elem.get_text(strip=True) if prix_remise_elem else ""
            prix_remise = clean_price(prix_remise_text) if prix_remise_text else prix_public

            productProReduc = round(((prix_public - prix_remise) / prix_public) * 100, 2) if prix_public else 0
            providerNum = 9
            productCltReduc = 0
            productQty = 10
            productAlert = 2
            productDescription = libelle

            if any([reference, libelle, prix_public]):
                scraped_data.append(
                    [providerNum, reference, libelle, prix_public, productProReduc, productCltReduc, productQty,
                     productAlert, productDescription])
        except Exception as e:
            print(f"Exception on listing {index}: {e}")
    return True

def scrape_copima():
    """Main scraping function for Copima."""
    global driver
    chrome_options = Options()
    chrome_options.add_argument("--headless")
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")  # Important for Linux
    chrome_options.add_argument("--disable-gpu")
    chrome_options.add_argument("--window-size=1920,1080")

    driver = webdriver.Chrome(options=chrome_options)

    try:
        driver.get("https://edge.copima.ma")
        print("Page loaded")

        WebDriverWait(driver, 20).until(EC.presence_of_element_located((By.ID, "mui-1")))
        driver.find_element(By.ID, "mui-1").send_keys("051063")
        driver.find_element(By.ID, "mui-2").send_keys("i2yCJXnLKSck96")

        button = WebDriverWait(driver, 20).until(
            EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Se connecter')]"))
        )
        button.click()

        WebDriverWait(driver, 20).until(lambda d: d.current_url != "https://edge.copima.ma/login")
        print("Logged in")

        catalog_button = WebDriverWait(driver, 20).until(
            EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Catalogue Produits')]"))
        )
        driver.execute_script("arguments[0].click();", catalog_button)

        WebDriverWait(driver, 20).until(lambda d: "/catalog" in d.current_url)
        print("Navigated to catalog")

        page_num = 1
        while page_num<=500:
            print(f"📄 Scraping Copima Page {page_num}...")

            if not wait_for_listings(driver, timeout=10):
                print(f"⚠️ No listings found on page {page_num}, stopping.")
                break

            if not scrape_listings(driver):
                break

            # Save data after each page
            save_data_to_csv()

            next_button = scroll_until_next_button(driver)
            if not next_button:
                print("✅ No more pages. Scraping complete.")
                break

            driver.execute_script("arguments[0].click();", next_button)
            page_num += 1
            time.sleep(6)

        print("Scraping complete! Saving data...")
        save_data_to_csv()

    except Exception as e:
        print(f"❌ Error during execution: {e}")
        save_data_to_csv()

    finally:
        if driver:
            driver.quit()