# Impact Live — studio, événementiel, centre culturel et campus de Saint-Louis

Date : 27/09/2026. Décisions de Momar :
- nom de l'activité : « Impact Live Events » ;
- tarifs affichés « à partir de », prix final sur devis ;
- réservation par demande en ligne, confirmée par l'équipe ;
- l'EMSI Saint-Louis propose les mêmes formations que Dakar, et le candidat choisit son campus.

Toutes les autres décisions ont été prises par Claude et restent révisables.

## 1. Contexte

Boubacar Tall, ingénieur du son sénégalais basé à Saint-Louis, a fondé un ensemble d'activités :
- **l'EMSI**, centre de formation, avec deux campus : Dakar, au Grand Théâtre National, et Saint-Louis ;
- **Impact Live Studio**, studio d'enregistrement à Saint-Louis ;
- **Impact Live Events**, qui loue des sonos de dernière génération, des podiums de spectacle et des lumières aux grands artistes et aux événements (dont le Festival de Saint-Louis), et assure les prestations associées ;
- **l'Espace Habib Faye**, centre culturel privé.

Objectif : en plus des étudiants, les artistes, organisateurs et entreprises doivent pouvoir :
- découvrir le studio, le matériel et les services ;
- consulter des tarifs indicatifs ;
- envoyer une demande de devis ou de réservation.

Le studio doit être un espace d'art qui séduit dès la visite. Tout se gère dans l'admin.

## 2. Données

| Table | Rôle |
|---|---|
| `places` | Lieux : `kind` = `campus`, `studio` ou `cultural_center`. Champs : nom, ville, adresse, téléphone, WhatsApp, e-mail, carte, horaires, description, photo, statut. Sert au choix du campus et aux coordonnées de chaque activité. |
| `services` | Services proposés. `activity` = `studio`, `events` ou `space`. Champs : nom, résumé, description, prix « à partir de » (FCFA entier, facultatif), unité (heure, session, titre, jour, événement), icône, position, publication. |
| `equipment_categories` | Catégories de matériel : sonorisation, lumière, scène et podiums, vidéo, backline… |
| `equipment_items` | Matériel. Champs : catégorie, nom, marque, résumé, caractéristiques (liste libellé → valeur), photos, quantité, prix « à partir de » par jour, `usage` (`rental` = à louer, `studio` = équipement du studio), à la une, position, publication. |
| `rental_packs` | Packs tout compris. Champs : nom, résumé, capacité (« jusqu'à 500 personnes »), contenu (liste), prix « à partir de », image, publication. |
| `events` | Agenda et références. Champs : titre, dates (facultatives pour une référence), lieu (texte ou `place`), activité (`events`, `space`, `studio`, `school`), résumé, contenu, image, lien de billetterie, `is_reference` (prestation réalisée, affichée en portfolio), publication. |
| `booking_requests` | Demandes. Référence `DEM-AAAA-NNNNN`. `type` = `studio_session`, `equipment_rental`, `event_service` ou `space_rental`. Statut : `new` → `quoted` → `confirmed` → `done`, ou `cancelled`. Champs : coordonnées (nom, structure, téléphone, e-mail), dates, lieu, nombre de personnes, message, éléments demandés (JSON : type, id, nom, quantité), notes internes. Chaque changement de statut est historisé. |
| `artworks.origin` | `school` ou `studio` : les productions du studio réutilisent les fiches existantes (audio avec forme d'onde, crédits). |
| `applications.place_id` | Campus choisi par le candidat (obligatoire dès que plus d'un campus est publié). |

Nouveau rôle **`commercial`** : il gère Impact Live (services, matériel, packs, événements, demandes). La communication peut publier les événements et les services ; le directeur a tous les droits.

Toutes les migrations sont réversibles et testées.

## 3. Admin (Filament)

Nouveau groupe **« Impact Live »** :
- Demandes, avec un compteur de nouvelles demandes, des onglets par statut et les actions « Envoyer un devis », « Confirmer », « Marquer réalisée », « Annuler », plus note et historique ;
- Matériel et catégories ;
- Packs ;
- Services ;
- Événements (agenda et références).

« Lieux » rejoint « Administration ». Un tableau de bord « Impact Live » affiche les nouvelles demandes et les événements à venir. Chaque nouvelle demande envoie un e-mail en file d'attente, qui ne fait jamais échouer l'envoi du formulaire.

## 4. Site public

- **Menu** : Univers · Formations · Studio · Events · L'École · Espace Pro + « Candidater ». Réalisations et Espace Habib Faye sont accessibles depuis le pied de page, l'accueil et L'École.
- **Accueil** : nouveau bloc « Un écosystème » avec les quatre activités et le rêve de Boubacar Tall.
- **`/studio`** (couleur signature rouge REC) :
  - héros « régie » : console animée (faders, vumètres, voyant ON AIR) ;
  - services avec prix « à partir de » ;
  - visite des espaces ;
  - équipement du studio en fiche technique ;
  - productions à écouter avec forme d'onde, via le lecteur continu existant ;
  - formulaire « Réserver une session ».
- **`/events`** (couleur signature ambre) :
  - héros « line array » ;
  - prestations, packs, catalogue par catégorie, références, agenda ;
  - demande de devis.
- **`/events/materiel`** et **`/events/materiel/[slug]`** : fiche détaillée (photos, caractéristiques, « à partir de … / jour », bouton « Ajouter à ma demande »). La sélection est gardée dans le navigateur et jointe à la demande de devis.
- **`/agenda`** et **`/agenda/[slug]`** : événements à venir.
- **`/espace-habib-faye`** : page à blocs, sa programmation et une demande de location de salle.
- **Campus** : Dakar et Saint-Louis apparaissent sur L'École, sur Contact et dans le pied de page. Le formulaire de candidature demande le campus.
- **Prix** : `null` s'affiche « Sur devis » ; sinon « à partir de 150 000 FCFA / jour ».

## 5. Contenu initial

Rien n'est inventé.
- **Lieux** : EMSI Dakar (Grand Théâtre), EMSI Saint-Louis, Impact Live Studio (Saint-Louis), Espace Habib Faye (Saint-Louis). Adresses à compléter.
- **Services** : studio (enregistrement, mixage, mastering) ; events (sonorisation, éclairage scénique, podiums et structures de scène) ; espace (location de salle). Aucun prix : ils s'affichent « Sur devis » tant que l'admin n'en a pas saisi.
- **Catégories de matériel** : Sonorisation, Lumière, Scène et podiums.
- **Référence** : Festival de Saint-Louis.
- **Pages** : Studio, Events et Espace Habib Faye, texte d'introduction à valider.

L'upgrade `emsi:site-v2` est étendu, ou une commande `emsi:impact-live` est créée, idempotente.

## 6. Tests

- **Laravel** :
  - migrations (colonnes + `down()`) ;
  - API publique (catalogue, prix, agenda, lieux) ;
  - demande : validation, honeypot, limitation de débit, référence séquentielle, e-mail en file, éléments inconnus refusés ;
  - transitions de statut ;
  - droits du rôle commercial ;
  - campus obligatoire à la candidature ;
  - rendu de tous les écrans admin.
- **Playwright** :
  - visite du studio ;
  - ajout d'un matériel à la demande puis envoi du devis ;
  - agenda ;
  - campus dans la candidature.

## 7. Plus tard (phase B)

- Planning de disponibilité du matériel, avec alerte de double réservation.
- Calendrier des sessions du studio.
- Acompte par paiement mobile.
- Facturation liée à la caisse.
