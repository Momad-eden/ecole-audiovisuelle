# EMSI — site public (Next.js)

Musée numérique de l'EMSI : salles, œuvres, formations, Espace Professionnels, candidatures.
Le contenu vient de l'API publique Laravel (`/api/v1/public`), administrée dans Filament (`/admin`).

## Démarrer

```bash
cp .env.example .env.local   # API_URL, MEDIA_URL, NEXT_PUBLIC_SITE_URL, REVALIDATE_SECRET
npm install
npm run dev                  # http://localhost:3000 (Laravel doit tourner sur API_URL)
```

## Points clés

- **Données** : `src/lib/api.ts` (lecture seule, cache étiqueté `content`).
- **Publication** : Laravel appelle `POST /api/revalidate` (secret partagé) à chaque modification ; les pages se régénèrent aussitôt.
- **Blocs** : `src/components/blocks/` — un composant par bloc du constructeur de pages de l'admin (même nom de type).
- **Aperçu** : l'admin ouvre `/api/preview?token=…` (lien signé, 1 h) qui affiche le brouillon sur `/apercu`.
- **Anciennes adresses** : gérées dans l'admin (Site › Redirections), appliquées par la route attrape-tout.
- **Build** : `npm run build` interroge l'API ; Laravel doit être joignable pendant le build.
