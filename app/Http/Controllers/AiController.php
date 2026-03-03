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
        $url = $request->input('url');
        $pageTitle = $request->input('page_title');
        
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
        Du hilfst dem Nutzer bei der Verwaltung von Herstellern, Angeboten und internen Prozessen.";

        // Kontext basierend auf URL bestimmen
        $contextInfo = "\nAktueller Kontext des Nutzers:";
        if ($url) {
            $contextInfo .= "\n- URL: " . $url;
            if (strpos($url, '/manufacturers') !== false && strpos($url, '/edit') === false) {
                $contextInfo .= "\n- Seite: Hersteller-Übersicht";
            } elseif (strpos($url, '/manufacturers/') !== false && strpos($url, '/edit') !== false) {
                $contextInfo .= "\n- Seite: Hersteller bearbeiten";
            } elseif (strpos($url, '/calendar') !== false) {
                $contextInfo .= "\n- Seite: Terminkalender";
            } elseif (strpos($url, '/dashboard') !== false) {
                $contextInfo .= "\n- Seite: Dashboard / Hauptübersicht";
            } elseif (strpos($url, '/offers') !== false) {
                $contextInfo .= "\n- Seite: Angebotsverwaltung / Kalkulation";
            }
        }
        if ($pageTitle) {
            $contextInfo .= "\n- Seitentitel: " . $pageTitle;
        }

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
            
            $hasSearchTerm = false;
            foreach ($keywords as $word) {
                if (strlen($word) > 3) {
                    $searchQuery->orWhere('firmenname', 'LIKE', "%{$word}%")
                                ->orWhere('herstellerinformation', 'LIKE', "%{$word}%");
                    $hasSearchTerm = true;
                }
            }
            
            if ($hasSearchTerm) {
                $results = $searchQuery->limit(10)->get();
                if ($results->count() > 0) {
                    $systemPrompt .= "\nIch habe in unserer Datenbank folgende relevante Hersteller gefunden:\n";
                    foreach ($results as $res) {
                        $systemPrompt .= "- {$res->firmenname} (HN: {$res->herstellernummer}): {$res->herstellerinformation}\n";
                    }
                }
            }
        }
        
        $systemPrompt .= $contextInfo;
        $systemPrompt .= "\nAufgabe: Antworte präzise und professionell. Nutze den oben genannten Kontext, um dem Nutzer bestmöglich zu helfen.";

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
