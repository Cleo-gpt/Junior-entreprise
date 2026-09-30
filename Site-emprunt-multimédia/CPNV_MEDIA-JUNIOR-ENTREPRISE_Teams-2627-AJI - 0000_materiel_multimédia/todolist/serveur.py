#!/usr/bin/env python3
"""Petit serveur local pour cocher les tâches de TODOLIST.md depuis le navigateur.

Lancer :  python3 todolist/serveur.py
Arrêter : Ctrl + C
"""
import json
import os
import re
import sys
import webbrowser
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer

DOSSIER = os.path.dirname(os.path.abspath(__file__))
FICHIER_MD = os.path.join(os.path.dirname(DOSSIER), "TODOLIST.md")
PORT = int(sys.argv[1]) if len(sys.argv) > 1 else 8765

FICHIERS = {
    "/": ("index.html", "text/html; charset=utf-8"),
    "/index.html": ("index.html", "text/html; charset=utf-8"),
    "/style.css": ("style.css", "text/css; charset=utf-8"),
}
CASE = re.compile(r"^(\s*[-*] \[)([ xX])(\])")


def lire_md():
    with open(FICHIER_MD, encoding="utf-8") as f:
        return f.read()


def cocher(numero, texte_attendu, fait):
    """Coche ou décoche la ligne `numero` (à partir de 0) si elle n'a pas changé entre-temps."""
    lignes = lire_md().split("\n")
    if numero < 0 or numero >= len(lignes) or lignes[numero] != texte_attendu:
        return False
    if not CASE.match(lignes[numero]):
        return False
    lignes[numero] = CASE.sub(lambda m: m.group(1) + ("x" if fait else " ") + m.group(3), lignes[numero])
    temporaire = FICHIER_MD + ".tmp"
    with open(temporaire, "w", encoding="utf-8") as f:
        f.write("\n".join(lignes))
    os.replace(temporaire, FICHIER_MD)
    return True


class Gestionnaire(BaseHTTPRequestHandler):
    def repondre(self, code, contenu, type_mime):
        donnees = contenu if isinstance(contenu, bytes) else contenu.encode("utf-8")
        self.send_response(code)
        self.send_header("Content-Type", type_mime)
        self.send_header("Content-Length", str(len(donnees)))
        self.send_header("Cache-Control", "no-store")
        self.end_headers()
        self.wfile.write(donnees)

    def do_GET(self):
        chemin = self.path.split("?")[0]
        if chemin == "/api/todo":
            self.repondre(200, lire_md(), "text/markdown; charset=utf-8")
        elif chemin in FICHIERS:
            nom, type_mime = FICHIERS[chemin]
            with open(os.path.join(DOSSIER, nom), "rb") as f:
                self.repondre(200, f.read(), type_mime)
        else:
            self.repondre(404, "Introuvable", "text/plain; charset=utf-8")

    def do_POST(self):
        if self.path != "/api/cocher":
            self.repondre(404, "Introuvable", "text/plain; charset=utf-8")
            return
        try:
            corps = json.loads(self.rfile.read(int(self.headers.get("Content-Length", 0))))
            ok = cocher(int(corps["ligne"]), str(corps["texte"]), bool(corps["fait"]))
        except (ValueError, KeyError, TypeError):
            self.repondre(400, "Requête invalide", "text/plain; charset=utf-8")
            return
        # 409 : le fichier a changé depuis l'affichage, la page se recharge avec le contenu renvoyé
        self.repondre(200 if ok else 409, lire_md(), "text/markdown; charset=utf-8")

    def log_message(self, *args):
        pass


if __name__ == "__main__":
    serveur = ThreadingHTTPServer(("127.0.0.1", PORT), Gestionnaire)
    adresse = "http://127.0.0.1:%d/" % PORT
    print("Todolist ouverte sur %s (Ctrl + C pour arrêter)" % adresse)
    if "--sans-navigateur" not in sys.argv:
        webbrowser.open(adresse)
    try:
        serveur.serve_forever()
    except KeyboardInterrupt:
        print("\nArrêt.")
