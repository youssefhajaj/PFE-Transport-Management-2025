from scripts.avito_scraper import scrape_avito
from scripts.marocannonces_scraper import scrape_marocannonces
from scripts.moteur_scraper import scrape_moteur
from scripts.kifal_scraper import scrape_kifal
from scripts.utils import save_to_excel
import threading

# Function to scrape Avito
def scrape_avito_thread(total_pages, result):
    result["avito"] = scrape_avito(total_pages=total_pages)

# Function to scrape MarocAnnonces
def scrape_marocannonces_thread(total_pages, result):
    result["marocannonces"] = scrape_marocannonces(total_pages=total_pages)

# Function to scrape Moteur
def scrape_moteur_thread(total_pages, result):
    result["moteur"] = scrape_moteur(total_pages=total_pages)

# Function to scrape Kifal
def scrape_kifal_thread(total_pages, result):
    result["kifal"] = scrape_kifal(total_pages=total_pages)

if __name__ == "__main__":
    total_pages = 30  # Change this as needed

    # Dictionary to store results from threads
    result = {}

    # Create threads for each scraper
    avito_thread = threading.Thread(target=scrape_avito_thread, args=(total_pages, result))
    marocannonces_thread = threading.Thread(target=scrape_marocannonces_thread, args=(total_pages, result))
    moteur_thread = threading.Thread(target=scrape_moteur_thread, args=(total_pages, result))
    kifal_thread = threading.Thread(target=scrape_kifal_thread, args=(total_pages, result))  # Added Kifal thread

    # Start the threads
    avito_thread.start()
    marocannonces_thread.start()
    moteur_thread.start()
    kifal_thread.start()  # Start Kifal thread

    # Wait for all threads to finish
    avito_thread.join()
    marocannonces_thread.join()
    moteur_thread.join()
    kifal_thread.join()  # Wait for Kifal thread

    # Combine the results
    avito_offers = result.get("avito", [])
    marocannonces_offers = result.get("marocannonces", [])
    moteur_offers = result.get("moteur", [])
    kifal_offers = result.get("kifal", [])  # Get Kifal offers
    all_offers = avito_offers + marocannonces_offers + moteur_offers + kifal_offers  # Combine all offers

    # Save the combined data to a single Excel file
    save_to_excel(all_offers)

    # Print a summary
    print(f"Scraping terminé. {len(avito_offers)} offres d'Avito, {len(marocannonces_offers)} offres de MarocAnnonces, {len(moteur_offers)} offres de Moteur et {len(kifal_offers)} offres de Kifal ont été combinées.")
    print(f"Un total de {len(all_offers)} offres ont été enregistrées")
