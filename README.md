# Sahli — Website

Moderne, klassisch-seriöse, **zweisprachige (DE/EN)** und **mehrseitige** Website
als Ersatz für die bisherige WordPress-Seite. Reines statisches HTML/CSS/JS —
ohne Build-Schritt, läuft auf jedem Webhosting, GitHub Pages, Netlify usw.

## Struktur

```
index.html        Startseite
about.html        Über
services.html     Leistungen
contact.html      Kontakt
assets/
  css/style.css   Gesamtes Design (Design-Tokens ganz oben in :root)
  js/i18n.js      ⬅ ALLE Texte (Deutsch + English) — hier Inhalte pflegen
  js/main.js      Sprachumschalter, Navigation, Jahreszahl
  img/logo.svg    Logo (Platzhalter-Wortmarke — siehe unten)
CNAME             Domain für GitHub Pages (www.sahli.com)
legacy/           Frühere, themenfremde App (Meeting Notes) — nur archiviert
```

## Inhalte ändern

Alle sichtbaren Texte liegen zentral in **`assets/js/i18n.js`** — jeweils ein
Block für `de` und `en` mit identischen Schlüsseln. Einfach die Strings ersetzen.
Mit `[Platzhalter]` markierte Stellen sind noch zu befüllen.

## Sprache

- Umschaltung über `DE / EN` oben rechts; die Wahl wird pro Besucher gespeichert.
- Standardsprache ist Deutsch (in `assets/js/main.js`, `DEFAULT_LANG`).

## Logo

`assets/img/logo.svg` ist aktuell eine **saubere, optimierte Platzhalter-Wortmarke**.
Sobald das Original-Logo vorliegt, wird es hier ersetzt (idealerweise als SVG,
damit es in jeder Grösse scharf bleibt und sich an die Farben anpasst).

## Lokal ansehen

Datei `index.html` im Browser öffnen — oder ein kleiner Server:

```bash
python3 -m http.server 8000   # dann http://localhost:8000
```

## Deployment (GitHub Pages)

1. Repository-Settings → Pages → Branch wählen.
2. Die `CNAME`-Datei setzt die Domain auf `www.sahli.com`.
3. Beim Domain-Anbieter einen `CNAME`-DNS-Eintrag für `www` auf die
   GitHub-Pages-Adresse setzen (Apex `sahli.com` per A-Records / ALIAS).
