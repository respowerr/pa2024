import tkinter as tk
from tkinter import ttk, Menu, messagebox

class TicketApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Système de Ticketing")
        self.root.geometry('900x650')
        self.root.configure(background='#242424') 


        style = ttk.Style()
        style.theme_use('clam')
        style.configure('TFrame', background='#242424')
        style.configure('TButton', font=('Arial', 12, 'bold'), background='#5C5C5C', foreground='white')
        style.map('TButton', background=[('active', '#6C6C6C'), ('pressed', '!disabled', '#5C5C5C')], relief=[('pressed', 'sunken'), ('!pressed', 'raised')])
        style.configure('TLabel', font=('Arial', 12), background='#242424', foreground='white')
        style.configure('TEntry', font=('Arial', 12), foreground='black', fieldbackground='white')
        style.configure('TMenu', background='#242424', foreground='white', font=('Arial', 12))


        menu_bar = Menu(root, bg='#4C4C4C', fg='white', relief='raised')
        menu_bar.add_command(label="Home", command=self.home_screen)
        menu_bar.add_command(label="About", command=self.about_screen)
        menu_bar.add_command(label="Help", command=self.help_screen)
        menu_bar.add_command(label="Logout", command=self.logout)
        root.config(menu=menu_bar)

        self.main_frame = ttk.Frame(self.root)
        self.main_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)

        self.login_screen()

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
        ttk.Button(self.main_frame, text="Inscription", command=self.register_screen).pack(pady=10)

    def open_chat_window(self, ticket):
        chat_window = Toplevel()
        chat_window.title(f"Chat for Ticket - {ticket['title']}")
        chat_window.geometry('400x300')
        chat_text = Text(chat_window, state='disabled', wrap='word')
        chat_text.pack(padx=10, pady=10, expand=True, fill='both')
        entry_frame = ttk.Frame(chat_window)
        entry_frame.pack(fill='x', expand=False)
        chat_entry = Entry(entry_frame)
        chat_entry.pack(side='left', fill='x', expand=True)
        send_button = Button(entry_frame, text="Send", command=lambda: self.send_chat_message(chat_entry.get(), chat_text, ticket))
        send_button.pack(side='right')

    def about_screen(self):
        messagebox.showinfo("About", "Ticketing System Version 1.0. Developed with Python's Tkinter library.")

    def help_screen(self):
        messagebox.showinfo("Help", "For help, please contact the system administrator.")

    def register_screen(self):
        for widget in self.main_frame.winfo_children():
            widget.destroy()
        ttk.Label(self.main_frame, text="Nom d'utilisateur:").pack(pady=10)
        username_entry = ttk.Entry(self.main_frame)
        username_entry.pack(pady=10)
        ttk.Label(self.main_frame, text="Mot de passe:").pack(pady=10)
        password_entry = ttk.Entry(self.main_frame, show='*')
        password_entry.pack(pady=10)
        ttk.Label(self.main_frame, text="Rôle (admin/client):").pack(pady=10)
        role_entry = ttk.Entry(self.main_frame)
        role_entry.pack(pady=10)
        ttk.Button(self.main_frame, text="S'inscrire", command=lambda: self.register(username_entry.get(), password_entry.get(), role_entry.get())).pack(pady=20)
        ttk.Button(self.main_frame, text="Retour", command=self.login_screen).pack(pady=10)


    def home_screen(self):
        for widget in self.main_frame.winfo_children():
            widget.destroy()
        ttk.Label(self.main_frame, text="Welcome to the Ticketing System", font=('Helvetica', 18)).pack(pady=40)
        ttk.Button(self.main_frame, text="View Tickets", command=self.view_tickets).pack(pady=10)
        ttk.Button(self.main_frame, text="Logout", command=self.logout).pack(pady=10)

    def view_tickets(self):
        for widget in self.main_frame.winfo_children():
            widget.destroy()
        ttk.Label(self.main_frame, text="Tickets", font=('Arial', 14)).pack(pady=10)
        tickets = [{"title": "Ticket 1", "message": "Issue with login"}, {"title": "Ticket 2", "message": "Cannot print"}]
        for ticket in tickets:
            ticket_button = ttk.Button(self.main_frame, text=f"{ticket['title']} - {ticket['message']}", command=lambda t=ticket: self.open_chat_window(t))
            ticket_button.pack(pady=10)
        ttk.Button(self.main_frame, text="Retour", command=self.home_screen).pack(pady=20)

    def send_chat_message(self, message, chat_text, ticket):
        if message:
            chat_text.config(state='normal')
            chat_text.insert('end', f"You: {message}\n")
            chat_text.config(state='disabled')

    def logout(self):
        self.login_screen()

    def login(self, username, password):
        if username == "admin":
            self.admin_screen()
        else:
            self.home_screen()

    def admin_screen(self):
        for widget in self.main_frame.winfo_children():
            widget.destroy()
        ttk.Label(self.main_frame, text="Admin Dashboard", font=('Helvetica', 18)).pack(pady=40)
        ttk.Button(self.main_frame, text="View All Tickets", command=self.view_tickets).pack(pady=10)
        ttk.Button(self.main_frame, text="Logout", command=self.logout).pack(pady=10)

if __name__ == "__main__":
    root = tk.Tk()
    app = TicketApp(root)
    root.mainloop()
