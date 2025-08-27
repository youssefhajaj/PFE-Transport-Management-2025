import requests
from bs4 import BeautifulSoup
import pandas as pd
import random

BASE_URL = "https://www.marocannonces.com/categorie/314/Auto-Moto/Voitures-occasion"
WEBSITE_NAME = "marocannonces.com"
OUTPUT_FILE = "output.xlsx"

# List of user agents to randomize requests
HEADERS_LIST = [
    {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36"
    },
    {
        "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
    },
    {
        "User-Agent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36"
    }
]

def get_random_headers():
    """
    Returns a random user-agent header from HEADERS_LIST.
    """
    return random.choice(HEADERS_LIST)

def scrape_page(page_number):
    """
    Scrape a single page of the website to get announcement links.
    """
    url = f"{BASE_URL}/{page_number}.html" if page_number > 1 else f"{BASE_URL}.html"
    headers = get_random_headers()
    response = requests.get(url, headers=headers)
    soup = BeautifulSoup(response.text, 'html.parser')

    # Find all <li> tags inside <ul class="cars-list">
    annonces = []
    ul_tag = soup.find('ul', class_='cars-list')
    if ul_tag:
        for li_tag in ul_tag.find_all('li'):
            a_tag = li_tag.find('a')
            if a_tag and 'href' in a_tag.attrs:
                lien = "https://www.marocannonces.com/" + a_tag['href']
                annonces.append(lien)
    return annonces

def scrape_annonce_details(annonce_url):
    """
    Scrape details from a single announcement page.
    """
    headers = get_random_headers()
    response = requests.get(annonce_url, headers=headers)
    soup = BeautifulSoup(response.text, 'html.parser')

    # Initialize variables
    annonceur = marque = modele = annee = kilometrage = carburant = prix = ville = telephone = None

    # Scrape "Nom de l'annonceur"
    annonceur_tag = soup.find('dt', string="Annonceur :")
    if annonceur_tag:
        annonceur = annonceur_tag.find_next('dd').text.strip()

    # Scrape "Marque" and "Modèle" (Case 1)
    info_tags = soup.find('ul', class_='info')
    if info_tags:
        for li_tag in info_tags.find_all('li', class_='label'):
            text = li_tag.text.strip()
            if "Marque:" in text:
                marque = text.replace("Marque:", "").strip()
            if "Modèle:" in text:
                modele = text.replace("Modèle:", "").strip()

    # Scrape "Année", "Kilométrage", "Carburant", and "Ville" (Case 2)
    extra_info_tag = soup.find('ul', class_='extraQuestionName')
    if extra_info_tag:
        for li_tag in extra_info_tag.find_all('li'):
            text = li_tag.text.strip()
            if "Année :" in text:
                annee = li_tag.find('a').text.strip() if li_tag.find('a') else text.replace("Année :", "").strip()
            if "Kilométrage :" in text:
                kilometrage = li_tag.find('a').text.strip() if li_tag.find('a') else text.replace("Kilométrage :", "").strip()
            if "Carburant" in text:  # Check for "Carburant" keyword
                carburant = li_tag.find('a').text.strip() if li_tag.find('a') else text.replace("Carburant :",
                                                                                                "").strip()
            if "Ville :" in text:
                ville = text.replace("Ville :", "").strip()

    # Scrape "Prix"
    prix_tag = soup.find('strong', class_='price')
    if prix_tag:
        prix_span = prix_tag.find('span')
        if prix_span:
            prix = prix_span.text.strip()

    # Scrape "Téléphone"
    telephone = "voire l'offre"

    # Return the scraped data
    return annonceur,marque,modele,annee,kilometrage,carburant,prix,ville,telephone


def scrape_marocannonces(total_pages):
    """
    Scrape all pages and collect the data.
    """
    all_annonces = []
    for page_number in range(1, total_pages + 1):
        print(f"marocannonces Scraping la page {page_number}...")
        annonces = scrape_page(page_number)
        for annonce_url in annonces:
            #print(f"Scraping details from {annonce_url}...")
            details = scrape_annonce_details(annonce_url)
            all_annonces.append((WEBSITE_NAME,annonce_url,*details))
    return all_annonces

