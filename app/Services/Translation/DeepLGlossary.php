<?php

namespace App\Services\Translation;

use App\Models\Setting;

/** Glossaire FR→EN de l'école (termes à traduire toujours pareil). */
class DeepLGlossary
{
    public function __construct(private DeepLClient $client) {}

    /**
     * @param  array<int, array{fr: ?string, en: ?string}>  $pairs
     * @return string|null identifiant du nouveau glossaire, null s'il n'y a plus de terme
     */
    public function sync(array $pairs): ?string
    {
        $clean = fn (?string $t) => trim(preg_replace('/[\t\r\n]+/', ' ', (string) $t));
        $lines = [];
        foreach ($pairs as $pair) {
            $fr = $clean($pair['fr'] ?? '');
            $en = $clean($pair['en'] ?? '');
            if ($fr !== '' && $en !== '') {
                $lines[] = "$fr\t$en";
            }
        }

        $setting = Setting::current();
        if ($old = $setting->deepl_glossary_id) {
            $this->client->send('DELETE', "/v2/glossaries/$old", allow404: true);
        }

        $id = null;
        if ($lines) {
            $id = $this->client->send('POST', '/v2/glossaries', [
                'name' => 'emsi-'.now()->timestamp,
                'source_lang' => 'fr',
                'target_lang' => 'en',
                'entries' => implode("\n", $lines),
                'entries_format' => 'tsv',
            ])->json('glossary_id');
        }

        $setting->forceFill(['deepl_glossary_id' => $id])->save();

        return $id;
    }
}
