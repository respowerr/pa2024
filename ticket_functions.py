import requests
from tkinter import messagebox

def create_ticket(title, desc, access_token):
    url = "http://ddns.callidos-mtf.fr:8080/tickets"
    data = {"title": title, "description": desc}
    headers = {'Authorization': f'Bearer {access_token}'}
    response = requests.post(url, json=data, headers=headers)
    if response.status_code == 200:
        messagebox.showinfo("Succès", "Ticket créé avec succès")
    else:
        messagebox.showerror("Erreur", "Problème lors de la création du ticket")

def fetch_tickets(access_token):
    url = "http://ddns.callidos-mtf.fr:8080/tickets"
    headers = {'Authorization': f'Bearer {access_token}'}
    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        return response.json()
    else:
        messagebox.showerror("Erreur", "Impossible de récupérer les tickets")
        return []