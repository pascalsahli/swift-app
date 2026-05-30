# Sahli & Partner — Website

Moderne, klassisch-seriöse, **zweisprachige (DE/EN)** und **mehrseitige** Website
als Ersatz für die bisherige WordPress-Seite. Reines statisches HTML/CSS/JS,
ohne Build-Schritt — läuft auf jedem Webhosting, GitHub Pages, Netlify usw.
Inhalte und Menüstruktur orientieren sich an der bestehenden Seite www.sahli.com.

## Seiten

```
index.html                Startseite — „Angels working for you"
executive-mandates.html   Executive Mandates
financial-mandates.html   Financial Mandates
news.html                 News (Recent Posts)
customers.html            Customers
contact.html              Contact
```

## Aufbau

```
assets/
  css/style.css   Gesamtes Design (Design-Tokens ganz oben in :root)
  js/i18n.js      ⬅ ALLE Texte (Deutsch + English) — hier Inhalte pflegen
  js/main.js      Sprachumschalter, Navigation, Jahreszahl
  img/logo.svg    Logo-Wappen (neu gezeichnet — siehe unten)
CNAME             Domain für GitHub Pages (www.sahli.com)
legacy/           Frühere, themenfremde App (Meeting Notes) — nur archiviert
```

## Inhalte ändern

Alle sichtbaren Texte liegen zentral in **`assets/js/i18n.js`** — je ein Block
für `de` und `en` mit identischen Schlüsseln. Einfach die Strings ersetzen.
Mit `[Platzhalter]` / `[Placeholder]` markierte Stellen sind noch zu befüllen
(aktuell: Kundenliste und die Kontaktangaben Adresse/Telefon).

## Sprache

- Umschaltung über `DE / EN` oben rechts; die Wahl wird pro Besucher gespeichert.
- Standardsprache ist Deutsch (in `assets/js/main.js`, `DEFAULT_LANG`).

## Logo

`assets/img/logo.svg` ist eine **neu gezeichnete, vektorbasierte Interpretation**
des bestehenden Löwen-/Flammen-Wappens (weiß, passt sich per `currentColor` an).
Für ein 100 % originalgetreues Logo bitte die **Originaldatei** (idealerweise SVG,
sonst hochauflösendes PNG) liefern — sie ersetzt dann diese Datei 1:1.

## Lokal ansehen

```bash
python3 -m http.server 8000   # dann http://localhost:8000
```

## Deployment (GitHub Pages)

1. Repository-Settings → Pages → Branch wählen.
2. Die `CNAME`-Datei setzt die Domain auf `www.sahli.com`.
3. Beim Domain-Anbieter `www` per CNAME auf die GitHub-Pages-Adresse zeigen
   (Apex `sahli.com` per A-Records / ALIAS).
