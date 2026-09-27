# 01 — Audit du projet EMSI (état au 27/09/2026)

> **Version 2 — complétée en Phase 0** (branche `refonte/nextjs`).
> La version 1 (audit préliminaire en lecture seule) a été **vérifiée point par point** : chaque constat porte un statut.
> Gravité : 🔴 critique · 🟠 important · 🟡 amélioration — Statut : ✅ confirmé · ✏️ corrigé / précisé · ➕ nouveau constat.

## 0. Méthode de vérification

- Lecture intégrale du code backend (routes, 10 contrôleurs admin, 8 contrôleurs publics, modèles, enums, services, FormRequests, migrations, seeders) et des **92 vues Blade** (publiques, admin, auth, erreurs, composants ; les écrans Breeze auth/profil en survol).
- Application lancée en local (`php artisan serve`) sur une **base SQLite isolée** dans un dossier temporaire (le `.env` et la base MySQL n'ont pas été modifiés), avec un jeu de données de test : 4 comptes (un par rôle), candidatures, étudiant, encaissement et dépense, actualité, vidéo.
- Parcours de **chaque page** dans Chromium sans interface (Playwright) en 1440 px et 375 px : statut HTTP, poids, erreurs console, débordement horizontal, images cassées, titres. Captures dans [`captures/`](captures/).
- Tests d'attaque et de robustesse par requêtes HTTP réelles : inscription publique, accès par rôle, spam du formulaire, validations.
- Suite de tests existante : **63 tests, tous au vert** (62 marqués *deprecated* à cause de `PDO::MYSQL_ATTR_SSL_CA` sous PHP 8.5). ⚠️ Les tests tournent sur SQLite alors que l'environnement local est sur MySQL : cela masque au moins un bug bloquant (voir A7).

## 1. Vue d'ensemble

- Laravel **12.64**, PHP 8.5, Breeze (Blade + Alpine.js + Tailwind 3), `mallardduck/blade-lucide-icons`.
- ✏️ **Base de données** : le `.env.example` pointe sur SQLite, mais le **`.env` local utilise MySQL** (`ecole_audiovisuelle`) ; `database/database.sqlite` est vide. La production est supposée être en MySQL (voir `DEPLOYMENT.md`). La migration cible PostgreSQL devra partir de **MySQL**.
- Données locales actuelles (MySQL) : 1 utilisateur, 6 formations (les 5 filières du projet et « Réalisation Audiovisuelle & Cinéma »), 2 étudiants, 2 candidatures, 1 paiement de 1 000 FCFA, 3 partenaires, 0 actualité, 0 média. Ce sont **des données de test** ; il faut confirmer qu'il n'existe pas de base de production avec des données réelles (voir décisions).
- ➕ Une première tentative de séparation Next.js + API existe **hors de ce dépôt** : `~/EMSI/emsi-frontend` (Next 16, React 19, Tailwind 4) et `~/EMSI/emsi-backend` (Laravel, RBAC Spatie, tables `media`, `academic_years`, `projects`), datée du 19–20 août. Elle n'est pas reliée à ce dépôt (décision à prendre : ignorer ou récupérer des éléments).

## 2. Sécurité

| # | Statut | Constat | Preuve / fichier | Risque |
|---|---|---|---|---|
| S1 | ✅ 🔴 **aggravé** | Inscription publique Breeze active (`GET/POST /register`). **La colonne `users.role` a pour valeur par défaut `gestionnaire`** : tout compte créé depuis `/register` reçoit donc le rôle gestionnaire. | Migration `add_role_to_users_table` (`default('gestionnaire')`) ; test réel : compte créé en 1 requête → accès 200 à `/students`, `/admissions`, `/payments`, `/comptabilite`, `/comptabilite/export` (CSV), `/courses` (création et suppression). | **N'importe quel internaute** lit les données personnelles des candidats et étudiants, exporte la comptabilité, crée ou modifie des paiements et supprime des formations. À corriger **immédiatement**, même avant la Phase 1, si le site est en ligne. |
| S2 | ✅ 🔴 | Seeder admin `admin@emsi.sn` / `password`. | `DatabaseSeeder.php` | Compte devinable. |
| S3 | ✅ 🔴 | Aucune limite de débit ni aucun anti-spam sur `POST /admission`. | 30 envois successifs acceptés (302) avec `phone=abc` et `volet="n'importe quoi"`. | Spam, pollution de la base. |
| S4 | ✅ 🟠 | Autorisations uniquement par middleware de routes, sans aucune Policy. `secretaire` n'a accès qu'au tableau de bord. | `routes/web.php`, `RoleMiddleware` | Voir aussi S6–S8. |
| S5 | ✅ 🟠 | `.env.example` : `APP_DEBUG=true`, `APP_LOCALE=en`. Le `.env` local a les mêmes valeurs et `APP_NAME=Laravel`. | Titre de `/login` : « Laravel — Espace Administration » ; titre de `/profile` : « Laravel ». | Pile d'erreurs exposée si `APP_DEBUG` reste actif en production ; interface partiellement en anglais. |
| S6 | ➕ 🟠 | **Le tableau de bord n'est pas cloisonné par rôle** : « Solde réel de caisse », « Recouvrement scolarités » et « Dernières candidatures » (nom et téléphone) sont visibles par `communication` et `secretaire`. | `admin/dashboard.blade.php` (cartes KPI et tableau hors de toute condition) | Fuite d'informations financières et personnelles. |
| S7 | ➕ 🟠 | Le compte unique de la base MySQL locale a le rôle **`admin`**, absent de l'enum `UserRole`. `RoleMiddleware` compare en strict : ce compte n'accède qu'au tableau de bord. `role` n'est ni casté ni validé en base. | `users.role = 'admin'` (id 47) | Compte principal inutilisable ; aucun garde-fou en base. |
| S8 | ➕ 🟡 | Un directeur peut modifier son propre rôle ou supprimer le dernier autre directeur (seule l'auto-suppression est bloquée). `make:admin --password=…` expose le mot de passe dans l'historique du shell. `ProfileController@destroy` permet à tout compte de se supprimer lui-même. | `UserController@update`, `CreateAdminCommand`, `routes/web.php` | Blocage de l'administration. |
| S9 | ➕ 🟡 | Aucune mention de protection des données personnelles (loi sénégalaise n° 2008-12, CDP) sur le formulaire de candidature ; pas de consentement explicite ni de page « Confidentialité ». | `public/admissions/create.blade.php` | Conformité. |
| — | ✏️ | La commande de création de compte s'appelle **`make:admin`** (et non `emsi:create-admin` comme indiqué dans le plan). | `CreateAdminCommand.php` | — |
| — | ✏️ (faux positif écarté) | Upload de logos SVG : le formulaire annonce le SVG, mais la règle `image` de Laravel le **rejette** (testé) ; pas de faille, seulement une aide trompeuse. | `StorePartnerRequest`, `admin/partners/create` | — |

## 3. Intégrité des données

| # | Statut | Constat | Détail vérifié |
|---|---|---|---|
| D1 | ✏️ 🔴 | Cascades destructrices | La suppression d'une **formation** est bloquée dans le contrôleur si des étudiants y sont inscrits, mais la contrainte en base reste en `cascadeOnDelete`. Surtout : **supprimer un étudiant efface tous ses paiements** (`payments.student_id → cascade`), en un clic, sans avertissement. **Supprimer une formation supprime silencieusement toutes ses candidatures** (`admissions.course_id → cascade`, aussi non nullable). |
| D2 | ✅ 🔴 | Numéro de reçu par `count()+1` | Compteur partagé entre REC et DEP : la 1ʳᵉ dépense du mois a reçu `DEP-202609-0002`. Collision après suppression et risque de concurrence confirmés par lecture du code. |
| D3 | ✅ 🟠 | Matricule `EMSI-YYYY-XXXX` non transactionnel | `StudentNumberService` : lecture puis boucle d'existence, sans verrou. |
| D4 | ✅ 🟠 | Référentiels incohérents | Genre `M/F` (admissions) contre `Homme/Femme` (étudiants, enum `Gender`) ; statuts étudiants accentués (`Diplômé`) ; `role` et `status` non castés. ➕ Le filtre « Statut dossier » de la liste des étudiants propose **« En attente »**, une valeur inexistante, et omet Diplômé et Abandonné. |
| D5 | ✅ 🟡 | `courses.students_count` = capacité | Affiché « 20 places max » par filière (5 × 20 = 100), alors que le PDF prévoit 4 × 10 (Volet 1) et 60 répartis sur 5 filières (Volet 2). |
| D6 | ✅ 🟠 | Montants `decimal(10,2)` | Plafond 99 999 999,99 : un encaissement de 999 999 999 FCFA est accepté par la validation (aucun `max`) → erreur SQL sous MySQL strict. |
| D7 | ✅ 🟠 | Prix 0 = « payé à 100 % » | Démontré : l'étudiant d'une filière à 0 FCFA ayant versé 150 000 FCFA apparaît « Recouvrement 100 % » et « Non réglé » selon l'écran. |
| D8 | ✅ 🔴 **aggravé** | `volet` en texte libre | **Quatre jeux de valeurs différents** coexistent : formulaire public (`Volet 1 — Perfectionnement intensif (3 mois)`), formulaire admin (`Volet 1 — 3 mois intensif (Perfectionnement)`), filtre admin et tableau de bord (`Volet 1 - Formations Pratiques`), fiche formation (`volet1`). Résultat : **les compteurs par volet du tableau de bord et de la fiche formation affichent toujours 0**, le filtre « Volet » ne trouve jamais les candidatures publiques, et **modifier en admin une candidature venue du site efface son volet** (aucune option ne correspond). Le formulaire public **pré-coche Volet 1** pour toute candidature, y compris pour une formation classique de l'école. |
| D9 | ✅ 🔴 | Aucune notion de session ou de cohorte | Le Volet 1 a démarré en septembre 2026, mais le site propose toujours « Postuler au Volet 1 ». Le reçu imprime « Session : {année en cours}-{année+1} », calculée à l'impression et non à la date du paiement. |
| D10 | ✅ 🔴 | Pas de pièces jointes | ➕ Le formulaire ne propose **ni CPS ni CS** dans la liste « Dernier diplôme » (BFEM, Bac, BTS, Licence, Master, Autre), alors que c'est le public cible du programme professionnel. |
| D11 | ➕ 🟠 | Caisse : pas de cohérence type/catégorie | Une **dépense** en catégorie « scolarité », datée de **2031**, de 999 999 999 FCFA, est acceptée. Une écriture peut changer de type après coup en gardant son préfixe REC. Aucun verrouillage, annulation par contre-écriture ni journal d'audit : suppression définitive par le gestionnaire. |
| D12 | ➕ 🟠 | Solde progressif faux sur une période | La comptabilité et l'export CSV repartent de 0 au début de la période filtrée (pas de report à nouveau) : le « solde progressif » d'un mois donné est faux. Les montants exportés sont du texte (`"150 000"`), donc non additionnables dans Excel. |
| D13 | ➕ 🟠 | Actualités : slug non unique et instable | Deux actualités de même titre provoquent une **erreur 500** (vérifié). Le slug est régénéré à chaque modification du titre, ce qui casse les liens déjà partagés (aucune redirection). |
| D14 | ➕ 🟡 | Candidature modifiable seulement si la formation est active | `Admin\AdmissionController@update` refuse une formation inactive, et changer la formation d'une candidature déjà inscrite ne met pas à jour l'étudiant. |

## 4. Performance / architecture

- ✅ `DashboardController`, `PaymentController@accounting`, `StudentController@index` (qui charge **tous** les étudiants deux fois puis pagine en PHP) et `PaymentController@create` (charge tous les étudiants et leurs paiements) : agrégats à déplacer en SQL.
- ✅ N+1 via les accessors `total_paid`, `enrolled_students_count`.
- ✅ `SettingComposer` : 2 requêtes et un `Schema::hasTable` à chaque vue. Les pages d'erreur (500) utilisent le layout public, qui interroge la base : si la base est hors service, la page d'erreur elle-même échoue.
- ✅ Contrôleurs volumineux ; routes admin sans préfixe (`/courses`, `/payments`…) mêlées au site public.
- ✅ `Gallery::getImageUrlAttribute` utilise `asset()`.
- ✅ `DEPLOYMENT.md` et `GUIDE_DEPLOIEMENT.md` sont **strictement identiques** (`cmp`) ; le guide décrit une installation MySQL.
- ➕ 🟠 Poids des pages : `/ecole` **6,3 Mo** (6 photos JPEG de ~1 Mo non optimisées), accueil 2,6 Mo. `hero.jpg` est en réalité un PNG de 699 × 389 px étiré en plein écran. Quatre familles Google Fonts sont chargées (Cinzel, Cormorant, Outfit, Plus Jakarta Sans). Aucune variante d'image générée (WebP/AVIF, tailles).
- ➕ 🟠 `prefers-reduced-motion` n'est respecté **nulle part** ; `/ecole` fait tourner un faux timecode à 24 images/seconde (`setInterval`) en permanence.
- ➕ 🟡 `AboutController` charge partenaires, compteur et galerie que la vue n'utilise pas ; `components/public/news-card` n'est jamais utilisé.

## 5. Frontend / contenu / UX (section complétée)

### 5.1 Positionnement : le projet domine le site (contraire au principe 2 bis-A)

- Le menu principal consacre 2 entrées sur 7 au projet (« Le Projet », « VAE ») ; le méga-menu « Formations » met en avant « Projet Officiel 2026–2027 ».
- Accueil : le 1ᵉʳ bouton du héros est « Découvrir le projet officiel », la 2ᵉ section est entièrement consacrée au projet, et les chiffres du héros (100 jeunes, 5 filières, BTS) sont ceux du projet.
- Le catalogue `/formations` présente les 5 filières du projet comme les formations de l'école, avec une durée de « 3 à 9 mois (Volet 1 & Volet 2 VAE) » et un tarif « Sur demande ». Les **vraies formations de l'EMSI** (CS, BTS spectacle vivant, selon le PDF §8.1) n'apparaissent nulle part.
- `/ecole` ne présente pas l'école : c'est une page sur **le Grand Théâtre** (« Le Grand Théâtre National sous tous ses angles », 1 800 places, cage de 45 m, 12 km de fibre, 6 régies). L'histoire de l'EMSI (créée en 2016), son studio de 154 m² et sa scène live sont absents. ([capture](captures/ecole-desktop.png))
- Positionnement « cinéma » contradictoire avec la réalité (son, lumière, spectacle vivant) : « L'image est un langage », clap de cinéma, « métiers du cinéma et de la télévision », « court-métrage de fin d'études », méta-description de `/formations` listant « Réalisation, Montage, Étalonnage, Photographie ».

### 5.2 Affirmations non sourcées ou contraires au PDF (risque juridique et d'image) 🔴

| Affirmation sur le site | Où | Source PDF |
|---|---|---|
| « **BTS d'État** », « Brevet de Technicien Supérieur d'État », « diplôme strictement identique au BTS de formation initiale », « inscription au registre national » | `/vae`, accueil, `/projet`, formulaire | Le PDF parle de « **Certification de niveau BTS** équivalant à Bac+2 » par la VAE. |
| « Double tutelle ministérielle », « délivrer des diplômes d'État », « Homologation des référentiels par le Ministère de la Formation » | `/ecole`, `/vae` | Non mentionné. |
| « Habilitation à signer les dossiers de sécurité ERP », « accès aux marchés publics », « reconnaissance dans l'espace UEMOA », « grilles indiciaires » | `/vae` | Non mentionné. |
| Livret **1** et Livret **2**, soutenance de **45 min**, validation « sous 7 jours », « jurys blancs », « Tutorat VIP », « accès illimité aux régies » | `/vae` | Le PDF parle d'**un** Livret de compétences VAE, d'un tuteur, d'un jury et d'une soutenance, sans durée. |
| VAE ouverte aux **autodidactes sans diplôme** ; simulateur d'éligibilité qui déclare « Forte éligibilité » tout profil ayant ≥ 1 an d'expérience, **même sans diplôme** | `/vae` (FAQ et simulateur) | Public = titulaires **CPS/CS**, 18–30 ans, sur dossier et entretien. |
| « Compatible avec votre emploi », « horaires aménagés, en partie à distance » | `/vae` | Volet 2 = **30 h/semaine**, alternance intensive. |
| « Aucun prérequis complexe n'est exigé » | `/admission` | Contradictoire avec le public CPS/CS. |
| EMSI « implantée au cœur du Grand Théâtre », « campus », « résidence permanente » | partout | Le Grand Théâtre **met à disposition** ses locaux pour le projet. |
| Équipements et chiffres (4K/6K, DaVinci, Avid, −60 dB, Dolby Atmos, 42 porteuses, 30 t, 12 km de fibre, Unreal, Novastar, XR) | accueil, `/ecole`, `/vae` | Non mentionnés ; à valider ou retirer. |
| Partenaires affichés par défaut : **FOPICA, RTS, Canal+ Afrique**, « Ministère de la Culture » | accueil (repli), `/ecole` | Non mentionnés. De plus, les 6 « logos partenaires » sont **tous des copies du logo EMSI**, et les partenaires sans logo reçoivent ces images au hasard. |
| « Réponse sous 48 h », « Sans frais de dossier », « Inscriptions ouvertes » permanentes, « Promotion 2026 · Inscriptions ouvertes » | formulaire, pied de page, accueil | Engagements codés en dur, jamais mis à jour. |
| 90 % de pratique pour tout (et **80 %** plus bas sur la même page d'accueil) | accueil, cartes formations | 90 % = Volet 1 seulement ; Volet 2 ≈ 67 %. |

### 5.3 Incohérences de nommage

- **Nom du lieu** : « Grand Théâtre National Doudou Ndiaye **Rose** » (en-tête, pied de page, accueil) contre « Doudou Ndiaye **Coumba** Rose » (PDF, `/projet`, `/ecole`).
- **Nom de l'école** : « École de Formation Audiovisuelle » (titre de l'accueil, défaut des paramètres, meta) contre « École des Métiers du Son et de l'Image » ; le projet voisin `~/EMSI` dit « École des Métiers de **l'Image et du Son** ».
- **Filières** : au moins 5 appellations pour les mêmes 5 filières (seeder, `/vae` « BTS 04 • Motion & XR — Régie d'écrans LED », simulateur « BTS Cadrage & Réalisation Broadcast », étiquettes du héros « Infographie 2D/3D », `/vae` « Régie Lumière & Scénographie » contre « Technicien Lumière » dans le PDF).
- **Appel à l'action** : « Admission », « Candidater en ligne », « Déposer ma candidature », « Déposer mon dossier VAE », « Postuler au Volet 1 », « Commencer ma candidature » ; `/candidater` redirige vers `/admission` (sens inverse de la cible).
- Rôles dans l'en-tête admin : `role_label` n'existe pas, le rôle brut s'affiche (« Directeur • Directeur »).

### 5.4 Faux contenus et coordonnées codées en dur 🔴

- Téléphone **`+221 33 800 00 00`** et email `contact@emsi.sn` affichés en repli sur `/vae` et `/ecole` quand les paramètres sont vides (le cas en base : email nul). Le bouton « Appeler le service admissions » du formulaire compose **en dur** `tel:+221338000000`.
- Le **reçu officiel imprimé** affiche en dur « Tél : +221 33 000 00 00 | Email : contact@emsi.sn », sans lire les paramètres.
- Paramètres locaux : Instagram = `https://zayan-sn.com` (lien erroné) ; nom de l'école = valeur par défaut.
- Accueil : les 3 « photos réelles » `institution-1/2/3.jpg` sont des **doublons octet pour octet** de `ecole.jpg`, `hero.jpg` et `grand-theatre.jpg`. L'image `objects/camera.png` contient un **damier de fausse transparence incrusté** ; les autres objets ont un fond blanc opaque. ([capture](captures/accueil-desktop.png))

### 5.5 Navigation, liens et parcours

- Pas de page **Contact** (`/contact` → 404) ; « Devenir partenaire » renvoie vers `/ecole`. « Plan d'accès » pointe vers `/ecole#contact` (l'ancre existe).
- `/admission/succes` est accessible directement, sans candidature.
- Paramètres ignorés : `?specialite=` (simulateur VAE) n'est pas repris par le formulaire. Les boutons « Admission » des cartes du catalogue ne transmettent pas la formation choisie (seule la fiche détail le fait).
- Le méga-menu n'affiche que 5 formations (`take(5)`), dans l'ordre alphabétique ; les icônes sont choisies en devinant le titre (`str_contains`). Il s'ouvre **au survol seulement** (inaccessible au clavier).
- Filtre par catégorie de `/formations` : chaque formation a sa propre catégorie, donc 5 boutons d'un élément chacun. Le filtre « niveau » existe côté serveur mais pas dans l'interface. « Formations similaires » est toujours vide (catégories uniques).
- Fiche formation : **seule la description** est affichée ; pas de programme, compétences, débouchés, prérequis, calendrier, équipements, FAQ ni réalisations. Le PDF fournit pourtant tout ce contenu (§4.2, §6).
- Galerie : aucune pagination (tout est chargé) ; les vignettes sont des `<article>` cliquables, inaccessibles au clavier ; YouTube est lancé en `autoplay=1` à l'ouverture (acceptable après clic).

### 5.6 Formulaire de candidature public

- Un seul formulaire pour deux publics (école et professionnels) ; volet imposé par défaut (voir D8) ; pas de CPS/CS (D10) ; pas de pièces jointes ; téléphone non validé (« abc » accepté) ; volet en texte libre ; aucun e-mail de confirmation ; aucun consentement (S9).
- **Messages de validation en anglais** (« The course id field is required. », « The email field must be a valid email address. ») : `APP_LOCALE=en` et aucun dossier `lang/fr`.
- Dates publiques en anglais : « Publié le 27 **September** 2026 ».

### 5.7 Actualités

- Le contenu est affiché avec `nl2br(e(...))` : aucune mise en forme possible (pas de lien, gras ni intertitre), et du HTML saisi s'affiche brut (`<p>Contenu test</p>` visible). Accueil et cartes font `strip_tags`, ce qui suppose du HTML : incohérent.
- Chaque article affiche le libellé « Grand Théâtre National » en dur.

### 5.8 Espace admin

- **Non responsive** : sur mobile, le menu latéral fixe de 256 px écrase le contenu en colonne d'un caractère, sans bouton de menu. ([capture](captures/admin-dashboard-mobile.png))
- `/profile` : écran Breeze d'origine **en anglais**, logo Laravel, style différent, et **texte des champs blanc sur blanc** (valeurs présentes mais invisibles, couleur calculée `rgb(255,255,255)` sur fond blanc). ([capture](captures/admin-profil-texte-invisible.png))
- Sous-titre de page : « Tableau de bord » s'affiche comme titre sur **toutes** les pages (Admissions, Dossier…). Balise `<title>` générique (« Administration — EMSI ») sur 11 écrans ; deux `<h1>` par page.
- Menu latéral : le rôle `secretaire` voit « Étudiants » et « Admissions », qui renvoient une 403. Le tableau de bord montre à tous les rôles des cartes d'accès vers des modules interdits (403).
- Formulaire « Nouvel étudiant » : **aucune erreur de validation affichée** (l'utilisateur soumet et rien ne semble se passer). `courses/create` contient une `<img src="">` cassée.
- Jargon et incohérences comptables : « Recette (Crédit) / Dépense (Débit) » (inversé par rapport au journal de caisse, où un encaissement est un débit du compte caisse), « Grand Livre », « Résultat net d'exploitation », « Écriture ». À simplifier pour un non-informaticien.
- Un solde de caisse négatif (−999 899 999 F) s'affiche sans aucune alerte. ([capture](captures/admin-dashboard.png))
- Formulaires create/edit dupliqués (confirmé) ; libellés de volet codés en dur dans les vues.
- Aucune aide contextuelle, aucune confirmation d'action destructive homogène, aucun aperçu avant publication, aucun brouillon hors actualités, aucune médiathèque, aucun historique.

### 5.9 Incohérences du document source (PDF) à corriger avant de reprendre le contenu

| # | Incohérence | Emplacement |
|---|---|---|
| P1 | Élision fautive « l'Grand Théâtre » (une dizaine d'occurrences) ; « séquententiels ». | passim |
| P2 | « **cinq filières** » suivies d'une liste de **quatre** (Technicien Lumière manquant). | Résumé exécutif |
| P3 | Sigle alternant **CPS** (Certificat de Professionnalisation Spécialisée) et **CS** (Certificat de Spécialité) pour le même public. | §1, §3, §4.1, §5 |
| P4 | **Budget faux** : la colonne Volet 2 totalise **92,5 M** et non 47,5 M ; la ligne « Rémunérations » donne 8 + 45 = 53 M et non 63 M ; la somme de la colonne Total donne 130,8 M et non 75,8 M. Seule la colonne Volet 1 (28,3 M) est juste. | Tableau 3 |
| P5 | Restes d'une version **Saint-Louis** : métadonnées « EMSI & Saint-Louis Jazz », « Saint Louis Jazz », « tissu culturel saint-louisien », « élite technique saint-louisienne », « exode vers Dakar », « centralisation dakaroise », « l'excellence n'est pas l'apanage de la capitale », « Sextan » (budget). | §5.2, §7.2, §10, §14 |
| P6 | Remplacements automatiques ratés : « Dakar — Dakar », « Dakar + Dakar », « Transport (Dakar/Dakar) », « du Grand Théâtre … et du Grand Théâtre … » (lieu cité deux fois), « Centre Grand Théâtre », « experts internationaux **de experts techniques mobilisés par l'EMSI Dakar** » (nom du partenaire international effacé). | passim |
| P7 | Certification Volet 1 : « Diplôme d'**École** » (texte) contre « Diplôme d'**État** » (Tableau 1). | §5.1 vs Tableau 1 |
| P8 | Durée de présence des experts : « deux mois » contre 12 semaines. | §5.1, §8.2 |
| P9 | Volet 2 : « 2 cohortes » (Tableau 1) mais un seul cycle février–octobre au calendrier ; répartition des 60 entre filières non précisée. | Tableau 1, §9 |
| P10 | Indicateurs contradictoires : « 100 % de réussite VAE » contre « ≥ 80 % certifiés ». | Tableau 4 |
| P11 | Phrase tronquée : « Enfin, une ambition territoriale » (sans suite). | Résumé |
| P12 | Titres du sommaire différents des titres du corps (§2.1, §2.2 fondu dans un paragraphe) ; pagination à « 1 » sur toutes les pages. | Sommaire |
| P13 | Liste d'événements redondante ; « Festival **Bienal** de Dakar » (le nom officiel est « Biennale de Dakar / Dak'Art »). | passim |
| P14 | Ministères associés différents entre §8.2 (Culture, Formation professionnelle, COJO) et la conclusion (Culture et **Jeunesse**). | §8.2 vs §14 |
| P15 | EMSI « créée en 2016 » mais « expérience pédagogique reconnue depuis 2018 ». | §8.1 |
| P16 | « thermodynamique » dans le socle transversal (terme vraisemblablement erroné). | §6.1, §6.4 |

## 6. Priorités recommandées (mises à jour)

1. 🔴 S1 et S2 en tête de Phase 1. Le site n'est pas en ligne (réponse de Momar du 27/09/2026) : pas de correctif d'urgence.
2. 🔴 Phase 1 : S1–S3, S6, D1, D2, D11 (verrouillage caisse), D13, bug de recherche de la caisse (A7 ci-dessous), locale FR et messages de validation, retrait des coordonnées et affirmations fausses les plus graves (5.2, 5.4).
3. 🔴 Phase 2 : D4–D10 (modèle de données, sessions, volets, pièces jointes), PostgreSQL.
4. 🟠 Phase 3 : API, Policies, agrégats SQL, tests sur le **même moteur que la production**.
5. 🟠 Phases 4–5 : site musée et admin Next.js.

### Annexe A — Bugs fonctionnels reproduits

| # | Bug | Reproduction | Gravité |
|---|---|---|---|
| A1 | Inscription publique → accès gestionnaire | S1 | 🔴 |
| A2 | Compteurs par volet toujours à 0 ; filtre « Volet » inopérant ; volet effacé à l'édition | D8 | 🔴 |
| A3 | Erreur 500 : deux actualités de même titre | D13 | 🟠 |
| A4 | Numérotation REC/DEP partagée | D2 | 🟠 |
| A5 | Texte invisible sur `/profile` | 5.8 | 🟠 |
| A6 | Admin inutilisable sur mobile | 5.8 | 🟠 |
| A7 | **Recherche de la caisse → erreur 500 sous MySQL** : `orWhere('matricule', …)` porte sur une colonne inexistante (`matricule` est un accessor). SQLite, utilisé par les tests, tolère l'erreur, ce qui la masque. Reproduit sur la base MySQL locale, en lecture seule : `SQLSTATE[42S22] Unknown column 'matricule'`. | `PaymentController@index` | 🔴 |
| A8 | Messages de validation et dates en anglais | 5.6 | 🟠 |
| A9 | Formulaire « Nouvel étudiant » sans affichage d'erreurs | 5.8 | 🟡 |
| A10 | Dépense catégorisée « scolarité », date future, montant hors limite acceptés | D11, D6 | 🟠 |

## 7. Suivi des corrections — Phase 1 (27/09/2026)

Plan : [`04-PHASE1-PLAN.md`](04-PHASE1-PLAN.md). Suite de tests : **105 tests verts sur SQLite et sur MySQL 8.4** (`php artisan test`, `composer test:mysql`).

| Constat | Statut | Correctif |
|---|---|---|
| S1 inscription publique | ✅ corrigé | `/register` supprimé ; `users.role` sans valeur par défaut ; tableau de bord et profil exigent un rôle valide |
| S2 mot de passe par défaut | ✅ corrigé | le seeder ne crée aucun compte ; `emsi:create-admin` (alias `make:admin`) sans option `--password` |
| S3 spam du formulaire | ✅ corrigé | 5 envois / 10 min / IP, champ piège, téléphone et volet validés |
| S5 défauts `.env.example` | ✅ corrigé | `APP_NAME=EMSI`, `APP_DEBUG=false`, locales `fr` (le `.env` local reste à ajuster à la main) |
| S6 tableau de bord non cloisonné | ✅ corrigé | Gates `view-finances`, `manage-admissions`, `manage-content` ; plus aucun lien vers une 403 |
| S7 rôle invalide | ✅ corrigé | un rôle nul ou inconnu n'ouvre plus l'administration (le compte `admin` de la base locale doit être recréé via `emsi:create-admin`) |
| S8 comptes | ✅ corrigé | pas d'auto-rétrogradation du directeur ; auto-suppression du profil retirée |
| S4 Policies | ⏳ Phase 3 | — |
| D1 cascades | ✅ corrigé | `restrictOnDelete` + suppression douce (formations, étudiants, paiements) |
| D2 / D3 numérotation | ✅ corrigé | table `sequences` verrouillée ; REC et DEP indépendants ; reprise des numéros existants |
| D11 caisse | ✅ partiel | catégorie cohérente avec le type, montant borné, date non future, type figé ; contre-écritures et clôture en Phase 2 |
| D13 slug d'actualité | ✅ corrigé | slug unique et stable |
| A7 recherche de la caisse | ✅ corrigé | recherche sur `student_number` ; vérifiée sur MySQL |
| A5 / A8 / A9 | ✅ corrigé | français partout (validation, dates, profil lisible), erreurs visibles sur « Nouvel étudiant » |
| §5.2 / §5.4 affirmations et coordonnées | ✅ corrigé | formulations du PDF, simulateur VAE limité aux CPS/CS, reçu relié aux paramètres ; test de non-régression `PublicContentIntegrityTest` |
| D4–D10, D12, D14 | ⏳ Phase 2 | modèle de données |
| Performance (§4), admin mobile, poids des pages | ⏳ Phases 3 à 5 | — |
