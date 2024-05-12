import tkinter as tk
from tkinter import ttk, PhotoImage, messagebox
import requests
from chat_functions import open_chat, refresh_messages, send_message
from ticket_functions import create_ticket, fetch_tickets


class UI_Screens:
    def __init__(self, master):
        self.master = master
        self.master.title("Système de Ticketing")
        self.configure_window()
        self.configure_styles()
        self.original_logo_image = PhotoImage(file='assets/helix_white.png')
        self.logo_image = self.original_logo_image.subsample(3, 3)
        self.logo_label = ttk.Label(master, image=self.logo_image, anchor='center')
        self.logo_label.pack(side=tk.TOP, pady=10)
        self.main_frame = ttk.Frame(master)
        self.main_frame.pack(fill=tk.BOTH, pady=20)
        self.username_display = ttk.Label(self.master, text="", font=('Arial', 12, 'bold'), background='#333', foreground='white')
        self.username_display.pack(side=tk.BOTTOM, fill=tk.X, padx=20, pady=5)
        self.access_token = None
        self.roles = []
        self.username = None
        self.login_screen()
        self.center_window(self.master)

    def configure_window(self):
        self.master.geometry('1000x600')
        self.master.configure(background='#333')
        self.master.resizable(False, False)

    def configure_styles(self):
        style = ttk.Style()
        style.theme_use('clam')
        style.configure('TFrame', background='#333')
        style.configure('TLabel', font=('Arial', 12), background='#333', foreground='white')
        style.configure('TButton', font=('Arial', 12), background='#555', foreground='white', borderwidth=1, padding=5)
        style.map('TButton', background=[('active', '#666'), ('pressed', '#777')], foreground=[('pressed', 'white'), ('active', 'white')])
        style.configure('TEntry', font=('Arial', 12), foreground='black', fieldbackground='#fff', padding=5)
        style.configure('Treeview', font=('Arial', 11), background='#333', foreground='white', fieldbackground='#333')
        style.map('Treeview', background=[('selected', '#555')])
        style.configure('Vertical.TScrollbar', gripcount=0,background='#555', darkcolor='#555', lightcolor='#555',troughcolor='#333', bordercolor='#333', arrowcolor='white')
        style.configure('TText', font=('Arial', 11), foreground='black', background='#fff')

    def center_window(self, window):
        window.update_idletasks()
        width = window.winfo_width()
        height = window.winfo_height()
        x = (window.winfo_screenwidth() // 2) - (width // 2)
        y = (window.winfo_screenheight() // 2) - (height // 2)
        window.geometry('{}x{}+{}+{}'.format(width, height, x, y))

    def login_screen(self):
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
        login_button = ttk.Button(login_frame, text="Se connecter", command=lambda: self.try_login(username_entry.get(), password_entry.get()))
        login_button.pack(pady=10)

    def try_login(self, username, password):
        url = "http://ddns.callidos-mtf.fr:8080/account/login"
        data = {"username": username, "password": password}
        try:
            response = requests.post(url, json=data)
            if response.status_code == 200:
                response_data = response.json()
                self.access_token = response_data.get('accessToken')
                self.roles = response_data.get('roles', [])
                self.username = username
                role_text = ', '.join(self.roles)
                self.username_display.config(text=f"Connecté en tant que: {self.username} - Roles: {role_text}")
                self.master.title(f"Connecté en tant que : {self.username}")
                self.home_screen()
            else:
                messagebox.showerror("Erreur de connexion", "Identifiants incorrects ou problème serveur.")
        except requests.ConnectionError:
            messagebox.showerror("Erreur de connexion", "Impossible de se connecter au serveur.")

    def logout(self):
        self.access_token = None
        self.username = None
        self.roles = []
        self.username_display.config(text="")
        self.login_screen()

    def clear_frame(self, frame):
        for widget in frame.winfo_children():
            widget.destroy()

    def home_screen(self):
        self.clear_frame(self.main_frame)
        welcome_label = ttk.Label(self.main_frame, text="Bienvenue dans le Système de Ticketing", font=('Helvetica', 18))
        welcome_label.pack(pady=40)
        tickets_button = ttk.Button(self.main_frame, text="Voir les Tickets", command=self.show_tickets)
        tickets_button.pack(pady=10)
        create_ticket_button = ttk.Button(self.main_frame, text="Créer un Ticket", command=self.prompt_new_ticket)
        create_ticket_button.pack(pady=10)
        logout_button = ttk.Button(self.main_frame, text="Déconnexion", command=self.logout)
        logout_button.pack(pady=10)

    def prompt_new_ticket(self):
        popup = tk.Toplevel(self.master)
        popup.title("Créer un Nouveau Ticket")
        popup.geometry("300x200")
        popup.resizable(False, False)
        popup.configure(background='#333')
        label_style = {'font': ('Arial', 12), 'bg': '#333', 'fg': 'white'}
        entry_style = {'font': ('Arial', 10), 'bg': '#fff', 'fg': 'black', 'insertbackground': 'black'}
        tk.Label(popup, text="Entrez le Titre du Ticket:", **label_style).pack(pady=(10, 5))
        title_entry = tk.Entry(popup, **entry_style)
        title_entry.pack(pady=(0, 10), padx=20, fill='x')
        tk.Label(popup, text="Entrez la Description:", **label_style).pack(pady=(5, 5))
        desc_entry = tk.Entry(popup, **entry_style)
        desc_entry.pack(pady=(0, 10), padx=20, fill='x')
        def submit_ticket():
            title = title_entry.get()
            desc = desc_entry.get()
            if title and desc:
                create_ticket(title, desc, self.access_token)
                popup.destroy()
        submit_button = ttk.Button(popup, text="Créer le Ticket", command=submit_ticket)
        submit_button.pack(pady=(5, 10))
        self.center_window(popup)
        popup.grab_set()
        self.master.wait_window(popup)

    def show_tickets(self):
        try:
            self.clear_frame(self.main_frame)

            tree = ttk.Treeview(self.main_frame, columns=('ID Ticket', 'Expéditeur', 'Résolu', 'Titre'), show='headings')
            tree.heading('ID Ticket', text='ID Ticket')
            tree.heading('Expéditeur', text='Expéditeur')
            tree.heading('Résolu', text='Résolu')
            tree.heading('Titre', text='Titre')
            tree.pack(fill=tk.BOTH, expand=True, pady=10)

            tickets = fetch_tickets(self.access_token)
            for ticket in tickets:
                tree.insert('', 'end', values=(ticket['ticket_id'], ticket['sender'], ticket['resolved'], ticket['title']))

            button_frame = ttk.Frame(self.main_frame)
            button_frame.pack(pady=10, fill=tk.X)

            back_button = ttk.Button(button_frame, text="Retour", command=self.home_screen)
            back_button.pack(side=tk.LEFT, padx=10)

            chat_button = ttk.Button(button_frame, text="Chat", state='disabled')
            chat_button.pack(side=tk.LEFT, padx=10)

            if "ROLE_ADMIN" in self.roles:
                resolve_button = ttk.Button(button_frame, text="Marquer comme Résolu", state='disabled')
                delete_button = ttk.Button(button_frame, text="Supprimer le Ticket", state='disabled')
                resolve_button.pack(side=tk.LEFT, padx=10)
                delete_button.pack(side=tk.LEFT, padx=10)

            def update_buttons(event):
                selected_item = tree.selection()
                if selected_item:
                    ticket_id = tree.item(selected_item, 'values')[0]

                    chat_button.config(state='normal', command=lambda: open_chat(ticket_id, self.master, self.access_token, "ROLE_USER"))

                    if "ROLE_ADMIN" in self.roles:
                        resolved = tree.item(selected_item, 'values')[2]
                        delete_button.config(state='normal', command=lambda: self.delete_ticket(ticket_id))
                        if resolved == 'False':
                            resolve_button.config(state='normal', command=lambda: self.resolve_ticket(ticket_id))
                        else:
                            resolve_button.config(state='disabled')
                    else:
                        if 'resolve_button' in locals():
                            resolve_button.config(state='disabled')
                        if 'delete_button' in locals():
                            delete_button.config(state='disabled')
                else:
                    chat_button.config(state='disabled')

            tree.bind('<<TreeviewSelect>>', update_buttons)

        except Exception as e:
            messagebox.showerror("Erreur", f"Une erreur s'est produite : {str(e)}")

    def resolve_ticket(self, ticket_id):
        url = f"http://ddns.callidos-mtf.fr:8080/tickets/{ticket_id}/resolve"
        headers = {'Authorization': f'Bearer {self.access_token}'}
        response = requests.put(url, headers=headers)
        if response.status_code == 200:
            messagebox.showinfo("Success", "Ticket marked as resolved")
            self.show_tickets()
        else:
            messagebox.showerror("Error", f"Failed to mark ticket as resolved: {response.status_code}")

    def delete_ticket(self, ticket_id):
        if messagebox.askyesno("Confirm Delete", f"Delete ticket ID {ticket_id}?"):
            url = f"http://ddns.callidos-mtf.fr:8080/tickets/{ticket_id}"
            headers = {'Authorization': f'Bearer {self.access_token}'}
            response = requests.delete(url, headers=headers)
            if response.status_code == 200:
                messagebox.showinfo("Success", "Ticket deleted successfully")
                self.show_tickets()
            else:
                messagebox.showerror("Error", "Failed to delete ticket")
                