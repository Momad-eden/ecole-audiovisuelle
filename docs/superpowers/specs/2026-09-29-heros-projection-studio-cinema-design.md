# Héros « Projection », « Studio » et « Cinéma » — conception

- **Date** : 29/09/2026
- **Décideur** : Momar Diop
- **Périmètre** : sous-projet 1 d'une demande en quatre temps (1. héros · 2. style des titres et textes dans l'admin · 4. passe visuelle · 3. paiement mensuel des étudiants). Les sous-projets 2, 3 et 4 auront leur propre conception.
- **Maquettes validées** : `.superpowers/brainstorm/…/content/` (oeuvre-directions A + C, studio-v2, nouvelle-mise-en-page A).

## 1. Intention

- Le héros « Œuvre d'art » (`masterpiece`, `ArtHero.tsx`) **n'est pas modifié**. Ses défauts relevés (le cadre passe sous la barre de navigation, le titre rempli par la photo est trop basique, pas de vraie image de fond) sont corrigés dans une **nouvelle** mise en page, « Projection ».
- La mise en page « Studio » doit **montrer un studio équipé** et permettre d'**écouter** des productions choisies dans l'admin.
- Une mise en page supplémentaire, **« Cinéma »**, plus professionnelle et accrocheuse, utilisable sur n'importe quelle page.
- Tout se règle dans l'admin, par une personne sans formation technique (CLAUDE.md, principe B).

## 2. Les trois mises en page

### 2.1 Projection (`layout = projection`, nouveau)

- **Fond** : l'image du bloc (`image`) en plein cadre, assombrie (≈ 40 % de luminosité), dégradé vers la nuit en bas pour la lisibilité. Sans image : fond nuit, comme l'Œuvre d'art.
- **Rubans de lumière** : même principe que l'Œuvre d'art (canvas, 6 rubans aux couleurs du logo, déformés vers le pointeur), dessinés en mode `screen` au-dessus de la photo.
- **Poursuite** : autour du pointeur, un disque doux où la photo retrouve luminosité et saturation (masque radial suivant le pointeur). Toute la photo reste visible ; la poursuite ne fait que l'éclairer.
- **Titre** : plein, net, sans remplissage par image. Le champ **« Mot(s) à mettre en couleur »** (`highlight`) colore dans la couleur d'accent la première occurrence de ce texte dans le titre ; absent ou introuvable, le titre reste uni.
- **Cadre** : filet et repères d'angle, qui commencent **sous la barre de navigation** (décalage supérieur égal à la hauteur de l'en-tête) ; ne croise jamais la navigation, sur mobile comme sur ordinateur.
- **Cartel** : conservé (légende `caption`, « Son, lumière et image · EMSI », fréquence sous le pointeur).
- **Son** : bouton « Écouter l'œuvre » identique à l'Œuvre d'art (fichier `sound` en boucle, sinon son synthétique), jamais automatique.
- **Code** : la logique des rubans et du son de `ArtHero.tsx` est extraite dans des modules partagés (`useLightRibbons`, `useArtSound`) utilisés par les deux héros, sans changer le rendu de l'Œuvre d'art.

### 2.2 Studio (`layout = studio`, rendu remplacé)

- **Fond** : la photo du studio (`image`), dégradé sombre à gauche (zone du texte) et en bas (zone du lecteur). Sans photo : fond nuit et l'illustration de console existante (`ConsoleArt`).
- **Voyant REC + timecode** au-dessus du titre (repris de `StageHero`, arrêté hors écran et figé en mouvement réduit).
- **Titre** avec mot en couleur (`highlight`, comme Projection), sous-titre, boutons.
- **Points sur le matériel** (`hotspots`, 0 à 6) : un point lumineux pulsant à la position choisie ; au survol, au focus clavier ou au toucher, une étiquette affiche le libellé. Les points sont des `button` accessibles (nom = libellé). **Sur mobile** (< 640 px), les points restent sur la photo, numérotés, et les libellés s'affichent en liste numérotée sous le texte.
- **Lecteur** (`tracks`, 0 à 3) en bas du héros : bouton lecture, titre, crédit, forme d'onde avec progression et recherche, sélecteur de morceau quand il y en a plusieurs (le premier par défaut). Il s'appuie sur le lecteur global existant (`AudioProvider`, `Waveform`, `peaks`) : la lecture continue dans la barre de lecture si le visiteur fait défiler la page. Rien ne joue automatiquement. Sans morceau, pas de lecteur.
- Le champ « Mots qui défilent » n'est plus proposé pour Studio (il reste pour Scène et Événementiel). Les pages existantes en Studio gardent leurs données ; les mots éventuels sont simplement ignorés.
- **Code** : nouveau composant `StudioHero.tsx` ; `HeroBlock` envoie `studio` vers lui au lieu de `StageHero`. `StageHero` garde `stage` et `events`.

### 2.3 Cinéma (`layout = cinema`, nouveau)

- **Diapositives** (`slides`, 2 à 5) : photo (obligatoire, avec description d'accessibilité), surtitre, titre, lien facultatif (texte + cible). La première diapositive porte le titre de la page (`h1` si le héros est le premier bloc) ; les suivantes sont des `h2` visuellement identiques.
- **Défilement** : fondu enchaîné toutes les 6 s, lent zoom (Ken Burns) sur la photo affichée ; barres de progression cliquables en haut (une par diapositive) ; compteur « 01 / 04 ».
- **Commandes** : pause au survol et au focus, flèches gauche/droite quand le héros a le focus, bouton pause/lecture visible (WCAG 2.2.2). Arrêt hors écran.
- **Mouvement réduit** : ni zoom ni défilement automatique ; les barres restent cliquables.
- **Bandeau bas** : chiffres clés (`facts`, 0 à 4, valeur + libellé) et boutons du bloc. Sur mobile, les chiffres passent en grille 2 × 2 au-dessus des boutons.
- **Images** : seule la première est chargée en priorité ; les autres en différé.
- **Code** : nouveau composant `CinemaHero.tsx`.

### 2.4 Règles communes

- Contraste : les couleurs choisies passent par `accentVars` / le calcul de contraste existant (`lib/contrast`), dans les deux thèmes.
- Animations suspendues hors écran (`IntersectionObserver`), `prefers-reduced-motion` respecté, aucun son automatique.
- Hauteur ≈ 92 svh sur ordinateur, sans débordement horizontal à 360 px.

## 3. Administration (bloc « Grand titre (héros) »)

Liste des mises en page : ajout de
- « Projection (photo de fond, rubans de lumière, cartel) » ;
- « Cinéma (diaporama plein écran et chiffres clés) » ;
- libellé de Studio mis à jour : « Studio (photo, matériel commenté et écoute) ».

Champs ajoutés (visibles seulement pour la mise en page concernée) :

| Champ | Mises en page | Règles |
|---|---|---|
| `highlight` « Mot(s) du titre à mettre en couleur » | projection, studio | 40 caractères ; aide : « doit figurer tel quel dans le titre » |
| `hotspots` « Points sur la photo » | studio | champ sur mesure : on clique sur l'aperçu de la photo pour poser un point, on écrit son libellé (40 car.), on peut le déplacer ou le supprimer ; 6 maximum ; stocké `[{x, y, label}]`, x et y en % (0–100, une décimale). Désactivé tant qu'aucune photo n'est choisie. |
| `tracks` « Morceaux à écouter » | studio | répéteur, 3 maximum : fichier (MP3/M4A/WAV/OGG, 20 Mo), titre (60 car., requis), crédit (80 car.) ; même format que le bloc Audio |
| `slides` « Diapositives » | cinema | répéteur, 2 à 5 : photo + description, surtitre (60), titre (80, requis), texte du lien (30) + lien (`LinkTargets`) |
| `facts` « Chiffres clés » | cinema | répéteur, 4 maximum : valeur (12), libellé (40) |

Champs existants réutilisés : `image` (Projection, Studio), `sound` et `caption` (visibles aussi pour Projection), `accent`, `buttons`, `eyebrow`, `subtitle`. Pour Cinéma, `title` reste requis par le formulaire (titre de la page et repli) et sert de titre à la première diapositive si celle-ci n'en a pas.

## 4. API (`BlockResolver`)

Pour `hero` :
- `tracks` → `[{title, credits, url}]` (comme le bloc Audio) ;
- `slides` → `[{eyebrow, title, image: Image, link: {label, url} | null}]` avec `withImages` ;
- `hotspots` → `[{x, y, label}]`, points hors bornes ou sans libellé écartés ;
- `facts`, `highlight` transmis tels quels.

Types TypeScript (`blocks/types.ts`) : `HeroData.layout` accepte `projection` et `cinema` ; nouveaux champs optionnels `highlight`, `hotspots`, `tracks`, `slides`, `facts`.

## 5. Tests

- **Laravel** (`PublicContentApiTest`) : résolution des morceaux (URL publiques), des diapositives (images), filtrage des points invalides, héros existants inchangés.
- **Unitaires front** (`npm test`) : découpage du titre autour du mot en couleur (présent, absent, casse, espaces insécables de `frenchSpacing`).
- **Playwright** : une commande `php artisan emsi:hero-showcase` crée trois pages publiées `essai-heros-projection`, `essai-heros-studio`, `essai-heros-cinema` (données fictives, images de `public/images/lieux`) et `--remove` les supprime. Elle est lancée par la préparation et le nettoyage de Playwright (`globalSetup` / `globalTeardown`), jamais par `db:seed`, pour ne pas laisser ces pages dans la base locale qui sera transférée en production. Vérifications — Projection : le cadre ne chevauche pas l'en-tête ; Studio : les points ont un nom accessible, le lecteur lance la lecture au clic et pas avant ; Cinéma : flèches du clavier, bouton pause, pas de défilement en mouvement réduit.
- `npm run lint && npm run build`, `php artisan test`, `composer test:mysql`.

## 6. Hors périmètre

- Modifications de l'Œuvre d'art (`masterpiece`).
- Style des titres (couleur, taille) : sous-projet 2.
- Vidéo dans les diapositives Cinéma.
