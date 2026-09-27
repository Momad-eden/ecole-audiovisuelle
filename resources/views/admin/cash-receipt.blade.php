@php
    use App\Enums\CashDirection;
    use App\Support\Money;
    $isIn = $transaction->direction === CashDirection::IN;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isIn ? 'Reçu' : 'Bon de décaissement' }} {{ $transaction->number }} — EMSI</title>
    <style>
        :root { --ink: #1c1917; --muted: #57534e; --line: #d6d3d1; --accent: #b45309; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f5f5f4; color: var(--ink); font: 14px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .toolbar { max-width: 760px; margin: 24px auto 0; display: flex; justify-content: flex-end; gap: 8px; }
        .toolbar button { background: var(--ink); color: #fff; border: 0; border-radius: 8px; padding: 10px 16px; font-weight: 600; cursor: pointer; }
        .sheet { max-width: 760px; margin: 12px auto 32px; background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 40px; position: relative; }
        header { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid var(--ink); padding-bottom: 16px; }
        .school strong { display: block; font-size: 18px; }
        .school span, .muted { color: var(--muted); font-size: 12px; }
        h1 { margin: 0; font-size: 20px; text-transform: uppercase; letter-spacing: .04em; text-align: right; }
        .number { text-align: right; font-weight: 700; color: var(--accent); }
        dl { display: grid; grid-template-columns: 180px 1fr; gap: 6px 16px; margin: 24px 0; }
        dt { color: var(--muted); }
        dd { margin: 0; font-weight: 600; }
        .amount { margin: 24px 0; padding: 16px 20px; border: 1px solid var(--line); border-radius: 10px; display: flex; justify-content: space-between; align-items: center; font-size: 22px; font-weight: 800; }
        .balance { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .balance td { padding: 6px 0; border-bottom: 1px dashed var(--line); }
        .balance td:last-child { text-align: right; font-weight: 700; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-top: 48px; }
        .signatures div { border-top: 1px solid var(--line); padding-top: 8px; color: var(--muted); font-size: 12px; min-height: 80px; }
        .stamp { position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%) rotate(-18deg); border: 4px solid #b91c1c; color: #b91c1c; padding: 8px 24px; font-size: 40px; font-weight: 900; opacity: .25; letter-spacing: .1em; }
        footer { margin-top: 32px; font-size: 11px; color: var(--muted); text-align: center; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { border: 0; margin: 0; max-width: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Imprimer / enregistrer en PDF</button>
    </div>

    <main class="sheet">
        @if ($transaction->isCancelled())
            <div class="stamp" aria-label="Opération annulée">ANNULÉ</div>
        @endif

        <header>
            <div class="school">
                <strong>{{ $settings->school_name }}</strong>
                @if ($settings->address)<span>{{ $settings->address }}</span><br>@endif
                <span>{{ collect([$settings->phone ? 'Tél. '.$settings->phone : null, $settings->email])->filter()->implode(' · ') }}</span>
            </div>
            <div>
                <h1>{{ $isIn ? 'Reçu d\'encaissement' : 'Bon de décaissement' }}</h1>
                <div class="number">N° {{ $transaction->number }}</div>
                <div class="muted" style="text-align:right">Date : {{ $transaction->occurred_on->format('d/m/Y') }}</div>
            </div>
        </header>

        <dl>
            @if ($enrollment)
                <dt>Étudiant</dt><dd>{{ $enrollment->student->full_name }} ({{ $enrollment->student->student_number }})</dd>
                <dt>Formation</dt><dd>{{ $enrollment->offering->label }}</dd>
            @elseif ($transaction->payee)
                <dt>{{ $isIn ? 'Payeur' : 'Bénéficiaire' }}</dt><dd>{{ $transaction->payee }}</dd>
            @endif
            <dt>Objet</dt><dd>{{ $transaction->label }}</dd>
            <dt>Catégorie</dt><dd>{{ $transaction->category->getLabel() }}</dd>
            <dt>Moyen de paiement</dt><dd>{{ $transaction->method->getLabel() }}@if ($transaction->external_reference) — réf. {{ $transaction->external_reference }}@endif</dd>
            @if ($transaction->reverses)
                <dt>Annule l'opération</dt><dd>{{ $transaction->reverses->number }}</dd>
            @endif
        </dl>

        <div class="amount">
            <span>Montant</span>
            <span>{{ Money::fcfa($transaction->amount) }}</span>
        </div>

        @if ($enrollment && $isIn)
            <table class="balance" aria-label="Situation de la scolarité">
                <tr><td>Frais de formation (après remise)</td><td>{{ Money::fcfa($enrollment->amountDue()) }}</td></tr>
                <tr><td>Total déjà réglé</td><td>{{ Money::fcfa($paid) }}</td></tr>
                <tr><td>Reste à payer</td><td>{{ Money::fcfa(max(0, $balance)) }}</td></tr>
            </table>
        @endif

        <div class="signatures">
            <div>{{ $isIn ? 'Le payeur' : 'Le bénéficiaire' }}</div>
            <div>Pour l'école — {{ $transaction->creator?->name ?? 'la caisse' }}</div>
        </div>

        <footer>Document émis par {{ $settings->school_name }} le {{ now()->format('d/m/Y à H:i') }}.</footer>
    </main>
</body>
</html>
