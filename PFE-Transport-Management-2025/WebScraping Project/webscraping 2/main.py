import threading
import csv
import signal
import sys
import os
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.application import MIMEApplication

from FORAMAG.foramag_scraper import (
    login_foramag,
    scrape_promotion,
    scrape_nouvelle_arrivage,
    save_to_csv as save_foramag_to_csv,
    close_browser as close_foramag_browser
)
from COPIMA.copima_scraper import scrape_copima, save_data_to_csv as save_copima_to_csv
import threading

csv_lock = threading.Lock()

# Global email variables
EMAIL_USER = "youssef.hajaj111@gmail.com"
EMAIL_PASS = "aljn ziwo gnfd vtsw"
EMAIL_RECEIVER = "contactkounhany@gmail.com"
#EMAIL_RECEIVER = "youssef.hajaj111@gmail.com"

# Global data
foramag_data = []
copima_data = []
CSV_FILE = "combined_data.csv"

# Send email with attachment
def send_confirmation_email(subject="✅ Scraping Completed Piece de recahnge", message="The data has been saved.", attachment_path=CSV_FILE):
    if not all([EMAIL_USER, EMAIL_PASS, EMAIL_RECEIVER]):
        print("⚠️ Missing email config.")
        return

    recipients = [email.strip() for email in EMAIL_RECEIVER.split(",")]

    msg = MIMEMultipart()
    msg["From"] = EMAIL_USER
    msg["To"] = ", ".join(recipients)
    msg["Subject"] = subject
    msg.attach(MIMEText(message, "plain"))

    # Attach CSV
    try:
        with open(attachment_path, "rb") as f:
            part = MIMEApplication(f.read(), Name=os.path.basename(attachment_path))
            part['Content-Disposition'] = f'attachment; filename="{os.path.basename(attachment_path)}"'
            msg.attach(part)
    except Exception as e:
        print(f"❌ Failed to attach CSV: {e}")

    try:
        with smtplib.SMTP("smtp.gmail.com", 587) as server:
            server.starttls()
            server.login(EMAIL_USER, EMAIL_PASS)
            server.sendmail(EMAIL_USER, recipients, msg.as_string())
        print(f"📧 Email sent to: {', '.join(recipients)}")
    except Exception as e:
        print(f"❌ Failed to send email: {e}")

# Handle Ctrl+C
def signal_handler(sig, frame):
    print("\n🛑 Ctrl+C detected! Saving and emailing scraped data...")

    if foramag_data:
        save_foramag_to_csv(foramag_data, filename=CSV_FILE)
        print("✔ Foramag data saved.")

    if copima_data:
        save_copima_to_csv(filename=CSV_FILE)
        print("✔ Copima data saved.")

    close_foramag_browser()

    send_confirmation_email(
        subject="⚠️ Scraping Interrupted",
        message="The scraping process was interrupted but data has been saved."
    )

    sys.exit(0)

signal.signal(signal.SIGINT, signal_handler)

# Clear CSV file
def clear_csv_file(filename=CSV_FILE):
    if os.path.exists(filename):
        print(f"🧹 Clearing file: {filename}")
        with csv_lock:  # Add lock here
            with open(filename, "w", newline="", encoding="utf-8") as file:
                writer = csv.writer(file)
                writer.writerow([
                    "providerNum", "productRef", "productDesignation", "productPrice",
                    "productProReduc", "productCltReduc", "productQty",
                    "productAlert", "productDescription"
                ])

# Foramag scraping
def run_foramag_scraper():
    global foramag_data
    if login_foramag():
        promotions = scrape_promotion()
        nouvelle_arrivage = scrape_nouvelle_arrivage()
        foramag_data = promotions + nouvelle_arrivage
        save_foramag_to_csv(foramag_data, filename=CSV_FILE)
    else:
        print("❌ Foramag login failed!")

# Copima scraping
def run_copima_scraper():
    global copima_data
    print("?? Starting Copima scraper...")
    try:
        scrape_copima()
        print("? Copima scraper finished successfully")
    except Exception as e:
        print(f"? Copima scraper failed: {str(e)}")

# Merge CSVs
def merge_csv_files(source_file, target_file):
    with open(source_file, 'r', encoding='utf-8') as src, open(target_file, 'a', newline='', encoding='utf-8') as tgt:
        reader = csv.reader(src)
        writer = csv.writer(tgt)
        next(reader)  # Skip header
        for row in reader:
            writer.writerow(row)

# --- Main Execution ---
clear_csv_file()

foramag_thread = threading.Thread(target=run_foramag_scraper)
copima_thread = threading.Thread(target=run_copima_scraper)

foramag_thread.start()
copima_thread.start()

foramag_thread.join()
copima_thread.join()

print("✅ Scraping completed and data saved to combined_data.csv")

send_confirmation_email(
    subject="✅ Scraping Finished Successfully - Piece de recahnge - COPIMA & FORAMAG",
    message="The scraping process finished successfully and the data has been saved."
)
