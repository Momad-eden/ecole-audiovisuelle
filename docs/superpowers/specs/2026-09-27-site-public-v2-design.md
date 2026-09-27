# Site public v2 — « Plein feux »

Date : 27/09/2026. Décisions prises par Claude sur délégation explicite de Momar (« prendre les meilleures décisions pour au final me donner un produit fini »). Toutes restent révisables.

## 1. Intention

- **Objectif premier** : faire découvrir l'EMSI et ses formations, et donner envie de la choisir. Un visiteur comprend ce qu'est l'école en 5 secondes, trouve sa discipline en 2 clics et peut candidater depuis chaque page.
- **Ambition visuelle** : le site est une œuvre, digne d'une école installée au Grand Théâtre National Doudou Ndiaye Coumba Rose et équipée de matériel de dernière génération. Le « musée » était une image pour dire « beau comme un musée », pas une arborescence à imposer.
- **Public** : futurs étudiants (18–30 ans, surtout sur mobile), leurs parents (sérieux, lieu, débouchés), et les professionnels (Espace Pro, inchangé sur le fond).
- **Contraintes** :
  - aucune photo disponible pour l'instant : le design doit être spectaculaire sans photos et accueillir les photos du matériel et des ateliers dès qu'elles arrivent ;
  - tout reste administrable dans Filament ;
  - WCAG AA et `prefers-reduced-motion` ;
  - fluide sur un téléphone moyen, Lighthouse ≥ 90.

## 2. Direction artistique « Plein feux »

Le site est une scène plongée dans le noir qui s'allume à mesure qu'on la parcourt. Les détails graphiques sont empruntés aux outils des métiers enseignés : timecode, point REC, repères de viseur, repères d'impression, faders.

Recherches ui-ux-pro-max retenues :
- motif « Scroll-Triggered Storytelling » : un chapitre par univers, chacun avec sa couleur ; la lecture doit rester complète sans animation ;
- style « Parallax / cinematic » en mode sombre ;
- repères GSAP : une ou deux sections épinglées par page au maximum, et rendu final immédiat en mouvement réduit.

Écartés : les deux premiers systèmes proposés, trop ludiques ou brutalistes, et l'« anti-dark mode ».

| Rôle | Valeur |
|---|---|
| Fond de scène | `#07070A` ; surfaces `#0F0F14`, `#17171E` |
| Texte | `#F5F2EC` ; secondaire `#A7A3B2` (contraste > 7:1) |
| Marque « Projecteur » (orange du logo, avivé) | `#FF7A1A` : boutons, texte `#07070A` |
| Marque « Nuit » (violet du logo, avivé) | `#8B6CFF` |
| Univers Son | `#8B6CFF` (violet) |
| Univers Image | `#3FD0FF` (HMI) |
| Univers Infographie & design | `#FF4FA3` (magenta) |
| Univers Scène | `#FFB020` (ambre) |
| Univers Cinéma (bientôt) | `#FF3B30` (REC) |

Typographies :
- **Archivo** variable, axe de largeur 62–125 : titres géants en largeur étendue, étiquettes en condensé ;
- **Inter** : texte courant ;
- **JetBrains Mono** : timecodes et cartels.

Fraunces est abandonnée : trop « musée classique ».

Signatures visuelles :
- **Hero « scène »** : trois faisceaux de projecteurs animés, dont un « poursuite » qui suit le pointeur. Un titre géant dont le dernier mot change (le son, l'image, la lumière, le design, la scène). Un timecode REC qui défile. Photo ou vidéo optionnelle derrière, en bichromie.
- **Grain pellicule** : grain SVG très léger sur toute la page, en CSS pur.
- **Bandeau défilant** : SON • IMAGE • LUMIÈRE • DESIGN • SCÈNE • CINÉMA.
- **Visuels d'univers** (SVG + CSS, suspendus hors écran, figés en mouvement réduit) :
  - son : spectre et oscilloscope ;
  - image : viseur avec repères, grille des tiers, iris et REC ;
  - design : courbe de Bézier qui se trace, repères d'impression, nuancier CMJN ;
  - scène : faisceaux et faders DMX ;
  - cinéma : pellicule qui défile et clap.
- **En-tête** : transparent sur le hero, opaque au défilement ; sous-menu « Univers » coloré.
- **Pied de page** : grand mot-symbole EMSI en contour.

## 3. Arborescence

| Adresse | Contenu |
|---|---|
| `/` | Accueil (blocs) |
| `/univers` | Les cinq univers |
| `/univers/[slug]` | Un univers : filières, « Vous apprendrez », métiers, formations, réalisations, candidater |
| `/formations` | Toutes les filières regroupées par univers, puis les programmes publiés (école et professionnels) ; toujours visible dans le menu |
| `/ecole` | Page à blocs : histoire, Grand Théâtre, pédagogie, matériel |
| `/realisations` et `/realisations/[slug]` | Anciennement `/musee` et `/musee/oeuvres/[slug]` ; filtre par univers |
| `/professionnels…`, `/actualites…`, `/candidater`, `/contact`, `/expositions…` | Inchangés sur le fond, restylés |

Redirections permanentes dans `next.config.ts` :
- `/musee` → `/realisations` ;
- `/musee/oeuvres/:slug` → `/realisations/:slug` ;
- `/musee/:salle` → `/univers/:salle`, avec les anciens slugs de salles pris en compte.

Menu principal (administrable) : Univers · Formations · L'École · Réalisations · Espace Pro · **Candidater**.

## 4. Données (Laravel)

Les « salles » deviennent des **univers**. Même table `rooms`, même modèle ; dans l'admin, « Univers (disciplines) ».

Nouvelle migration réversible, avec test :
- `rooms.visual` : chaîne `sound | image | design | stage | cinema` ;
- `rooms.is_upcoming` : booléen.

| Univers (slug) | Filières rattachées |
|---|---|
| Son (`son`) | Son |
| Image : vidéo & photo (`image`) | Cadrage sportif et régie vidéo |
| Infographie & design (`design`) | Infographie et création numérique |
| Scène : régie & lumière (`scene`) | Technicien lumière, Régie générale spectacle |
| Cinéma (`cinema`) | Aucune, `is_upcoming` |

API publique :
- `RoomResource` expose en plus `visual`, `isUpcoming` et `tracks`, avec `skills`, `outcomes` et `shortName` ;
- `GET rooms/{slug}` ajoute les programmes publiés qui ont une offre sur une filière de l'univers ;
- le bloc `rooms` inclut les filières.

Blocs de page, ajoutés ou étendus :
- `hero` : nouvelle mise en page `stage` (Scène animée) et champ `words` (mots qui défilent) ;
- `marquee` : bandeau défilant (mots) ;
- `venue` : « Au cœur du Grand Théâtre » (sur-titre, titre, texte, chiffres, image optionnelle) ;
- `equipment` : « Sur quoi vous vous formez » (catégories → éléments, photo optionnelle) ;
- `timeline` : nouvelle mise en page `steps` (parcours en étapes horizontales) ;
- `rooms` : libellé admin « Univers ».

Mise à niveau du contenu : commande `php artisan emsi:site-v2`, idempotente et testée. Elle :
- renomme ou crée les univers et rattache les filières ;
- republie l'Accueil et L'École avec les nouveaux blocs, sans perte : l'ancienne version reste dans l'historique des révisions et l'image du hero est reprise ;
- met à jour les menus principal et pied de page ;
- ne touche ni aux paramètres (logo, coordonnées) ni aux autres pages.

`ContentSeeder` produit directement le contenu v2 pour une installation neuve.

Le contenu est issu du PDF et de la base : filières, compétences, métiers, matériel cité, studio de 154 m², scène live, création en 2016. Rien n'est inventé ; les statistiques non sourcées ne sont pas affichées.

## 5. Front (Next.js)

- **Nouvelles dépendances** : `gsap` et `@gsap/react` (ScrollTrigger). Ils ne sont chargés que par les composants client qui en ont besoin.
- **Performance** :
  - les animations continues sont en CSS ou SVG et suspendues hors écran (IntersectionObserver) ;
  - aucun canvas plein écran ;
  - seule l'image du hero est prioritaire ;
  - polices chargées avec `next/font` (auto-hébergées).
- **Mouvement réduit** : pas d'épinglage ni de défilement horizontal, faisceaux figés, bandeau immobile, titre sans rotation ; toute l'information reste lisible.
- **Univers sur l'accueil** : section épinglée à défilement horizontal sur ordinateur (≥ 1024 px, mouvement autorisé) ; pile verticale sur mobile.

## 6. Tests

- **Laravel** :
  - migration (colonnes + `down()`) ;
  - API univers (`visual`, `isUpcoming`, filières, programmes) ;
  - résolution des nouveaux blocs ;
  - commande `emsi:site-v2` : idempotente, image du hero conservée, révision précédente conservée, univers et filières correctement rattachés ;
  - seeder v2.
- **Playwright**, bureau et mobile :
  - l'accueil présente les univers et « Candidater » ;
  - on ouvre un univers et on voit ses filières ;
  - `/formations` liste les filières ;
  - les redirections `/musee…` fonctionnent ;
  - les parcours existants restent verts.
- **Vérifications** : `pint`, `lint`, `tsc`, `build`, puis captures bureau et mobile relues à l'œil.

## 7. Hors périmètre

- Photos et vidéos réelles, à fournir par l'école.
- Témoignages : aucun contenu disponible ; le bloc `quote` existe déjà.
- Transitions de page animées.
- Contenu du programme Cinéma.
