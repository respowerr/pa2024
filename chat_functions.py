import tkinter as tk
from tkinter import ttk, scrolledtext
import requests


CHAT_WINDOW_SIZE = "500x600"
API_BASE_URL = "http://ddns.callidos-mtf.fr:8080/tickets/"

def open_chat(ticket_id, master, access_token, user_role):
    """ Open a chat window for a given ticket ID with fixed size. """
    chat_window = tk.Toplevel(master)
    chat_window.title(f"Chat for Ticket ID: {ticket_id}")
    chat_window.geometry(CHAT_WINDOW_SIZE)
    chat_window.resizable(False, False)

    headers = {'Authorization': f"Bearer {access_token}"}
    response = requests.get(f"{API_BASE_URL}{ticket_id}", headers=headers)
    if response.status_code == 200:
        ticket_info = response.json()
        ticket_title = tk.Label(chat_window, text=f"Title: {ticket_info['title']}")
        ticket_title.pack(pady=(10, 0))
        ticket_description = tk.Label(chat_window, text=f"Description: {ticket_info['desc']}")
        ticket_description.pack(pady=(0, 10))

    message_area = scrolledtext.ScrolledText(chat_window, wrap=tk.WORD, state=tk.DISABLED)
    message_area.pack(pady=10, padx=10, expand=True, fill=tk.BOTH)

    refresh_messages(ticket_id, message_area, access_token)

    msg_entry = ttk.Entry(chat_window, width=40)
    msg_entry.pack(side=tk.LEFT, expand=True, fill=tk.X, pady=10, padx=10)

    send_button = ttk.Button(chat_window, text="Send", 
                             command=lambda: send_message(msg_entry, message_area, ticket_id, access_token))
    send_button.pack(side=tk.RIGHT, pady=10, padx=10)
    
def send_message(msg_entry, message_area, ticket_id, access_token):
    """ Send a message and refresh the message area upon successful send. """
    message = msg_entry.get()
    if message:
        headers = {'Authorization': f"Bearer {access_token}", 'Content-Type': 'application/json'}
        data = {"message": message}
        url = f"{API_BASE_URL}{ticket_id}/messages"
        response = requests.post(url, json=data, headers=headers)
        if response.status_code == 200:
            update_message_area(message_area, message, "You: ")
            msg_entry.delete(0, tk.END)
            refresh_messages(ticket_id, message_area, access_token)
        else:
            print("Error sending message:", response.text)

def update_message_area(message_area, message, prefix=""):
    """ Update the message area with new message. """
    if message_area and message:
        message_area.config(state=tk.NORMAL)
        message_area.insert(tk.END, f"{prefix}{message}\n")
        message_area.config(state=tk.DISABLED)
        message_area.yview(tk.END)

def manage_ticket(ticket_id, access_token, action):
    """ Manage ticket actions like resolve or delete. """
    headers = {'Authorization': f"Bearer {access_token}"}
    if action == "resolve":
        url = f"{API_BASE_URL}{ticket_id}/resolve"
    elif action == "delete":
        url = f"{API_BASE_URL}{ticket_id}"
    method = requests.post if action == "resolve" else requests.delete
    response = method(url, headers=headers)
    if response.status_code in [200, 204]:
        print(f"Ticket {action} successfully!")
    else:
        print(f"Failed to {action} ticket: {response.status_code}")

def refresh_messages(ticket_id, message_area, access_token):
    """Fetch and display messages from the server."""
    headers = {'Authorization': f"Bearer {access_token}"}
    url = f"http://ddns.callidos-mtf.fr:8080/tickets/{ticket_id}/messages"
    response = requests.get(url, headers=headers)
    if response.status_code == 200:
        messages = response.json()
        message_area.config(state=tk.NORMAL)
        message_area.delete(1.0, tk.END) 
        for message in messages:
            entry = f"{message['sender']}: {message['message']} ({message['date']})\n"
            message_area.insert(tk.END, entry)
        message_area.config(state=tk.DISABLED)
        message_area.yview(tk.END)
    else:
        print("Failed to load messages:", response.status_code)
