import os
import pandas as pd
import datetime
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.base import MIMEBase
from email import encoders
from email.mime.text import MIMEText


def send_email_with_attachment(to_emails, subject, body, attachment_path):
    from_email = "youssef.hajaj111@gmail.com"  # Replace with your email
    password = "xxxx xxxx xxxx"  # Replace with your email password

    msg = MIMEMultipart()
    msg['From'] = from_email
    msg['Subject'] = subject

    # Attach the body text
    msg.attach(MIMEText(body, 'plain'))

    # Attach the file
    part = MIMEBase('application', 'octet-stream')
    with open(attachment_path, 'rb') as file:
        part.set_payload(file.read())
    encoders.encode_base64(part)
    part.add_header('Content-Disposition', f'attachment; filename={os.path.basename(attachment_path)}')
    msg.attach(part)

    try:
        # Connect to Gmail's SMTP server
        server = smtplib.SMTP('smtp.gmail.com', 587)
        server.starttls()
        server.login(from_email, password)

        # Join all recipients into a single string with commas
        msg['To'] = ", ".join(to_emails)

        # Send email to all recipients in the list
        server.sendmail(from_email, to_emails, msg.as_string())
        server.quit()
        print("Emails sent successfully!")
    except Exception as e:
        print(f"Failed to send emails. Error: {e}")


def save_to_excel(data):
    jours_fr = ["lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche"]

    now = datetime.datetime.now()
    jour = jours_fr[now.weekday()]  # Obtenir le jour en français
    date_heure = now.strftime("%d-%m-%Y_%H-%M")  # Format date et heure

    # Define the folder paths
    base_folder = "data"
    month_year_folder = now.strftime("%B_%Y")  # Format as 'February_2025'
    folder_path = os.path.join(base_folder, month_year_folder)

    # Debug: Check if the base folder exists
    if not os.path.exists(base_folder):
        print(f"Base folder '{base_folder}' does not exist. Creating it now.")
        os.makedirs(base_folder)
    else:
        print(f"Base folder '{base_folder}' already exists.")

    # Debug: Check if the month-year folder exists
    if not os.path.exists(folder_path):
        print(f"Month-year folder '{month_year_folder}' does not exist. Creating it now.")
        os.makedirs(folder_path)
    else:
        print(f"Month-year folder '{month_year_folder}' already exists.")

    # Define the file name with the desired format
    filename = f"{jour}_{date_heure}.xlsx"
    file_path = os.path.join(folder_path, filename)  # Full file path

    # Create the DataFrame and save it to Excel
    df = pd.DataFrame(data, columns=["Website", "Lien", "Nom de l'annonceur", "Marque", "Modèle",
                                     "Année d'immatriculation", "Kilométrage", "Carburant",
                                     "Prix", "Ville", "Téléphone"])
    df.to_excel(file_path, index=False, engine="openpyxl")

    # Debug: Output the file path where the data was saved
    print(f"File saved to {file_path}")

    # Send the email with the attached file
    email_subject = f"Confirmation: File {filename} Saved Successfully voiture d'occasion "
    email_body = (f"The file '{filename}' has been saved successfully at the following location:\n\n"
                  f"File Path: {file_path}\n\n"
                  f"Please find the file attached.\n\n"
                  f"Best regards,\nYour Automated System")

    # List of recipients
    recipients = [
        "hicham_labriki@yahoo.fr",
        "contactkounhany@gmail.com"    ]

    send_email_with_attachment(recipients, email_subject, email_body, file_path)
