import tkinter as tk
from ui_screens import UI_Screens

def main():
    root = tk.Tk()
    app = UI_Screens(root)
    root.mainloop()

if __name__ == "__main__":
    main()
