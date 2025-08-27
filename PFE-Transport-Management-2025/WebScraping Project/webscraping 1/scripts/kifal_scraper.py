import requests
from bs4 import BeautifulSoup
import random
import pandas as pd

BASE_URL = "https://occasion.kifal.ma/annonces?page="
WEBSITE_NAME = "kifal.ma"

HEADERS_LIST = [
    {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36"},
    {
        "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"},
    {
        "User-Agent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36"}
]


def get_random_headers():
    return random.choice(HEADERS_LIST)


def scrape_page(page_number):
    url = f"{BASE_URL}{page_number}"
    headers = get_random_headers()
    response = requests.get(url, headers=headers)
    if response.status_code != 200:
        print(f"⚠️ Erreur {response.status_code} sur la page {page_number}")
        return []

    soup = BeautifulSoup(response.text, 'html.parser')
    annonces = []
    for div in soup.find_all('div', class_='card-annonce-card'):
        lien = div.get('data-url')
        if lien:
            full_url = f"https://occasion.kifal.ma/annonce/{lien}"  # Construire l'URL complète
            #print(f"🔗 Lien trouvé: {full_url}")  # Debug
            annonces.append(full_url)
    return annonces


def scrape_annonce_details(annonce_url):
    headers = get_random_headers()
    response = requests.get(annonce_url, headers=headers)
    if response.status_code != 200:
        print(f"⚠️ Erreur {response.status_code} sur {annonce_url}")
        return ("Erreur",) * 9

    soup = BeautifulSoup(response.text, 'html.parser')

    try:
        titre = soup.find('h1', itemprop='name').text.strip()
        marque_modele = titre.split(' ', 1)
        marque = marque_modele[0] if len(marque_modele) > 1 else "Inconnu"
        modele = marque_modele[1] if len(marque_modele) > 1 else "Inconnu"
    except AttributeError:
        titre,marque, modele = "Inconnu", "Inconnu","Inconnu"

    try:
        annee = soup.find('span', itemprop='productionYear').text.strip()
    except AttributeError:
        annee = "Inconnu"

    try:
        kilometrage = soup.find('span', itemprop='mileageFromOdometer').text.strip() + " km"
    except AttributeError:
        kilometrage = "Inconnu"

    try:
        carburant = soup.find('p', itemprop='fuelType').text.strip()
    except AttributeError:
        carburant = "Inconnu"

    try:
        prix = soup.find('span', class_='h3').text.strip() + " Dh"
    except AttributeError:
        prix = "Inconnu"

    try:
        ville_divs = soup.find_all('div', class_='col-12 col-md-4 col-lg-3 text-center')
        for div in ville_divs:
            p_tag = div.find('p', class_='text-muted-dark mb-0')
            if p_tag and 'itemprop' not in p_tag.attrs:  # Avoid brand, fuelType, etc.
                ville = p_tag.text.strip()
                break
        else:
            ville = "Inconnu"
    except AttributeError:
        ville = "Inconnu"

    return titre, marque, modele, annee, kilometrage, carburant, prix, ville, "voire l'offre"



def scrape_kifal(total_pages):
    all_annonces = []
    for page_number in range(1, total_pages + 1):
        print(f"kifal Scraping la page {page_number}...")
        annonces = scrape_page(page_number)
        for annonce_url in annonces:
            details = scrape_annonce_details(annonce_url)
            all_annonces.append((WEBSITE_NAME, annonce_url, *details))
    return all_annonces


