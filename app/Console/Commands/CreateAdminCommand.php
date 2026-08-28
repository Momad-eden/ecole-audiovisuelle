<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin 
                            {--name= : Nom complet de l\'administrateur}
                            {--email= : Adresse email}
                            {--password= : Mot de passe}
                            {--role=directeur : Rôle (directeur, gestionnaire, secretaire, communication)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer un compte administrateur / utilisateur pour l\'espace de gestion';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('--- Création d\'un compte pour l\'espace Administration EMSI ---');

        $name = $this->option('name') ?: $this->ask('Nom complet');
        $email = $this->option('email') ?: $this->ask('Adresse email');
        $role = $this->option('role') ?: $this->choice(
            'Rôle d\'accès',
            UserRole::values(),
            0
        );

        $password = $this->option('password') ?: $this->secret('Mot de passe (au moins 8 caractères)');

        $validator = Validator::make([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role'     => $role,
        ], [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'string', 'in:' . implode(',', UserRole::values())],
        ]);

        if ($validator->fails()) {
            $this->error('Erreurs de validation :');
            foreach ($validator->errors()->all() as $error) {
                $this->error(' - ' . $error);
            }
            return self::FAILURE;
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => $role,
        ]);

        $this->newLine();
        $this->info(' Compte administrateur créé avec succès !');
        $this->table(
            ['ID', 'Nom', 'Email', 'Rôle'],
            [[$user->id, $user->name, $user->email, $user->role]]
        );
        $this->line('Vous pouvez maintenant vous connecter sur : <fg=yellow>' . url('/login') . '</>');

        return self::SUCCESS;
    }
}
