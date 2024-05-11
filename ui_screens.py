import tkinter as tk
from tkinter import ttk, PhotoImage
from api_functions import login, create_ticket, fetch_tickets
from chat_functions import open_chat
from chat_functions import refresh_messages
from tkinter import simpledialog
from tkinter import messagebox
import requests

class UI_Screens:
    def __init__(self, master):
        self.master = master
        self.master.title("Système de Ticketing")
        self.master.configure(background='#404040')

        style = ttk.Style()
        style.configure('TFrame', background='#404040')
        style.configure('TLabel', font=('Arial', 12), background='#404040', foreground='white')
        style.configure('TButton', font=('Arial', 12), background='#606060', foreground='white', borderwidth=1)
        style.map('TButton', background=[('active', '#808080'), ('pressed', '#606060')])

        self.original_logo = PhotoImage(file='assets/helix_white.png')
        self.logo_label = ttk.Label(master, image=self.original_logo, background='#404040')
        self.logo_label.pack(side=tk.TOP, pady=20)
        self.master.bind('<Configure>', self.resize_image)

        self.top_frame = ttk.Frame(master, padding=5)
        self.top_frame.pack(fill=tk.X, side=tk.TOP)
        self.username_display = ttk.Label(self.top_frame, text="", font=('Arial', 12, 'bold'), background='#404040', foreground='white')
        self.username_display.pack(side=tk.RIGHT, padx=20)

        self.main_frame = ttk.Frame(master, padding=20)
        self.main_frame.pack(fill=tk.BOTH, expand=True)

        self.access_token = None
        self.username = None

        self.login_screen()

    def resize_image(self, event):
        new_width = max(min(self.master.winfo_width() // 5, 300), 100)
        new_height = int(new_width * (self.original_logo.height() / self.original_logo.width()))
        image = self.original_logo.subsample(int(self.original_logo.width() / new_width), int(self.original_logo.height() / new_height))
        self.logo_label.configure(image=image)
        self.logo_label.image = image

    def login_screen(self):
        for widget in self.main_frame.winfo_children():
            widget.destroy()

        login_label = ttk.Label(self.main_frame, text="Connexion", font=('Helvetica', 18))
        login_label.grid(row=0, column=0, columnspan=2, pady=(10, 20))

        username_label = ttk.Label(self.main_frame, text="Nom d'utilisateur :")
        username_label.grid(row=1, column=0, sticky=tk.W)
        username_entry = ttk.Entry(self.main_frame, width=20)
        username_entry.grid(row=1, column=1, sticky=tk.EW, padx=5)

        password_label = ttk.Label(self.main_frame, text="Mot de passe :")
        password_label.grid(row=2, column=0, sticky=tk.W)
        password_entry = ttk.Entry(self.main_frame, show='*', width=20)
        password_entry.grid(row=2, column=1, sticky=tk.EW, padx=5)

        login_button = ttk.Button(self.main_frame, text="Login", command=lambda: self.try_login(username_entry.get(), password_entry.get()))
        login_button.grid(row=3, column=0, columnspan=2, pady=20)

    def try_login(self, username, password):
        try:
            self.access_token = login(username, password)
            self.username = username
            self.username_display.config(text=f"Connecté en tant que : {self.username}")
            self.home_screen()
        except Exception as e:
            messagebox.showerror("Erreur de connexion", str(e))

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
        self.clear_frame(self.main_frame) 

        tree = ttk.Treeview(self.main_frame, columns=('Ticket ID', 'Sender', 'Resolved', 'Title'), show='headings')
        tree.heading('Ticket ID', text='Ticket ID')
        tree.heading('Sender', text='Sender')
        tree.heading('Resolved', text='Resolved')
        tree.heading('Title', text='Title')
        tree.pack(fill=tk.BOTH, expand=True)
        
        for ticket in fetch_tickets(self.access_token):
            tree.insert('', 'end', values=(ticket['ticket_id'], ticket['sender'], ticket['resolved'], ticket['title']))

        back_button = ttk.Button(self.main_frame, text="Retour", command=self.home_screen)
        back_button.pack(side=tk.TOP, anchor='nw', pady=10)

    def create_ticket(self, title, desc):
        url = "http://ddns.callidos-mtf.fr:8080/tickets"
        data = {"title": title, "desc": desc}
        headers = {'Authorization': f"Bearer {self.access_token}"}
        try:
            response = requests.post(url, json=data, headers=headers)
            if response.status_code in [200, 201]:
                messagebox.showinfo("Succès", "Ticket créé avec succès.")
            else:
                messagebox.showerror("Erreur", f"Échec de la création du ticket avec le code de statut: {response.status_code}")
        except requests.RequestException as e:
            messagebox.showerror("Erreur réseau", str(e))

    def prompt_new_ticket(self):
        title = simpledialog.askstring("Create Ticket", "Enter title:", parent=self.master)
        desc = simpledialog.askstring("Create Ticket", "Enter description:", parent=self.master)
        if title and desc:
            self.create_ticket(title, desc)


    def logout(self):
        self.access_token = None
        self.username = None
        self.username_display.config(text="")
        self.login_screen()