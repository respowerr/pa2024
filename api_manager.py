import requests
from config import API_BASE_URL

def fetch_tickets():
    response = requests.get(f"{API_BASE_URL}/tickets")
    if response.status_code == 200:
        return response.json()
    else:
        raise Exception("Failed to fetch tickets")

def login(username, password):
    response = requests.post(f"{API_BASE_URL}/login", data={"username": username, "password": password})
    if response.status_code == 200:
        return response.json()
    else:
        raise Exception("Login failed")
