import requests
from bs4 import BeautifulSoup
import random
import pandas as pd

BASE_URL = "https://www.moteur.ma/fr/voiture/achat-voiture-occasion/"
WEBSITE_NAME = "moteur.ma"

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
    headers = get_random_headers()
    url = f"{BASE_URL}?page={page_number}"
    response = requests.get(url, headers=headers)

    if response.status_code != 200:
        print(f"Erreur: La page {page_number} n'a pas été chargée correctement (Code: {response.status_code})")
        return []

    soup = BeautifulSoup(response.text, 'html.parser')
    annonces = []

    for sub_div_tag in soup.find_all('div', class_='row-item row-item-checkout link'):
        a_tag = sub_div_tag.find('a', class_="slide")
        prix_tag = sub_div_tag.find('div', class_='price color_primary PriceListing')  # Extraction du prix
        if a_tag and 'href' in a_tag.attrs:
            lien = a_tag['href']
            if not lien.startswith("https"):
                lien = "https://www.moteur.ma" + lien

            prix = prix_tag.get_text(strip=True) if prix_tag else "Non spécifié"  # Si prix non trouvé, on met une valeur par défaut
            annonces.append([lien, prix])

    #print(f"Nombre d'annonces trouvées sur la page {page_number}: {len(annonces)}")
    return annonces


def scrape_annonce_details(annonce_url):
    """Scrape les détails d'une annonce spécifique."""

    annonceur = "voire l'offre"  # Valeur par défaut
    marque = modele = ville = telephone = "voire l'offre"
    annee = kilometrage = carburant = "Non spécifié"  # Valeurs par défaut

    headers = get_random_headers()
    response = requests.get(annonce_url, headers=headers)

    if response.status_code != 200:
        print(f"Erreur de chargement de l'annonce: {annonce_url}")
        return annonceur, marque, modele, annee, kilometrage, carburant, ville, telephone

    soup = BeautifulSoup(response.text, 'html.parser')

    # Extraction de l'annonceur
    annonceur_tag = soup.select_one(".actions.block_tele a")
    if annonceur_tag:
        annonceur = annonceur_tag.get_text(strip=True)

    # Extraction de la ville
    ville_tag = soup.select_one(".actions.block_tele li a[href*='ville']")
    if ville_tag:
        ville = ville_tag.get_text(strip=True)

    # Extraction de la marque et du modèle
    marque_modele_tag = soup.select_one(".col-md-12.text-center.ads-detail h1 .text_bold")
    if marque_modele_tag:
        marque_modele = marque_modele_tag.get_text(strip=True)
        # Séparer la marque et le modèle
        marque_parts = marque_modele.split()
        marque = marque_parts[0]  # Premier mot comme marque
        modele = " ".join(marque_parts[1:])  # Le reste comme modèle

    # Extraction des détails spécifiques
    details = soup.find_all("div", class_="detail_line")

    for detail in details:
        label = detail.find("span", class_="col-md-6 col-xs-6")
        value = detail.find("span", class_="text_bold")

        if label and value:
            label_text = label.get_text(strip=True)
            value_text = value.get_text(strip=True)

            if "Année" in label_text:
                annee = value_text
            elif "Kilométrage" in label_text:
                kilometrage = value_text
            elif "Carburant" in label_text:
                carburant = value_text

    return annonceur, marque, modele, annee, kilometrage, carburant, ville, telephone


def scrape_moteur(total_pages):
    all_annonces = []

    for page in range(1, total_pages + 1):
        print(f"moteur Scraping la page {page}...")
        annonces = scrape_page(page)
        for annonce_url, prix in annonces:  # Maintenant, nous récupérons le prix ici
            annonceur, marque, modele, annee, kilometrage, carburant, ville, telephone = scrape_annonce_details(
                annonce_url)
            all_annonces.append([
                WEBSITE_NAME, annonce_url, annonceur, marque, modele, annee, kilometrage, carburant, prix, ville,
                telephone
            ])

    return all_annonces
