import requests
from tkinter import messagebox

def login(username, password):
    url = "http://ddns.callidos-mtf.fr:8080/account/login"
    data = {"username": username, "password": password}
    response = requests.post(url, json=data)
    if response.status_code == 200:
        return response.json()['accessToken']
    else:
        raise Exception("La connexion a échoué avec le code d'état : " + str(response.status_code))

def create_ticket(title, desc, access_token):
    url = "http://ddns.callidos-mtf.fr:8080/tickets"
    data = {"title": title, "desc": desc}
    headers = {'Authorization': f"Bearer {access_token}"} 
    try:
        response = requests.post(url, json=data, headers=headers)
        if response.status_code == 200:
            messagebox.showinfo("Succès", "Ticket créé avec succès")
        elif response.status_code == 401:
            messagebox.showerror("Erreur d'autorisation", "Échec en raison d'un jeton invalide ou expiré")
        else:
            messagebox.showerror("Erreur", f"Échec de la création du ticket avec le code d'état : {response.status_code}")
    except requests.RequestException as e:
        messagebox.showerror("Erreur réseau", str(e))

def fetch_tickets(access_token):
    if not access_token:
        raise Exception("Aucun jeton d'accès fourni.")
    headers = {'Authorization': f'Bearer {access_token}'}
    url = "http://ddns.callidos-mtf.fr:8080/tickets"
    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        return response.json()
    else:
        raise Exception("Échec de la récupération des tickets avec le code d'état : " + str(response.status_code))
