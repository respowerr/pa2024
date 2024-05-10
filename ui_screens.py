# ui_screens.py

import tkinter as tk
from tkinter import ttk, messagebox
import requests

class UI_Screens:
    def __init__(self, master):
        self.master = master
        self.main_frame = ttk.Frame(master)
        self.main_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)
        self.username_display = ttk.Label(master, text="", font=('Arial', 12), background='#242424', foreground='white', anchor='e')
        self.username_display.pack(fill=tk.X)
        self.access_token = None 
        self.username = None 

    def login_screen(self):
        for widget in self.main_frame.winfo_children():
            widget.destroy()

        ttk.Label(self.main_frame, text="Nom d'utilisateur:", font=('Arial', 14, 'bold')).pack(pady=10)
        username_entry = ttk.Entry(self.main_frame, font=('Arial', 14))
        username_entry.pack(pady=10)

        ttk.Label(self.main_frame, text="Mot de passe:", font=('Arial', 14, 'bold')).pack(pady=10)
        password_entry = ttk.Entry(self.main_frame, show='*', font=('Arial', 14))
        password_entry.pack(pady=10)

        ttk.Button(self.main_frame, text="Login", command=lambda: self.login(username_entry.get(), password_entry.get())).pack(pady=20)

    def login(self, username, password):
        url = "http://ddns.callidos-mtf.fr:8080/account/login"
        data = {"username": username, "password": password}
        try:
            response = requests.post(url, json=data)
            if response.status_code == 200:
                user_data = response.json()
                self.access_token = user_data.get('accessToken')
                self.username = username
                self.username_display.config(text=f"Connecté en tant que: {self.username}")
                self.home_screen()
            else:
                messagebox.showerror("Erreur", "Nom d'utilisateur ou mot de passe incorrect")
        except requests.exceptions.RequestException:
            messagebox.showerror("Erreur", "Problème de connexion au service")

    def home_screen(self):
        for widget in self.main_frame.winfo_children():
            widget.destroy()

        ttk.Label(self.main_frame, text="Welcome to the Ticketing System", font=('Helvetica', 18)).pack(pady=40)
        ttk.Button(self.main_frame, text="View Tickets", command=self.view_tickets).pack(pady=10)
        ttk.Button(self.main_frame, text="Logout", command=self.logout).pack(pady=10)

    def view_tickets(self):
        messagebox.showinfo("Tickets", "Displaying tickets here would require further implementation.")

    def logout(self):
        self.access_token = None
        self.username = None
        self.username_display.config(text="")
        self.login_screen()
