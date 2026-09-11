<?php

namespace App\Enums;

enum TransactionCategory: string
{
    // Encaissements / Recettes (Inflows)
    case SCOLARITE = 'scolarite';
    case INSCRIPTION = 'inscription';
    case PRESTATION = 'prestation';
    case VENTE_MATERIEL = 'vente_materiel';
    case AUTRE_RECETTE = 'autre_recette';

    // Décaissements / Dépenses (Outflows)
    case ACHAT_MATERIEL = 'achat_materiel';
    case INTERVENANT = 'intervenant';
    case MAINTENANCE = 'maintenance';
    case LOYER_CHARGES = 'loyer_charges';
    case LOGISTIQUE = 'logistique';
    case COMMUNICATION = 'communication';
    case AUTRE_DEPENSE = 'autre_depense';

    public function label(): string
    {
        return match ($this) {
            self::SCOLARITE => 'Paiement de scolarité',
            self::INSCRIPTION => 'Frais d\'inscription / dossier',
            self::PRESTATION => 'Prestation audiovisuelle / Location studio',
            self::VENTE_MATERIEL => 'Vente de matériel / supports',
            self::AUTRE_RECETTE => 'Autre encaissement',

            self::ACHAT_MATERIEL => 'Achat matériel & consommables',
            self::INTERVENANT => 'Honoraires formateurs & intervenants',
            self::MAINTENANCE => 'Maintenance & réparations régie',
            self::LOYER_CHARGES => 'Loyer & charges d\'exploitation',
            self::LOGISTIQUE => 'Transport & logistique tournages',
            self::COMMUNICATION => 'Marketing & communication',
            self::AUTRE_DEPENSE => 'Autre dépense de fonctionnement',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function inflowOptions(): array
    {
        return [
            self::SCOLARITE->value => self::SCOLARITE->label(),
            self::INSCRIPTION->value => self::INSCRIPTION->label(),
            self::PRESTATION->value => self::PRESTATION->label(),
            self::VENTE_MATERIEL->value => self::VENTE_MATERIEL->label(),
            self::AUTRE_RECETTE->value => self::AUTRE_RECETTE->label(),
        ];
    }

    public static function outflowOptions(): array
    {
        return [
            self::ACHAT_MATERIEL->value => self::ACHAT_MATERIEL->label(),
            self::INTERVENANT->value => self::INTERVENANT->label(),
            self::MAINTENANCE->value => self::MAINTENANCE->label(),
            self::LOYER_CHARGES->value => self::LOYER_CHARGES->label(),
            self::LOGISTIQUE->value => self::LOGISTIQUE->label(),
            self::COMMUNICATION->value => self::COMMUNICATION->label(),
            self::AUTRE_DEPENSE->value => self::AUTRE_DEPENSE->label(),
        ];
    }

    public static function allOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}
