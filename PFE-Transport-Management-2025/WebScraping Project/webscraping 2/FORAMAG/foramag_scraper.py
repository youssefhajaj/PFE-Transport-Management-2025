from webbrowser import Chrome
import threading
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import csv
import re  # For removing special characters from the description
import time


csv_lock = threading.Lock()
# URLs
login_url = "https://pro.foramag.ma/login"
promotion_url = "https://pro.foramag.ma/promotion"
nouvelle_arrivage_url = "https://pro.foramag.ma/nouvelle-arrivage"

# Configure Selenium to run in headless mode
chrome_options = Options()
chrome_options.add_argument("--headless")
chrome_options.add_argument("--no-sandbox")
chrome_options.add_argument("--disable-dev-shm-usage")  # Important for Linux
chrome_options.add_argument("--disable-gpu")
chrome_options.add_argument("--window-size=1920,1080")

# Initialize the WebDriver
driver = webdriver.Chrome(options=chrome_options)

def login_foramag():
    """Logs into the website and returns True if successful."""
    print("Logging in...")
    driver.get(login_url)

    # Wait for the login form to load
    WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "username")))

    # Extract the CSRF token (hidden input field)
    csrf_token = driver.find_element(By.NAME, "_csrf_token").get_attribute("value")

    # Fill in the login form
    driver.find_element(By.NAME, "username").send_keys("USER6400")  # Replace with your username
    driver.find_element(By.NAME, "password").send_keys("USER6400")  # Replace with your password

    # Submit the form
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

    # Wait for the login to complete (check for a redirect or a specific element)
    try:
        WebDriverWait(driver, 10).until(EC.url_changes(login_url))
        print("Login successful!")
        return True
    except Exception as e:
        print(f"Login failed: {e}")
        return False

def save_to_csv(data, filename="combined_data.csv"):
    """Saves extracted data to a CSV file in the new format."""
    with csv_lock:
        print(f"Saving data to {filename}...")
        with open(filename, mode="a", newline="", encoding="utf-8") as file:
            writer = csv.writer(file)
            writer.writerows(data)
        print("Data saved successfully!")
        data.clear()

def scrape_promotion():
    """Fetches the 'Promotion' page and extracts relevant data using Selenium, handling pagination."""
    print("Scraping promotions...")
    driver.get(promotion_url)

    products = []  # Initialize the list to store all products
    while True:
        # Wait until the table is fully loaded
        wait = WebDriverWait(driver, 20)  # Increased timeout to 20 seconds
        table = wait.until(EC.presence_of_element_located((By.ID, 'promotions')))

        # Wait for the table content (rows) to be populated
        wait.until(lambda driver: len(driver.find_elements(By.CSS_SELECTOR, "#promotions tbody tr")) > 0)

        # Re-locate the rows after each page transition
        rows = table.find_elements(By.CSS_SELECTOR, "tbody tr")

        for row in rows:
            try:
                # Re-locate the columns for each row
                columns = row.find_elements(By.TAG_NAME, "td")
                if len(columns) >= 8:  # Ensure there are enough columns
                    # Check if the product is available ("Disponible")
                    disponible_div = columns[5].find_element(By.CSS_SELECTOR, "div.status-pill.secondary")
                    if disponible_div.get_attribute("data-title") == "Disponible":
                        reference = columns[3].text.strip()  # 4th column (Référence)
                        description = columns[4].text.strip()  # 5th column (Description)
                        prix_initial = float(columns[6].text.strip().replace("DH", "").replace(" ", ""))  # 7th column (Initial DH TTC)
                        prix_promo = float(columns[7].text.strip().replace("DH", "").replace(" ", ""))  # 8th column (Promo DH TTC)

                        # Calculate discount percentage (only the number)
                        discount = round(((prix_initial - prix_promo) / prix_initial) * 100, 2)

                        # Remove special characters from the description
                        description_cleaned = re.sub(r"[^a-zA-Z0-9\s]", "", description)

                        # Append the product to the list
                        products.append([11, reference, description_cleaned, prix_initial, discount, 0, 10, 2, description_cleaned])
            except Exception as e:
                # Skip rows where the "Disponible" div is not found or elements are stale
                continue

        print(f"Scraped {len(products)} products so far.")

        # Save data after each page
        save_to_csv(products)

        # Check if there is a "Next" button and if it is clickable
        try:
            next_button = driver.find_element(By.CSS_SELECTOR, "a.paginate_button.next")
            if "disabled" in next_button.get_attribute("class"):
                print("Reached the last page.")
                break  # Exit the loop if there is no next page
            else:
                # Click the "Next" button to load the next page
                next_button.click()
                print("Moving to the next page...")
                # Add a delay to ensure the new content is fully loaded
                time.sleep(6)  # Wait for 5 seconds (adjust as needed)
                # Wait for the first row of the new table to load
                wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "#promotions tbody tr")))
        except Exception as e:
            print(f"Error navigating to the next page: {e}")
            break  # Exit the loop if there is an error

    print(f"Total products scraped: {len(products)}")
    return products

def scrape_nouvelle_arrivage():
    """Fetches the 'Nouvelle Arrivage' page and extracts relevant data using Selenium, handling pagination."""
    print("Scraping nouvelle arrivage...")
    driver.get(nouvelle_arrivage_url)

    products = []  # Initialize the list to store all products
    while True:
        # Wait until the table is fully loaded
        wait = WebDriverWait(driver, 20)  # Increased timeout to 20 seconds
        table = wait.until(EC.presence_of_element_located((By.ID, 'nouvelle-arrivage')))

        # Wait for the table content (rows) to be populated
        wait.until(lambda driver: len(driver.find_elements(By.CSS_SELECTOR, "#nouvelle-arrivage tbody tr")) > 0)

        # Re-locate the rows after each page transition
        rows = table.find_elements(By.CSS_SELECTOR, "tbody tr")

        for row in rows:
            try:
                # Re-locate the columns for each row
                columns = row.find_elements(By.TAG_NAME, "td")
                if len(columns) >= 5:  # Ensure there are enough columns
                    # Check if the product is available ("Disponible")
                    disponible_div = columns[4].find_element(By.CSS_SELECTOR, "div.status-pill.secondary")
                    if disponible_div.get_attribute("data-title") == "Disponible":
                        reference = columns[2].text.strip()  # 4th column (Référence)
                        description = columns[3].text.strip()  # 5th column (Description)
                        prix_initial = float(columns[5].text.strip().replace("DH", "").replace(" ", ""))  # 7th column (Initial DH TTC)
                        prix_promo = prix_initial

                        # Calculate discount percentage (only the number)
                        discount = round(((prix_initial - prix_promo) / prix_initial) * 100, 2)

                        # Remove special characters from the description
                        description_cleaned = re.sub(r"[^a-zA-Z0-9\s]", "", description)

                        # Append the product to the list
                        products.append([11, reference, description_cleaned, prix_initial, discount, 0, 10, 2, description_cleaned])
            except Exception as e:
                # Skip rows where the "Disponible" div is not found or elements are stale
                continue

        print(f"Scraped {len(products)} products so far.")

        # Save data after each page
        save_to_csv(products)

        # Check if there is a "Next" button and if it is clickable
        try:
            next_button = driver.find_element(By.CSS_SELECTOR, "a.paginate_button.next")
            if "disabled" in next_button.get_attribute("class"):
                print("Reached the last page.")
                break  # Exit the loop if there is no next page
            else:
                # Click the "Next" button to load the next page
                next_button.click()
                print("Moving to the next page...")
                # Add a delay to ensure the new content is fully loaded
                time.sleep(8)  # Wait for 5 seconds (adjust as needed)
                # Wait for the first row of the new table to load
                wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "#nouvelle-arrivage tbody tr")))
        except Exception as e:
            print(f"Error navigating to the next page: {e}")
            break  # Exit the loop if there is an error

    print(f"Total products scraped: {len(products)}")
    return products

def close_browser():
    """Closes the browser."""
    print("Closing Foramag browser...")
    driver.quit()