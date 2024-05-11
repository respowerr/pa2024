import tkinter as tk
from tkinter import ttk, PhotoImage, Toplevel, simpledialog, scrolledtext
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
        style.map('TButton', background=[('active', '#808080'), ('pressed', '#606060')],
                  foreground=[('active', 'white'), ('pressed', 'white')])

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

        login_button = ttk.Button(self.main_frame, text="Login", command=lambda: self.login(username_entry.get(), password_entry.get()))
        login_button.grid(row=3, column=0, columnspan=2, pady=20)

        self.main_frame.grid_columnconfigure(1, weight=1)

    def login(self, username, password):
        url = "http://ddns.callidos-mtf.fr:8080/account/login"
        data = {"username": username, "password": password}
        response = requests.post(url, json=data)
        if response.status_code == 200:
            user_data = response.json()
            self.access_token = user_data.get('accessToken')
            self.username = username
            self.username_display.config(text=f"Connecté en tant que : {self.username}")
            self.home_screen()

    def home_screen(self):
        for widget in self.main_frame.winfo_children():
            widget.destroy()

        welcome_label = ttk.Label(self.main_frame, text="Bienvenue dans le Système de Ticketing", font=('Helvetica', 18))
        welcome_label.pack(pady=40)

        tickets_button = ttk.Button(self.main_frame, text="Voir les Tickets", command=self.fetch_tickets)
        tickets_button.pack(pady=10)

        create_ticket_button = ttk.Button(self.main_frame, text="Créer un Ticket", command=self.create_ticket)
        create_ticket_button.pack(pady=10)

        logout_button = ttk.Button(self.main_frame, text="Déconnexion", command=self.logout)
        logout_button.pack(pady=10)

    def fetch_tickets(self):
        headers = {'Authorization': f"Bearer {self.access_token}"}
        response = requests.get("http://ddns.callidos-mtf.fr:8080/tickets", headers=headers)
        if response.status_code == 200:
            tickets = response.json()
            self.show_tickets(tickets)

    def show_tickets(self, tickets):
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

        tree.bind('<Double-1>', lambda event: self.open_chat(tree))

    def open_chat(self, tree):
        item = tree.selection()[0]
        ticket_id = tree.item(item, "values")[0]
        chat_window = Toplevel(self.master)
        chat_window.title(f"Chat for Ticket ID: {ticket_id}")
        chat_window.geometry("400x400")

        message_area = scrolledtext.ScrolledText(chat_window, wrap=tk.WORD, state=tk.DISABLED)
        message_area.pack(pady=10, padx=10, expand=True, fill=tk.BOTH)

        msg_entry = ttk.Entry(chat_window, width=40)
        msg_entry.pack(side=tk.LEFT, expand=True, fill=tk.X, pady=10, padx=10)

        send_button = ttk.Button(chat_window, text="Send", command=lambda: self.send_message(msg_entry, message_area, ticket_id))
        send_button.pack(side=tk.RIGHT, pady=10, padx=10)

        self.refresh_messages(ticket_id, message_area)

    def load_messages(self, ticket_id, message_area):
        url = f"http://ddns.callidos-mtf.fr:8080/tickets/{ticket_id}/messages"
        headers = {'Authorization': f"Bearer {self.access_token}"}
        response = requests.get(url, headers=headers)
        if response.status_code == 200:
            messages = response.json()
            message_area.config(state=tk.NORMAL)
            for message in messages:
                sender = message.get("sender", "Unknown")
                msg_text = message.get("message", "")
                message_area.insert(tk.END, f"{sender}: {msg_text}\n")
            message_area.config(state=tk.DISABLED)
            message_area.yview(tk.END)

    def send_message(self, msg_entry, message_area, ticket_id):
        message = msg_entry.get()
        if message:
            headers = {'Authorization': f"Bearer {self.access_token}"}
            data = {"message": message}
            url = f"http://ddns.callidos-mtf.fr:8080/tickets/{ticket_id}/messages"
            response = requests.post(url, json=data, headers=headers)
            if response.status_code == 201:
                msg_entry.delete(0, tk.END)
                self.refresh_messages(ticket_id, message_area)

    def refresh_messages(self, ticket_id, message_area):
        """Refresh the message area with new messages from the server."""
        url = f"http://ddns.callidos-mtf.fr:8080/tickets/{ticket_id}/messages"
        headers = {'Authorization': f"Bearer {self.access_token}"}
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
        self.master.after(1000, lambda: self.refresh_messages(ticket_id, message_area))


    def create_ticket(self):
        title = simpledialog.askstring("Créer Ticket", "Titre:", parent=self.master)
        desc = simpledialog.askstring("Créer Ticket", "Description:", parent=self.master)

        if title and desc:
            data = {"title": title, "desc": desc}
            headers = {'Authorization': f"Bearer {self.access_token}"}
            response = requests.post("http://ddns.callidos-mtf.fr:8080/tickets", json=data, headers=headers)
            if response.status_code == 201:
                self.fetch_tickets()

    def logout(self):
        self.access_token = None
        self.username = None
        self.username_display.config(text="")
        self.login_screen()
