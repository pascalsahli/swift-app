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
  setzt diese gross in Szene (Startseite, Beitragskopf). Ist kein Beitragsbild
  gesetzt, nimmt das Theme automatisch das **erste Bild aus dem Beitragstext**
  als Vorschaubild – bestehende Beiträge müssen also nicht angefasst werden.
- **Logo/Titel:** Design → Customizer → Website-Informationen
  (Titel „Claudia's Blog“, Untertitel „Meine Geschichten und so vieles mehr“).
- **Sprechende URLs:** Einstellungen → Permalinks → „Beitragsname“ wählen
  (statt der alten `?p=123`-Links) – besser für Leser und SEO.
- **„Über mich“-Seite:** Seiten → Erstellen → Vorlage „Über mich“ wählen
  (Seitenattribute), ein Beitragsbild als Porträt setzen und den Auszug als
  Lead-Satz nutzen.

## E-Mail-Anmeldung (Newsletter)

Besucher können sich mit ihrer E-Mail-Adresse eintragen. Das Anmelde-Band
erscheint automatisch auf der Blog-Übersicht und unter jedem Beitrag. Per
Shortcode lässt es sich überall einfügen:

```
[claudia_subscribe heading="Bleib auf dem Laufenden"]
```

- **Double-Opt-in (DSGVO-konform):** Nach dem Eintragen erhält der Besucher
  eine **Bestätigungs-E-Mail**. Erst nach Klick auf den Link gilt die Anmeldung
  als bestätigt – ungeprüfte Adressen werden nicht als Abonnenten gezählt.
- Die Adressen werden in der Datenbank gespeichert (mit Honeypot-Spamschutz).
- Übersicht & Verwaltung: WordPress-Admin → **Newsletter** (zeigt Status
  „bestätigt“ / „ausstehend“).
- Dort lässt sich die Liste der **bestätigten** Abonnenten als **CSV exportieren**.
- Voraussetzung: WordPress muss E-Mails versenden können (`wp_mail`). Falls die
  Bestätigungsmails nicht ankommen, hilft ein SMTP-Plugin (z. B. WP Mail SMTP).
- Hinweis: Das Theme *sammelt* Adressen. Zum *Versenden* von Newslettern die
  CSV-Liste in einen Dienst importieren oder ein Sende-Plugin (z. B. MailPoet)
  ergänzen.

## Aufbau

```
claudia-theme/
├── style.css            Theme-Header + WP-spezifische Helfer
├── functions.php        Theme-Setup, Assets, Menüs, Lesezeit
├── header.php / footer.php
├── index.php            Blog-Übersicht (mit Featured-Beitrag)
├── single.php           Einzelner Beitrag
├── page.php             Statische Seite
├── template-about.php   Seitenvorlage "Über mich" (Porträt + Bio + Zahlen)
├── archive.php          Kategorie/Tag/Datum-Archive
├── search.php           Suchergebnisse
├── 404.php
├── searchform.php
├── comments.php
├── inc/
│   └── newsletter.php   E-Mail-Anmeldung: Formular, Speicherung, Admin + Export
├── template-parts/
│   ├── content.php      Beitrags-Kachel
│   └── newsletter.php   Anmelde-Band für die Templates
└── assets/
    ├── css/main.css     Das gesamte Design (von Theme + Vorschau genutzt)
    └── js/main.js       Mobiles Menü
```

## Technik

- Klassisches PHP-Theme, kompatibel mit WordPress 6.0+ und PHP 7.4+.
- Responsiv (Desktop, Tablet, Smartphone) mit mobilem Menü.
- Schlank: nur eine CSS-Datei, ein kleines JS, Google Fonts.
- Barrierearm: Skip-Link, ARIA-Attribute, semantisches HTML.
