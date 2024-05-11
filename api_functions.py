import requests

def login(username, password):
    url = "http://ddns.callidos-mtf.fr:8080/account/login"
    data = {"username": username, "password": password}
    response = requests.post(url, json=data)
    if response.status_code == 200:
        return response.json()['accessToken']
    else:
        raise Exception("Login failed with status code: " + str(response.status_code))

def create_ticket(title, desc, access_token):
    headers = {'Authorization': f"Bearer {access_token}"}
    data = {"title": title, "desc": desc}
    url = "http://ddns.callidos-mtf.fr:8080/tickets"
    response = requests.post(url, json=data, headers=headers)
    if response.status_code != 201:
        raise Exception("Failed to create ticket with status code: " + str(response.status_code))

def fetch_tickets(access_token):
    headers = {'Authorization': f"Bearer {access_token}"}
    url = "http://ddns.callidos-mtf.fr:8080/tickets"
    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        return response.json()
    else:
        raise Exception("Failed to fetch tickets with status code: " + str(response.status_code))

def create_ticket(title, desc, access_token):
    headers = {'Authorization': f"Bearer {access_token}"}
    data = {"title": title, "desc": desc}
    url = "http://ddns.callidos-mtf.fr:8080/tickets"
    response = requests.post(url, json=data, headers=headers)
    if response.status_code != 201: 
        raise Exception("Failed to create ticket with status code: " + str(response.status_code))
