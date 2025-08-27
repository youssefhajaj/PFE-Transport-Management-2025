import requests
from bs4 import BeautifulSoup
import random
import time
import pandas as pd

BASE_URL = "https://www.avito.ma/fr/maroc/voitures_d_occasion-%C3%A0_vendre?o="

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

def scrape_page(page_number, retries=3):
    url = f"{BASE_URL}{page_number}"
    headers = random.choice(HEADERS_LIST)

    for attempt in range(retries):
        try:
            response = requests.get(url, headers=headers)
            response.raise_for_status()
            soup = BeautifulSoup(response.text, 'html.parser')
            offers = []

            for link_tag in soup.find_all('a', class_="sc-1jge648-0 jZXrfL"):
                announcer_tag = link_tag.find_next('p', class_="sc-1x0vz2r-0 hNCqYw sc-1wnmz4-5 dXzQnB")
                link = link_tag['href']
                announcer = announcer_tag.text.strip() if announcer_tag else None
                details = scrape_announcement_details(link)
                offers.append(("avito.ma", link, announcer, *details))
            return offers

        except requests.exceptions.RequestException as e:
            print(f"Erreur lors de la requête : {e}. Tentative {attempt + 1}/{retries}")
            time.sleep(2)

    return []

def scrape_announcement_details(offer_url):
    headers = random.choice(HEADERS_LIST)
    try:
        response = requests.get(offer_url, headers=headers)
        response.raise_for_status()
        soup = BeautifulSoup(response.text, 'html.parser')

        # Extract details
        model = marque = year = mileage = fuel = price = city = None
        phone = ""

        div_tag = soup.find('div', class_='sc-6p5md9-0 dsWaSi')
        if div_tag:
            spans = div_tag.find_all('span', class_='sc-1x0vz2r-0 kQHNss')
            if len(spans) >= 3:
                year = spans[0].text.strip()
                fuel = spans[2].text.strip()

        items = soup.find_all('li', class_='sc-qmn92k-1 jJjeGO')
        for item in items:
            title = item.find('span', class_='sc-1x0vz2r-0 jZyObG')
            value = item.find('span', class_='sc-1x0vz2r-0 gSLYtF')

            if title and value:
                title_text = title.text.strip()
                value_text = value.text.strip()
                if "Modèle" in title_text:
                    model = value_text
                if "Marque" in title_text:
                    marque = value_text
                elif "Kilométrage" in title_text:
                    mileage = value_text

        price_tag = soup.find('p', class_='sc-1x0vz2r-0 lnEFFR sc-1g3sn3w-13 czygWQ')
        price = price_tag.text.strip() if price_tag else "Non spécifié"

        city_tag = soup.find('span', class_='sc-1x0vz2r-0 iotEHk')
        city = city_tag.text.strip() if city_tag else None

        phone = "Voir L'offre"

        # Return marque and model separately
        return marque, model, year, mileage, fuel, price, city, phone

    except requests.exceptions.RequestException as e:
        print(f"Erreur lors de la récupération des détails : {e}")
        return ("Erreur",) * 8

def scrape_avito(total_pages=150):
    all_offers = []
    for page_number in range(1, total_pages + 1):
        print(f"avito Scraping la page {page_number}...")
        offers = scrape_page(page_number)
        all_offers.extend(offers)
    return all_offers

