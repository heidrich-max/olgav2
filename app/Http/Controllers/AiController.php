<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function ask(Request $request)
    {
        $prompt = $request->input('prompt');
        $manufacturerId = $request->input('manufacturer_id');
        
        $manufacturer = null;
        if ($manufacturerId) {
            $manufacturer = DB::table('hersteller')->where('id', $manufacturerId)->first();
        }

        $apiKey = config('services.openai.key');
        
        if (!$apiKey) {
            return response()->json(['error' => 'OpenAI API Key nicht konfiguriert.'], 500);
        }

        $client = new Client();
        
        $systemPrompt = "Du bist ein hilfreicher KI-Assistent für die Firmen Frank Group (Branding Europe GmbH / Europe Pen GmbH). 
        Du hilfst dem Nutzer bei der Verwaltung von Herstellern.";

        if ($manufacturer) {
            $systemPrompt .= " 
            Hier sind die Daten des aktuellen Herstellers:
            Name: {$manufacturer->firmenname}
            Nummer: {$manufacturer->herstellernummer}
            Ansprechpartner: {$manufacturer->anrede} {$manufacturer->vorname} {$manufacturer->nachname}
            Telefon: {$manufacturer->telefon}
            Email: {$manufacturer->email}
            Zusatzinfo: {$manufacturer->herstellerinformation}";
        } else {
            // GLOBALE SUCHE: Wir suchen nach Keywords im Prompt des Nutzers
            // Wir filtern Stop-Wörter und suchen in firmenname und herstellerinformation
            $keywords = explode(' ', str_replace(['?', '!', '.', ','], '', $prompt));
            $searchQuery = DB::table('hersteller');
            
            foreach ($keywords as $word) {
                if (strlen($word) > 3) {
                    $searchQuery->orWhere('firmenname', 'LIKE', "%{$word}%")
                                ->orWhere('herstellerinformation', 'LIKE', "%{$word}%");
                }
            }
            
            $results = $searchQuery->limit(10)->get();
            
            if ($results->count() > 0) {
                $systemPrompt .= "\nIch habe in unserer Datenbank folgende relevante Hersteller gefunden:\n";
                foreach ($results as $res) {
                    $systemPrompt .= "- {$res->firmenname} (HN: {$res->herstellernummer}): {$res->herstellerinformation}\n";
                }
                $systemPrompt .= "\nNutze diese Informationen, um die Frage des Nutzers zu beantworten.";
            } else {
                $systemPrompt .= " Du befindest dich gerade in der allgemeinen Hersteller-Übersicht. Ich konnte auf Anhieb keine spezifischen Details zu deiner Anfrage in der Datenbank finden. Antworte basierend auf deinem allgemeinen Wissen, weise aber darauf hin, dass in den Hersteller-Notizen nichts gefunden wurde.";
            }
        }
        
        $systemPrompt .= "\nAufgabe: Antworte präzise und professionell. Wenn der Nutzer nach einer E-Mail fragt, erstelle einen Entwurf.";

        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                ],
                'http_errors' => true
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $aiMessage = $result['choices'][0]['message']['content'];

            return response()->json(['answer' => $aiMessage]);

        } catch (\Exception $e) {
            Log::error('OpenAI Error: ' . $e->getMessage());
            return response()->json(['error' => 'Fehler bei der Kommunikation mit OpenAI: ' . $e->getMessage()], 500);
        }
    }
}
