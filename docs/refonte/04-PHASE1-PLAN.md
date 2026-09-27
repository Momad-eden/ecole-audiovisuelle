# Phase 1 — Correctifs critiques : plan d'implémentation

> **Pour les agents :** exécuter tâche par tâche (skill executing-plans), TDD, un commit par tâche. Cases `- [ ]` pour le suivi.

**Objectif :** corriger, sur la base Blade actuelle, les failles et bugs critiques de l'audit (01-AUDIT) sans anticiper le modèle de données de la Phase 2.

**Architecture :** corrections locales dans Laravel 12 existant (routes, FormRequests, services, migrations réversibles, vues). Aucune nouvelle dépendance Composer ni npm. Chaque correctif est protégé par un test Feature.

**Stack :** Laravel 12, PHPUnit 11, Blade/Alpine, SQLite en mémoire (tests rapides) et MySQL 8.4 en conteneur Docker jetable (tests sur le moteur réel).

**Spec :** `docs/refonte/01-AUDIT.md` (constats), `docs/refonte/03-CONCEPTION.md` §12 (décisions), `docs/refonte/02-PLAN-MIGRATION.md` (Phase 1).

## Contraintes globales

- Ne jamais modifier `.env` ; ne jamais écrire dans la base MySQL `ecole_audiovisuelle` (les tests MySQL utilisent un conteneur jetable).
- Migrations réversibles (`down()` correct) et couvertes par un test.
- Interface en français ; libellés sans jargon.
- Rôles : `directeur`, `gestionnaire`, `secretaire`, `communication`. Les droits de la secrétaire (décision Q-9) arrivent en Phase 3 avec les Policies ; en Phase 1, l'interface ne montre simplement plus de liens interdits.
- Hors périmètre (Phase 2) : normalisation des volets (D8), genre, statuts, montants en entiers (D6), sessions (D9), pièces jointes (D10).
- Formulations : « Certification de niveau BTS (équivalent Bac+2) par la VAE », « Grand Théâtre National Doudou Ndiaye Coumba Rose », « titulaires d'un CPS ou d'un CS ».

## Points de vigilance

1. Données existantes : les numéros de reçu et les matricules déjà en base (`REC-202609-0001`, `EMSI-2026-0002`) ne doivent jamais être réémis par le nouveau générateur de séquences.
2. Un compte au rôle invalide (`admin` en base locale) ou nul ne doit rien voir de l'administration, pas même le tableau de bord.
3. Un bot qui remplit le champ piège doit recevoir une réponse identique à un vrai succès, mais rien n'est enregistré.
4. Supprimer un étudiant ne doit plus jamais supprimer ses paiements ; supprimer une formation ne doit plus supprimer ses candidatures.
5. Une actualité dont on modifie le titre garde son adresse (slug) : les liens partagés ne cassent plus.

---

### Task 1 — Comptes et authentification (S1, S2, S7, S8)

**Fichiers :** `routes/auth.php`, `routes/web.php`, suppression de `RegisteredUserController` et de la vue `auth/register`, nouvelle migration `…_remove_default_role_from_users_table.php`, `database/seeders/DatabaseSeeder.php`, `app/Console/Commands/CreateAdminCommand.php`, `app/Http/Controllers/Admin/UserController.php`, `app/Http/Requests/Admin/UpdateUserRequest.php`. Tests : `tests/Feature/Auth/RegistrationTest.php` (réécrit), `tests/Feature/AccountSecurityTest.php` (nouveau), `tests/Feature/ProfileTest.php`.

- [ ] Tests : `GET /register` et `POST /register` → 404 ; un utilisateur créé sans rôle n'a pas de rôle (`null`) ; un utilisateur au rôle `null` ou `admin` reçoit une 403 sur `/dashboard` ; `DatabaseSeeder` ne crée aucun utilisateur ; `emsi:create-admin` (nom, email, rôle, mot de passe saisi de façon masquée) crée un directeur et refuse l'option `--password` ; un directeur ne peut ni rétrograder son propre rôle ni supprimer ou rétrograder le dernier directeur ; `DELETE /profile` → 404 (plus d'auto-suppression).
- [ ] Vérifier que les tests échouent.
- [ ] Implémenter : retrait des routes et de la vue d'inscription ; migration `users.role` nullable sans défaut (`down()` remet `default('gestionnaire')`) ; middleware `role:directeur,gestionnaire,secretaire,communication` sur `/dashboard` et `/profile` ; commande renommée `emsi:create-admin` (alias `make:admin` conservé) ; garde du dernier directeur dans `UserController@update/destroy` ; retrait de la route `profile.destroy` et du formulaire correspondant.
- [ ] Vérifier que tous les tests passent.
- [ ] Commit `fix(auth): close public registration, remove default role and password`.

### Task 2 — Intégrité des suppressions (D1)

**Fichiers :** migration `…_restrict_deletes_and_add_soft_deletes.php`, modèles `Course`, `Student`, `Payment` (trait `SoftDeletes`). Tests : `tests/Feature/DeletionIntegrityTest.php`.

- [ ] Tests : supprimer un étudiant via `DELETE /students/{id}` le masque (soft delete) et ses paiements restent en base et comptent toujours dans le solde de caisse ; supprimer une formation sans étudiant mais avec candidatures conserve les candidatures (soft delete) ; suppression SQL directe d'une formation ayant des étudiants → exception de contrainte ; migration `down()` puis `up()` sans erreur.
- [ ] Vérifier l'échec.
- [ ] Implémenter : clés étrangères `students.course_id`, `payments.student_id`, `admissions.course_id` en `restrictOnDelete` ; colonne `deleted_at` sur `courses`, `students`, `payments`.
- [ ] Vérifier le succès.
- [ ] Commit `fix(data): restrict destructive cascades and add soft deletes`.

### Task 3 — Séquences transactionnelles (D2, D3)

**Fichiers :** migration `…_create_sequences_table.php`, `app/Services/SequenceService.php` (nouveau), `app/Models/Payment.php`, `app/Services/StudentNumberService.php`. Tests : `tests/Feature/SequenceServiceTest.php`.

**Interfaces :** `SequenceService::next(string $key, ?callable $initial = null): int` — dans une transaction : crée la ligne si elle manque (valeur initiale = `$initial()` ou 0), la verrouille (`lockForUpdate`), l'incrémente et renvoie la nouvelle valeur.

- [ ] Tests : première dépense du mois → `DEP-AAAAMM-0001` même si des reçus REC existent ; REC et DEP numérotés indépendamment ; après suppression d'un paiement, le numéro suivant n'est pas réutilisé ; avec un `REC-202609-0007` existant sans ligne de séquence, le suivant est `REC-202609-0008` ; même logique pour les matricules `EMSI-AAAA-NNNN`.
- [ ] Vérifier l'échec.
- [ ] Implémenter : clés `receipt:{REC|DEP}:{AAAAMM}` et `student:{AAAA}` ; valeur initiale = plus grand suffixe numérique existant.
- [ ] Vérifier le succès.
- [ ] Commit `fix(cash): transactional sequences for receipts and student numbers`.

### Task 4 — Formulaire public de candidature (S3, D10 partiel)

**Fichiers :** `routes/web.php`, `app/Providers/AppServiceProvider.php` (RateLimiter `admissions`), `app/Http/Requests/Public/StorePublicAdmissionRequest.php`, `app/Http/Controllers/Public/AdmissionController.php`, `resources/views/public/admissions/create.blade.php`. Tests : `tests/Feature/PublicAdmissionTest.php`.

- [ ] Tests : 6ᵉ envoi en 10 minutes depuis la même IP → 429 ; champ piège `website` rempli → redirection vers la page de succès et aucune candidature créée ; téléphone `abc` refusé, `+221 77 123 45 67` accepté ; volet hors des deux valeurs du formulaire refusé ; `GET /admission/succes` sans candidature → redirection vers le formulaire ; la liste « Dernier diplôme » propose CPS et CS.
- [ ] Vérifier l'échec.
- [ ] Implémenter : limite de 5 envois par 10 minutes et par IP ; honeypot ; règle téléphone `regex:/^\+?[0-9][0-9 ().-]{7,19}$/` ; volet `Rule::in` des deux valeurs du formulaire public ; garde de session sur la page de succès ; options CPS et CS.
- [ ] Vérifier le succès.
- [ ] Commit `fix(admission): rate limit, honeypot and stricter validation`.

### Task 5 — Caisse (A7, D11)

**Fichiers :** `app/Http/Controllers/Admin/PaymentController.php`, `app/Http/Requests/Admin/StorePaymentRequest.php`, `UpdatePaymentRequest.php`. Tests : `tests/Feature/CashRegisterAndAccountingTest.php`.

- [ ] Tests : la recherche par matricule `EMSI-2026-0001` trouve le paiement de l'étudiant (échoue aujourd'hui aussi sur SQLite) ; une dépense en catégorie `scolarite` est refusée ; un encaissement en catégorie `maintenance` est refusé ; montant > 99 999 999 refusé ; date future refusée ; la modification ne peut pas changer le type d'une écriture.
- [ ] Vérifier l'échec.
- [ ] Implémenter : recherche sur `student_number` ; catégorie validée selon le type (`TransactionCategory::inflowOptions()` et `outflowOptions()`) ; `amount` `integer|min:1|max:99999999` ; `payment_date` `before_or_equal:today` ; `type` en modification limité au type actuel.
- [ ] Vérifier le succès.
- [ ] Commit `fix(cash): search by student number, coherent categories and bounds`.

### Task 6 — Tableau de bord et menu par rôle (S6)

**Fichiers :** `app/Providers/AppServiceProvider.php` (Gates `view-finances`, `manage-admissions`, `manage-content`), `app/Http/Controllers/Admin/DashboardController.php`, `resources/views/admin/dashboard.blade.php`, `resources/views/components/ui/sidebar.blade.php`, `resources/views/components/ui/header.blade.php`. Tests : `tests/Feature/DashboardVisibilityTest.php`.

- [ ] Tests : `communication` ne voit ni « Solde Réel de Caisse », ni « Recouvrement », ni le nom ou le téléphone d'un candidat ; `secretaire` ne voit aucun lien vers un module qui lui renvoie 403 ; `directeur` voit tout ; l'en-tête affiche le libellé du rôle (« Directeur ») sous le nom.
- [ ] Vérifier l'échec.
- [ ] Implémenter : Gates (`view-finances` = directeur, gestionnaire ; `manage-admissions` = directeur, gestionnaire ; `manage-content` = directeur, communication) ; calculs financiers exécutés seulement si autorisé ; sidebar et cartes du tableau de bord pilotées par les Gates ; accessor `User::getRoleLabelAttribute()` via `UserRole::tryFrom()`.
- [ ] Vérifier le succès.
- [ ] Commit `fix(admin): scope dashboard and navigation to user role`.

### Task 7 — Actualités — slug unique et stable (D13)

**Fichiers :** `app/Http/Controllers/Admin/NewsController.php`. Tests : `tests/Feature/NewsSlugTest.php`.

- [ ] Tests : deux actualités de même titre → 2 slugs distincts (`titre`, `titre-2`) sans erreur ; modifier le titre ne change pas le slug.
- [ ] Vérifier l'échec, implémenter `uniqueSlug(string $title): string`, vérifier le succès.
- [ ] Commit `fix(news): unique and stable slugs`.

### Task 8 — Français partout (A8, A5, S5)

**Fichiers :** `config/app.php`, `.env.example`, `lang/fr/{validation,auth,passwords,pagination}.php`, `lang/fr.json` (chaînes Breeze), `resources/views/components/text-input.blade.php`, layouts `guest` et `app` (titre « EMSI »), `resources/views/admin/students/create.blade.php` (affichage des erreurs, A9). Tests : `tests/Feature/FrenchLocaleTest.php`.

- [ ] Tests : message « Le champ prénom est obligatoire. » à l'envoi d'une candidature vide ; date d'actualité « 27 septembre 2026 » ; `/profile` en français (« Informations du profil ») et champs avec une classe de couleur de texte sombre ; titre de `/login` sans « Laravel » ; erreurs visibles sur « Nouvel étudiant ».
- [ ] Vérifier l'échec.
- [ ] Implémenter : `locale` et `fallback_locale` fixés à `fr` (application monolingue, indépendante de `APP_LOCALE`), `faker_locale` `fr_FR` ; fichiers de langue FR avec noms d'attributs (`course_id` → « formation », etc.) ; `.env.example` : `APP_NAME="EMSI"`, `APP_DEBUG=false`, `APP_LOCALE=fr` ; `text-gray-900` sur `text-input`.
- [ ] Vérifier le succès.
- [ ] Commit `fix(i18n): French locale, translations and readable profile form`.

### Task 9 — Contenus fictifs et affirmations non sourcées (01-AUDIT §5.2, §5.4)

**Fichiers :** vues `public/{home,vae,project,about,admissions/create,admissions/success,courses/index}`, `components/public/{header,footer}`, `layouts/public`, `admin/payments/receipt`, `admin/settings/index`. Tests : `tests/Feature/PublicContentIntegrityTest.php`.

- [ ] Test : aucune page publique ni le reçu ne contiennent « BTS d'État », « Diplôme d'État », « d'État » associé au BTS, « 33 800 00 00 », « 33 000 00 00 », « Doudou Ndiaye Rose », « UEMOA », « marchés publics », « double tutelle », « Livret 2 », « FOPICA », « Canal+ », « Réponse sous 48h », « Aucun prérequis » ; le reçu affiche le téléphone des paramètres et la session de l'année du paiement ; le simulateur VAE ne déclare jamais éligible un profil sans CPS/CS.
- [ ] Vérifier l'échec.
- [ ] Implémenter : coordonnées lues dans les paramètres, rien d'affiché si elles sont vides ; formulations validées ; retrait de la section « Garantie légale », de la réponse FAQ sur la valeur juridique et des partenaires non confirmés ; « Livret VAE » unique ; simulateur corrigé (sans CPS/CS → orientation, jamais « éligible ») ; badges « Inscriptions ouvertes » et « Réponse sous 48h » retirés.
- [ ] Vérifier le succès.
- [ ] Commit `fix(content): remove fake contacts and unsourced legal claims`.

### Task 10 — Tests sur MySQL et bruit de dépréciation

**Fichiers :** `config/database.php` (constante `Pdo\Mysql::ATTR_SSL_CA` sous PHP ≥ 8.5), `phpunit.mysql.xml`, `scripts/test-mysql.sh`, `composer.json` (script `test:mysql`), `CLAUDE.md` §7.

- [ ] `scripts/test-mysql.sh` : démarre un conteneur `mysql:8.4` jetable (port 33306, base `emsi_test`), attend qu'il soit prêt, lance `php artisan test --configuration=phpunit.mysql.xml`, supprime le conteneur même en cas d'échec.
- [ ] Vérifier : `php artisan test` (SQLite) et `composer test:mysql` au vert, sans avertissement de dépréciation.
- [ ] Commit `test: run the suite against MySQL in a throwaway container`.

### Clôture de phase

- [ ] `./vendor/bin/pint --test` sur les fichiers modifiés ; suite complète sur SQLite et sur MySQL.
- [ ] Revue de sécurité (skill security-review) et revue de code sur le diff de la phase.
- [ ] Mise à jour de `01-AUDIT.md` (statut « corrigé en Phase 1 » par constat) et rapport de fin de phase. **STOP.**
