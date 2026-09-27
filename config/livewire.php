<?php

/*
 * Réglages Livewire propres au projet (le reste suit les valeurs par défaut du paquet).
 * L'admin accepte des sons jusqu'à 50 Mo et des vidéos jusqu'à 20 Mo : la limite
 * d'envoi temporaire de Livewire (12 Mo par défaut) doit les laisser passer.
 */
return [
    'temporary_file_upload' => [
        'disk' => null,
        'rules' => ['required', 'file', 'max:51200'],
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => ['png', 'gif', 'bmp', 'svg', 'wav', 'mp4', 'mov', 'avi', 'wmv', 'mp3', 'm4a', 'jpg', 'jpeg', 'mpga', 'webp', 'wma'],
        'max_upload_time' => 10,
        'cleanup' => true,
    ],
];
