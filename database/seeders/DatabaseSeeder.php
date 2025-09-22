<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Mesto;
use App\Models\Dogadjaj;
use App\Models\Kupovina;
use App\Models\Ulaznica;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) MESTA (≥5)
        $mesta = collect([
            ['naziv' => 'Dom omladine',       'adresa' => 'Cetinjska 15',               'grad' => 'Beograd', 'kapacitet' => 2000],
            ['naziv' => 'Beogradska Arena',   'adresa' => 'Bulevar A. Čarnojevića 58',  'grad' => 'Beograd', 'kapacitet' => 18000],
            ['naziv' => 'Sava Centar',        'adresa' => 'Milentija Popovića 9',       'grad' => 'Beograd', 'kapacitet' => 4000],
            ['naziv' => 'SPENS',              'adresa' => 'Sutjeska 2',                 'grad' => 'Novi Sad','kapacitet' => 10000],
            ['naziv' => 'Kombank dvorana',    'adresa' => 'Dečanska 14',                'grad' => 'Beograd', 'kapacitet' => 1800],
        ])->map(fn($m) => Mesto::create($m));

        // helper za brzo uzimanje ID-ja
        $m = fn(string $naziv) => $mesta->firstWhere('naziv', $naziv)->id;

        // 2) DOGAĐAJI (≥5)
        $dogadjaji = collect([
            [
                'naziv' => 'Bajaga i Instruktori - Unplugged',
                'opis'  => 'Akustični koncert legendarnog benda.',
                'mesto_id' => $m('Dom omladine'),
                'datum_pocetka' => '2025-10-05 20:30:00',
                'datum_zavrsetka'=> '2025-10-05 23:00:00',
            ],
            [
                'naziv' => 'Zdravko Čolić - 50 godina',
                'opis'  => 'Jubilarni koncert.',
                'mesto_id' => $m('Beogradska Arena'),
                'datum_pocetka' => '2025-11-12 20:00:00',
                'datum_zavrsetka'=> '2025-11-12 23:30:00',
            ],
            [
                'naziv' => 'Parni Valjak - Akustik',
                'opis'  => 'Veče sa hitovima u akustik aranžmanu.',
                'mesto_id' => $m('Sava Centar'),
                'datum_pocetka' => '2025-12-01 20:00:00',
                'datum_zavrsetka'=> '2025-12-01 22:30:00',
            ],
            [
                'naziv' => 'Stand-up Veče',
                'opis'  => 'Najbolji regionalni komičari.',
                'mesto_id' => $m('Kombank dvorana'),
                'datum_pocetka' => '2025-10-20 20:00:00',
                'datum_zavrsetka'=> '2025-10-20 22:00:00',
            ],
            [
                'naziv' => 'Zoster Live',
                'opis'  => 'Sarajevski bend uživo.',
                'mesto_id' => $m('Dom omladine'),
                'datum_pocetka' => '2025-10-15 21:00:00',
                'datum_zavrsetka'=> '2025-10-15 23:00:00',
            ],
        ])->map(fn($d) => Dogadjaj::create($d));

        $d = fn(string $naziv) => $dogadjaji->firstWhere('naziv', $naziv)->id;

        // 3) KUPOVINE (≥5) — guest checkout (email), različita stanja
        $kupovine = collect([
            ['broj_porudzbine' => $this->ord(), 'email' => 'ana@example.com',   'ukupno' => 0, 'valuta' => 'RSD', 'nacin_placanja' => 'kartica', 'stanje' => 'novo'],
            ['broj_porudzbine' => $this->ord(), 'email' => 'marko@example.com', 'ukupno' => 0, 'valuta' => 'RSD', 'nacin_placanja' => 'kartica', 'stanje' => 'novo'],
            ['broj_porudzbine' => $this->ord(), 'email' => 'ivana@example.com', 'ukupno' => 0, 'valuta' => 'RSD', 'nacin_placanja' => 'paypal',  'stanje' => 'novo'],
            ['broj_porudzbine' => $this->ord(), 'email' => 'milan@example.com', 'ukupno' => 0, 'valuta' => 'RSD', 'nacin_placanja' => 'gotovina','stanje' => 'novo'],
            ['broj_porudzbine' => $this->ord(), 'email' => 'sofia@example.com', 'ukupno' => 0, 'valuta' => 'RSD', 'nacin_placanja' => 'kartica', 'stanje' => 'novo'],
        ])->map(fn($k) => Kupovina::create($k));

        $k = fn(int $idx) => $kupovine[$idx - 1]->id; // $k(1) → id prve kupovine

        // 4) ULAZNICE (≥5) — napravićemo 15 komada (3 po događaju)
        $ulaznicePodaci = [
            // Bajaga (3)
            ['dogadjaj_id' => $d('Bajaga i Instruktori - Unplugged'), 'tip' => 'VIP',     'cena' => 4500, 'sediste' => 'B-12', 'status' => 'dostupna',  'kupovina_id' => null],
            ['dogadjaj_id' => $d('Bajaga i Instruktori - Unplugged'), 'tip' => 'Parter',  'cena' => 2500, 'sediste' => 'P-45', 'status' => 'rezervisana','kupovina_id' => $k(1)],
            ['dogadjaj_id' => $d('Bajaga i Instruktori - Unplugged'), 'tip' => 'Parter',  'cena' => 2500, 'sediste' => 'P-46', 'status' => 'prodata',   'kupovina_id' => $k(2)],

            // Čola (3)
            ['dogadjaj_id' => $d('Zdravko Čolić - 50 godina'),        'tip' => 'VIP',     'cena' => 6000, 'sediste' => 'A-01', 'status' => 'dostupna',  'kupovina_id' => null],
            ['dogadjaj_id' => $d('Zdravko Čolić - 50 godina'),        'tip' => 'Parter',  'cena' => 3200, 'sediste' => 'P-10', 'status' => 'rezervisana','kupovina_id' => $k(3)],
            ['dogadjaj_id' => $d('Zdravko Čolić - 50 godina'),        'tip' => 'Tribina', 'cena' => 2800, 'sediste' => 'T-20', 'status' => 'prodata',   'kupovina_id' => $k(4)],

            // Parni Valjak (3)
            ['dogadjaj_id' => $d('Parni Valjak - Akustik'),           'tip' => 'VIP',     'cena' => 5000, 'sediste' => 'B-01', 'status' => 'dostupna',  'kupovina_id' => null],
            ['dogadjaj_id' => $d('Parni Valjak - Akustik'),           'tip' => 'Parter',  'cena' => 2600, 'sediste' => 'P-30', 'status' => 'rezervisana','kupovina_id' => $k(5)],
            ['dogadjaj_id' => $d('Parni Valjak - Akustik'),           'tip' => 'Parter',  'cena' => 2600, 'sediste' => 'P-31', 'status' => 'prodata',   'kupovina_id' => $k(1)],

            // Stand-up (3)
            ['dogadjaj_id' => $d('Stand-up Veče'),                    'tip' => 'Parter',  'cena' => 1800, 'sediste' => null,   'status' => 'dostupna',  'kupovina_id' => null],
            ['dogadjaj_id' => $d('Stand-up Veče'),                    'tip' => 'Parter',  'cena' => 1800, 'sediste' => null,   'status' => 'rezervisana','kupovina_id' => $k(2)],
            ['dogadjaj_id' => $d('Stand-up Veče'),                    'tip' => 'Parter',  'cena' => 1800, 'sediste' => null,   'status' => 'prodata',   'kupovina_id' => $k(3)],

            // Zoster (3)
            ['dogadjaj_id' => $d('Zoster Live'),                      'tip' => 'Parter',  'cena' => 2400, 'sediste' => null,   'status' => 'dostupna',  'kupovina_id' => null],
            ['dogadjaj_id' => $d('Zoster Live'),                      'tip' => 'Parter',  'cena' => 2400, 'sediste' => null,   'status' => 'rezervisana','kupovina_id' => $k(4)],
            ['dogadjaj_id' => $d('Zoster Live'),                      'tip' => 'VIP',     'cena' => 4200, 'sediste' => 'V-05', 'status' => 'prodata',   'kupovina_id' => $k(5)],
        ];

        foreach ($ulaznicePodaci as $row) {
            Ulaznica::create($row + ['kod' => $this->unikatanKod()]);
        }
    }

    private function ord(): string
    {
        return 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
    }

    private function unikatanKod(): string
    {
        do { $kod = Str::upper(Str::random(10)); }
        while (Ulaznica::where('kod', $kod)->exists());
        return $kod;
    }
}
