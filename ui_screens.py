import tkinter as tk
from tkinter import ttk, PhotoImage
from api_functions import login, create_ticket, fetch_tickets
from chat_functions import open_chat
from tkinter import simpledialog
from tkinter import messagebox
import requests


class UI_Screens:
    def __init__(self, master):
        self.master = master
        self.master.title("Système de Ticketing")
        self.configure_window()
        self.configure_styles()
        self.logo_label = ttk.Label(master, text="HELIX", font=('Helvetica', 24, 'bold'))
        self.logo_label.pack(side=tk.TOP, pady=(20, 10))
        self.main_frame = ttk.Frame(master)
        self.main_frame.pack(expand=True, fill=tk.BOTH, padx=20, pady=20)
        self.access_token = None
        self.username = None
        self.login_screen()

    def configure_window(self):
        """ Configure les propriétés de la fenêtre principale. """
        self.master.geometry('800x600')
        self.master.configure(background='#333')
        self.master.resizable(False, False)

    def configure_styles(self):
        """ Configure les styles de tous les widgets. """
        style = ttk.Style()
        style.theme_use('clam')
        style.configure('TFrame', background='#333')
        style.configure('TLabel', font=('Helvetica', 12), background='#333', foreground='white')
        style.configure('TButton', font=('Helvetica', 12), background='#555', foreground='white', borderwidth=1)
        style.map('TButton', background=[('active', '#666'), ('pressed', '#777')])
        style.configure('TEntry', font=('Helvetica', 12), foreground='black', fieldbackground='#fff')

    def resize_image(self, event):
        new_width = max(min(self.master.winfo_width() // 5, 300), 100)
        new_height = int(new_width * (self.original_logo.height() / self.original_logo.width()))
        image = self.original_logo.subsample(int(self.original_logo.width() / new_width), int(self.original_logo.height() / new_height))
        self.logo_label.configure(image=image)
        self.logo_label.image = image

    def login_screen(self):
        """ Crée l'écran de connexion avec des entrées pour l'username et le mot de passe. """
        self.clear_frame(self.main_frame)

        login_frame = ttk.Frame(self.main_frame, padding=10)
        login_frame.pack(expand=True)

        ttk.Label(login_frame, text="Connexion", font=('Helvetica', 18, 'bold')).pack(pady=(0, 20))

        username_label = ttk.Label(login_frame, text="Nom d'utilisateur :")
        username_label.pack(fill='x', expand=True)
        username_entry = ttk.Entry(login_frame)
        username_entry.pack(fill='x', expand=True, pady=(5, 20))

        password_label = ttk.Label(login_frame, text="Mot de passe :")
        password_label.pack(fill='x', expand=True)
        password_entry = ttk.Entry(login_frame, show='*')
        password_entry.pack(fill='x', expand=True, pady=(5, 20))

        login_button = ttk.Button(login_frame, text="Login", command=lambda: self.try_login(username_entry.get(), password_entry.get()))
        login_button.pack(pady=10)

    def try_login(self, username, password):
        url = "http://ddns.callidos-mtf.fr:8080/account/login"
        data = {"username": username, "password": password}
        try:
            response = requests.post(url, json=data)
            if response.status_code == 200:
                self.access_token = response.json().get('accessToken')
                self.username = username
                self.master.title(f"Connecté en tant que : {self.username}")
                self.home_screen()
            else:
                messagebox.showerror("Erreur de connexion", "Identifiants incorrects ou problème serveur.")
        except requests.ConnectionError:
            messagebox.showerror("Erreur de connexion", "Impossible de se connecter au serveur.")

    def clear_frame(self, frame):
        """ Efface tous les widgets dans le cadre spécifié. """
        for widget in frame.winfo_children():
            widget.destroy()

    def home_screen(self):
        for widget in self.main_frame.winfo_children():
            widget.destroy()

        welcome_label = ttk.Label(self.main_frame, text="Bienvenue dans le Système de Ticketing", font=('Helvetica', 18))
        welcome_label.pack(pady=40)

        tickets_button = ttk.Button(self.main_frame, text="Voir les Tickets", command=lambda: self.show_tickets())
        tickets_button.pack(pady=10)

        create_ticket_button = ttk.Button(self.main_frame, text="Create a Ticket", command=self.prompt_new_ticket)
        create_ticket_button.pack(pady=10)

        logout_button = ttk.Button(self.main_frame, text="Déconnexion", command=self.logout)
        logout_button.pack(pady=10)

    def show_tickets(self):
        try:
            tickets = fetch_tickets(self.access_token)
            for widget in self.main_frame.winfo_children():
                widget.destroy()

            back_button = ttk.Button(self.main_frame, text="Retour", command=self.home_screen)
            back_button.pack(side=tk.TOP, anchor='nw', pady=10)

            tree = ttk.Treeview(self.main_frame, columns=('Ticket ID', 'Sender', 'Resolved', 'Title'), show='headings')
            tree.heading('Ticket ID', text='Ticket ID')
            tree.heading('Sender', text='Sender')
            tree.heading('Resolved', text='Resolved')
            tree.heading('Title', text='Title')
            tree.pack(fill=tk.BOTH, expand=True)

            for ticket in tickets:
                tree.insert('', 'end', values=(ticket['ticket_id'], ticket['sender'], ticket['resolved'], ticket['title']))

            tree.bind('<Double-1>', lambda event: open_chat(tree.item(tree.selection())['values'][0], self.master, self.access_token, self.refresh_messages))
        except Exception as e:
            messagebox.showerror("Erreur", str(e))

    def prompt_new_ticket(self):
        title = simpledialog.askstring("Create Ticket", "Enter title:", parent=self.master)
        desc = simpledialog.askstring("Create Ticket", "Enter description:", parent=self.master)
        if title and desc:
            create_ticket(title, desc, self.access_token)

    def logout(self):
        self.access_token = None
        self.username = None
        self.username_display.config(text="")
        self.login_screen()

    def refresh_messages(self, ticket_id, message_area, access_token):
        """Refresh the message area with new messages from the server."""
        headers = {'Authorization': f"Bearer {access_token}"}
        url = f"http://ddns.callidos-mtf.fr:8080/tickets/{ticket_id}/messages"
        response = requests.get(url, headers=headers)
        if response.status_code == 200:
            messages = response.json()
            message_area.config(state=tk.NORMAL)
            message_area.delete(1.0, tk.END)
            for message in messages:
                sender = message.get("sender", "Unknown")
                msg_text = message.get("message", "")
                message_area.insert(tk.END, f"{sender}: {msg_text}\n")
            message_area.config(state=tk.DISABLED)
            message_area.yview(tk.END)
        message_area.after(1000, lambda: self.refresh_messages(ticket_id, message_area, access_token))
