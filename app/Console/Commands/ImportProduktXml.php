<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportProduktXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-produkte';
    protected $description = 'Importiert Produkte aus der produkt_daten.xml';

    public function handle()
    {
        $xmlPath = base_path('produkt_daten.xml');
        if (!file_exists($xmlPath)) {
            $this->error("Datei nicht gefunden: {$xmlPath}");
            return 1;
        }

        // Farbcodes laden
        $farbcodes = include config_path('farbcodes.php');

        $this->info("Importiere Produkte und Varianten...");
        $xml = simplexml_load_file($xmlPath, 'SimpleXMLElement', LIBXML_NOCDATA);
        $total = count($xml->row);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($xml->row as $row) {
            $fullArtNr = (string)$row->Artikelnummer;
            $parts = explode('-', $fullArtNr);
            $baseArtNr = $parts[0];
            $variantCode = isset($parts[1]) ? $parts[1] : '00';
            $produktName = (string)$row->Produktname;

            // 1. Stamm-Produkt finden oder erstellen
            // Wir gruppieren nach Basis-Artikelnummer AND Produktname
            $produkt = \App\Models\Produkt::updateOrCreate(
                [
                    'base_artikelnummer' => $baseArtNr,
                    'produktname' => $produktName,
                ],
                [
                    'hersteller_id'              => (int)$row->Hersteller ?: null,
                    'produktname_hersteller'    => (string)$row->ProduktnameHersteller,
                    'material'                  => (string)$row->Material,
                    'produktmasse'              => (string)$row->Produktmasse,
                    'gewicht_g'                 => (string)$row->GewichtG,
                    'grammatur'                 => (string)$row->Grammatur,
                    'menge_pro_karton'          => (int)$row->MengeProKarton ?: null,
                    'herkunftsland'             => (string)$row->Herkunftsland,
                    'zolltarifnummer'           => (string)$row->Zolltarifnummer,
                    'mindestmenge'              => (int)$row->Mindestmenge ?: null,
                    'kategorie1'                => (string)$row->Kategorie1,
                    'kategorie2'                => (string)$row->Kategorie2,
                    'kategorie3'                => (string)$row->Kategorie3,
                    'kategorie4'                => (string)$row->Kategorie4,
                    'beschreibung'              => (string)$row->Beschreibung,
                    'lieferzeit_ohne_druck_min' => (int)$row->LieferzeitOhneDruckMin ?: null,
                    'lieferzeit_ohne_druck_max' => (int)$row->LieferzeitOhneDruckMax ?: null,
                    'lieferzeit_mit_druck_min'  => (int)$row->LieferzeitMitDruckMin ?: null,
                    'lieferzeit_mit_druck_max'  => (int)$row->LieferzeitMitDruckMax ?: null,
                    'masse_produktkarton'       => (string)$row->MasseProduktkarton,
                    'bruttogewicht_produktkarton' => (string)$row->BruttogewichtProduktkarton,
                    'preis'                     => $row->Preis ? (float)str_replace(',', '.', (string)$row->Preis) : null,
                    'sale'                      => (string)$row->Sale,
                    'minenfarbe'                => (string)$row->Minenfarbe,
                    'bearbeitungscode'          => (string)$row->Bearbeitungscode,
                    'hinweis'                   => (string)$row->Hinweis,
                    'kapazitaet'                => (string)$row->Kapazitaet,
                ]
            );

            // 2. Variante erstellen
            $farbe = $farbcodes[$variantCode] ?? ((string)$row->Farbe ?: 'Unbekannt');

            \App\Models\ProduktVariante::updateOrCreate(
                ['artikelnummer_full' => $fullArtNr],
                [
                    'produkt_id' => $produkt->id,
                    'farbcode'   => $variantCode,
                    'farbe'      => $farbe,
                    'foto01'     => (string)$row->Foto01,
                    'foto02'     => (string)$row->Foto02,
                    'foto03'     => (string)$row->Foto03,
                    'foto04'     => (string)$row->Foto04,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nImport abgeschlossen.");
        return 0;
    }
}
