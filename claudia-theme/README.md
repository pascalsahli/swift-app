# Claudia Editorial — WordPress-Theme

Ein elegantes, modernes Magazin-Theme für **Claudia's Blog** (Reise- & Lifestyle).
Neutrale, edle Farbpalette (Schwarz/Weiss/Beige + dezenter Goldakzent), grosse
Serif-Typografie und ein vollständig responsives Layout.

## Vorschau ansehen (ohne Installation)

Im Ordner [`../preview/`](../preview/) liegt eine statische HTML-Vorschau.
Einfach `preview/index.html` im Browser öffnen – Startseite und Beitragsseite
(`post.html`) zeigen das Design 1:1 so, wie es später im Blog aussieht.

## Installation in WordPress

1. Diesen Ordner `claudia-theme/` als **ZIP** verpacken
   (z. B. `zip -r claudia-theme.zip claudia-theme`).
2. In WordPress einloggen → **Design → Themes → Neues Theme hinzufügen → Theme hochladen**.
3. Die ZIP-Datei auswählen, **Installieren** und anschliessend **Aktivieren**.

Die bestehenden Inhalte (Beiträge, Bilder, Seiten) bleiben vollständig erhalten –
nur die Optik wird ausgetauscht.

## Nach der Aktivierung empfohlen

- **Menü setzen:** Design → Menüs → ein Menü erstellen und der Position
  „Hauptmenü“ zuweisen.
- **Beitragsbilder:** Pro Beitrag ein „Beitragsbild“ festlegen – das Theme
  setzt diese gross in Szene (Startseite, Beitragskopf).
- **Logo/Titel:** Design → Customizer → Website-Informationen
  (Titel „Claudia's Blog“, Untertitel „Meine Geschichten und so vieles mehr“).
- **Sprechende URLs:** Einstellungen → Permalinks → „Beitragsname“ wählen
  (statt der alten `?p=123`-Links) – besser für Leser und SEO.

## Aufbau

```
claudia-theme/
├── style.css            Theme-Header + WP-spezifische Helfer
├── functions.php        Theme-Setup, Assets, Menüs, Lesezeit
├── header.php / footer.php
├── index.php            Blog-Übersicht (mit Featured-Beitrag)
├── single.php           Einzelner Beitrag
├── page.php             Statische Seite
├── archive.php          Kategorie/Tag/Datum-Archive
├── search.php           Suchergebnisse
├── 404.php
├── searchform.php
├── comments.php
├── template-parts/
│   └── content.php      Beitrags-Kachel
└── assets/
    ├── css/main.css     Das gesamte Design (von Theme + Vorschau genutzt)
    └── js/main.js       Mobiles Menü
```

## Technik

- Klassisches PHP-Theme, kompatibel mit WordPress 6.0+ und PHP 7.4+.
- Responsiv (Desktop, Tablet, Smartphone) mit mobilem Menü.
- Schlank: nur eine CSS-Datei, ein kleines JS, Google Fonts.
- Barrierearm: Skip-Link, ARIA-Attribute, semantisches HTML.
