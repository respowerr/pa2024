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
