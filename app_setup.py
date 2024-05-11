import tkinter as tk
from tkinter import ttk, Menu
from ui_screens import UI_Screens

class TicketApp(UI_Screens):
    def __init__(self, root):
        self.root = root
        self.root.title("Système de Ticketing")
        self.root.geometry('900x650')
        self.root.configure(background='#242424') 

        # Configurer la barre de menu ici
        menu_bar = Menu(root, bg='#4C4C4C', fg='white', relief='raised')
        menu_bar.add_command(label="Home", command=self.home_screen)
        menu_bar.add_command(label="Logout", command=self.logout)
        root.config(menu=menu_bar)

        # Super initialise UI_Screens, qui configure le reste
        super().__init__(root)

if __name__ == "__main__":
    root = tk.Tk()
    app = TicketApp(root)
    root.mainloop()
