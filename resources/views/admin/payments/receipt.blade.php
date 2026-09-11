<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu {{ $payment->receipt_number ?? 'REC-' . $payment->id }} — EMSI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .receipt-container {
                border: 1px solid #e5e7eb !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900 min-h-screen py-8 px-4 sm:px-6">

    {{-- Barre d'actions (masquée à l'impression) --}}
    <div class="no-print max-w-3xl mx-auto mb-6 flex items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">
                ✓
            </span>
            <span class="text-sm font-semibold text-gray-700">Aperçu du reçu officiel avant impression</span>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('payments.show', $payment) }}"
                class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs transition"
            >
                Retour aux détails
            </a>
            <button
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-emerald-600/20"
            >
                🖨️ Imprimer / Sauvegarder PDF
            </button>
        </div>
    </div>


    {{-- Document du Reçu Officiel --}}
    <div class="receipt-container max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-gray-200 relative overflow-hidden">

        {{-- Filigrane discret --}}
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.025] pointer-events-none select-none">
            <span class="text-9xl font-extrabold uppercase tracking-widest text-black">EMSI</span>
        </div>

        {{-- En-tête de l'École --}}
        <div class="flex flex-col sm:flex-row items-start justify-between gap-6 pb-6 border-b-2 border-gray-900">
            <div class="flex items-center gap-4">
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="EMSI Logo"
                    class="h-16 w-auto object-contain"
                    onerror="this.style.display='none'"
                >
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-gray-900 uppercase">EMSI</h1>
                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider">École des Métiers du Son et de l'Image</p>
                    <p class="text-[11px] text-gray-500 mt-0.5">Grand Théâtre National — Dakar, Sénégal</p>
                    <p class="text-[11px] text-gray-500">Tél : +221 33 000 00 00 | Email : contact@emsi.sn</p>
                </div>
            </div>

            <div class="text-right sm:text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-widest {{ $payment->isInflow() ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                    @if($payment->isInflow())
                        REÇU D'ENCAISSEMENT
                    @else
                        BON DE DÉCAISSEMENT
                    @endif
                </span>
                <p class="text-xs font-mono font-bold text-gray-900 mt-2">
                    N° {{ $payment->receipt_number ?? 'REC-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                </p>
                <p class="text-xs text-gray-500 mt-0.5">
                    Date : <strong class="text-gray-900">{{ $payment->payment_date->format('d/m/Y') }}</strong>
                </p>
            </div>
        </div>


        {{-- Informations Payeur / Bénéficiaire --}}
        <div class="my-6 p-5 rounded-2xl bg-gray-50 border border-gray-200/80">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                @if($payment->student)
                    <div>
                        <span class="text-gray-400 font-semibold uppercase tracking-wider block">Étudiant / Débiteur :</span>
                        <p class="text-base font-bold text-gray-900 mt-0.5">{{ $payment->student->full_name }}</p>
                        <p class="text-gray-500 font-mono">Matricule : {{ $payment->student->matricule ?? 'N/A' }}</p>
                        <p class="text-gray-500">Tél : {{ $payment->student->phone ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span class="text-gray-400 font-semibold uppercase tracking-wider block">Filière / Programme :</span>
                        <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $payment->student->course?->title ?? 'Formation générale' }}</p>
                        <p class="text-gray-500">Session : {{ date('Y') }}-{{ date('Y')+1 }}</p>
                    </div>
                @else
                    <div class="sm:col-span-2">
                        <span class="text-gray-400 font-semibold uppercase tracking-wider block">Bénéficiaire / Motif :</span>
                        <p class="text-base font-bold text-gray-900 mt-0.5">{{ $payment->title ?: 'Opération de caisse' }}</p>
                        <p class="text-gray-500">Catégorie : {{ $payment->category_label }}</p>
                    </div>
                @endif
            </div>
        </div>


        {{-- Tableau du Règlement --}}
        <table class="w-full text-left my-6 border-collapse">
            <thead>
                <tr class="border-b-2 border-gray-300 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <th class="py-2.5">Désignation</th>
                    <th class="py-2.5">Moyen de règlement</th>
                    <th class="py-2.5">Référence</th>
                    <th class="py-2.5 text-right">Montant</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-xs">
                <tr>
                    <td class="py-3 font-semibold text-gray-900">
                        {{ $payment->title ?: ($payment->isInflow() ? 'Versement ' . $payment->category_label : 'Dépense ' . $payment->category_label) }}
                        @if($payment->student && $payment->category === 'scolarite')
                            <span class="block text-[11px] text-gray-500 font-normal">Frais de formation professionnelle</span>
                        @endif
                    </td>
                    <td class="py-3 text-gray-700 font-medium">
                        {{ $payment->payment_method_label }}
                    </td>
                    <td class="py-3 font-mono text-gray-500">
                        {{ $payment->reference ?: '—' }}
                    </td>
                    <td class="py-3 text-right font-mono font-extrabold text-sm text-gray-900">
                        {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-900">
                    <td colspan="3" class="py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-700">
                        Total Versé :
                    </td>
                    <td class="py-3 text-right font-mono font-extrabold text-base text-gray-900">
                        {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            </tfoot>
        </table>


        {{-- Bilan de Scolarité & Reste à Payer (si étudiant) --}}
        @if($payment->student && $studentStats)
            <div class="my-6 p-4 rounded-xl border border-gray-300 bg-gray-50/70 text-xs">
                <div class="grid grid-cols-3 gap-2 text-center divide-x divide-gray-200">
                    <div>
                        <span class="text-gray-500 block">Coût total formation</span>
                        <span class="font-bold text-gray-900 font-mono text-sm">
                            {{ number_format($studentStats['course_price'], 0, ',', ' ') }} F
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Total cumulé réglé</span>
                        <span class="font-bold text-emerald-700 font-mono text-sm">
                            {{ number_format($studentStats['total_paid'], 0, ',', ' ') }} F
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Reste dû après ce versement</span>
                        <span class="font-extrabold font-mono text-sm {{ $studentStats['remaining_due'] > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                            {{ number_format($studentStats['remaining_due'], 0, ',', ' ') }} F
                        </span>
                    </div>
                </div>
            </div>
        @endif


        {{-- Notes éventuelles --}}
        @if($payment->notes)
            <div class="my-4 text-xs text-gray-500 italic bg-gray-50 p-3 rounded-lg border border-gray-200">
                <strong>Note :</strong> {{ $payment->notes }}
            </div>
        @endif


        {{-- Zone des Signatures --}}
        <div class="mt-12 pt-8 border-t border-gray-200 grid grid-cols-2 gap-8 text-center text-xs">
            <div>
                <p class="font-bold uppercase tracking-wider text-gray-700">Signature du Payeur / Bénéficiaire</p>
                <div class="h-20 flex items-end justify-center">
                    <span class="text-[11px] text-gray-400">Lu et approuvé</span>
                </div>
            </div>

            <div>
                <p class="font-bold uppercase tracking-wider text-gray-700">Cachet & Signature de l'Administration</p>
                <div class="h-20 flex flex-col items-center justify-end">
                    <span class="text-[11px] font-semibold text-gray-600">Le Responsable de Caisse</span>
                    <span class="text-[10px] text-gray-400 font-mono">EMSI — Dakar</span>
                </div>
            </div>
        </div>

        {{-- Pied de page légal --}}
        <div class="mt-8 pt-4 border-t border-gray-100 text-center text-[10px] text-gray-400">
            Ce document tient lieu de reçu officiel de paiement délivré par l'École des Métiers du Son et de l'Image (EMSI). Document généré le {{ now()->format('d/m/Y à H:i') }}.
        </div>

    </div>

</body>
</html>
