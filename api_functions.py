import requests
from tkinter import messagebox

def login(username, password):
    url = "http://ddns.callidos-mtf.fr:8080/account/login"
    data = {"username": username, "password": password}
    response = requests.post(url, json=data)
    if response.status_code == 200:
        return response.json()['accessToken']
    else:
        raise Exception("Login failed with status code: " + str(response.status_code))

def create_ticket(title, desc, access_token):
    url = "http://ddns.callidos-mtf.fr:8080/tickets"
    data = {"title": title, "desc": desc}
    headers = {'Authorization': f"Bearer {access_token}"} 
    try:
        response = requests.post(url, json=data, headers=headers)
        if response.status_code == 200:
            messagebox.showinfo("Success", "Ticket created successfully")
        elif response.status_code == 401:
            messagebox.showerror("Authorization Error", "Failed due to invalid or expired token")
        else:
            messagebox.showerror("Error", f"Failed to create ticket with status code: {response.status_code}")
    except requests.RequestException as e:
        messagebox.showerror("Network Error", str(e))

def fetch_tickets(access_token):
    if not access_token:
        raise Exception("No access token provided.")
    headers = {'Authorization': f'Bearer {access_token}'}
    url = "http://ddns.callidos-mtf.fr:8080/tickets"
    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        return response.json()
    else:
        raise Exception("Failed to fetch tickets with status code: " + str(response.status_code))
