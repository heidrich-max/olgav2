<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportProduktDruck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-druck';

    protected $description = 'Importiert Druckpositionen aus produkt_druck.xml';

    public function handle()
    {
        $xmlPath = 'd:/Projekte/Olgav2/produkt_druck.xml';
        if (!file_exists($xmlPath)) {
            $this->error("Datei nicht gefunden: $xmlPath");
            return;
        }

        $this->info("Importiere Druckdaten...");
        $xml = simplexml_load_file($xmlPath);
        $total = count($xml->row);
        $bar = $this->output->createProgressBar($total);

        foreach ($xml->row as $row) {
            $artNrFull = (string)$row->Artikelnummer;
            // Suche die Variante anhand der vollständigen Artikelnummer
            $variante = \App\Models\ProduktVariante::where('artikelnummer_full', $artNrFull)->first();

            if ($variante) {
                // Bestehende Positionen löschen für sauberen Re-Import
                $variante->druckpositionen()->delete();

                // Es gibt Felder für bis zu 6 Positionen
                for ($i = 1; $i <= 6; $i++) {
                    $posField = "Position_{$i}_PrintPosition";
                    $sizeField = "Position_{$i}_PrintSize";
                    
                    if (!empty((string)$row->$posField)) {
                        $techniques = [];
                        for ($t = 1; $t <= 6; $t++) {
                            $techField = "Position_{$i}_PrintTech_{$t}";
                            if (!empty((string)$row->$techField)) {
                                $techniques[] = (string)$row->$techField;
                            }
                        }

                        \App\Models\ProduktDruckPosition::create([
                            'produkt_variante_id' => $variante->id,
                            'position_name' => (string)$row->$posField,
                            'print_size' => (string)$row->$sizeField,
                            'techniques' => $techniques
                        ]);
                    }
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nImport der Druckdaten abgeschlossen.");
    }
}
