<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportProduktPreise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-preise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = base_path('produkt_preis.xml');
        if (!file_exists($path)) {
            $this->error("Datei nicht gefunden: {$path}");
            return 1;
        }

        $xml = simplexml_load_file($path);
        $count = 0;

        foreach ($xml->row as $row) {
            $artNr = (string)$row->Artikelnummer;
            $variante = \App\Models\ProduktVariante::where('artikelnummer_full', $artNr)->first();

            if (!$variante) {
                //$this->warn("Variante nicht gefunden: {$artNr}");
                continue;
            }

            // Bestehende Preise löschen für sauberen Re-Import
            \App\Models\ProduktPreis::where('produkt_variante_id', $variante->id)->delete();

            for ($i = 1; $i <= 10; $i++) {
                $qKey = "Quantity_{$i}";
                $pKey = "Quantity_{$i}_Price";
                $pwKey = "Profit_{$i}_Without_Print";
                $ppKey = "Profit_{$i}_Print";

                if (isset($row->$qKey) && !empty((string)$row->$qKey)) {
                    \App\Models\ProduktPreis::create([
                        'produkt_variante_id' => $variante->id,
                        'tier_number' => $i,
                        'quantity' => (int)$row->$qKey,
                        'base_price' => (float)str_replace(',', '.', (string)$row->$pKey),
                        'profit_without_print' => (float)str_replace(',', '.', (string)$row->$pwKey),
                        'profit_print' => (float)str_replace(',', '.', (string)$row->$ppKey),
                    ]);
                }
            }
            $count++;
        }

        $this->info("Import abgeschlossen: {$count} Varianten verarbeitet.");
        return 0;
    }
}
