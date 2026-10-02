<?php

namespace App\Policies;

use App\Models\User;

/**
 * Traductions anglaises (spec R2 §6) : la direction et la communication relisent et corrigent ;
 * les autres rôles consultent, sauf le commercial (ni liste ni widget). Personne ne crée ni ne
 * supprime une traduction à la main : vider le texte anglais d'une fiche suffit à revenir au français.
 */
class TranslationPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'gestionnaire', 'secretaire', 'communication'];

    protected ?array $create = [];

    protected array $edit = ['directeur', 'communication'];

    protected array $delete = [];

    /** Modifier un texte anglais, le marquer comme relu ou demander une nouvelle traduction. */
    public function manage(User $user): bool
    {
        return $this->allows($user, $this->edit);
    }
}
