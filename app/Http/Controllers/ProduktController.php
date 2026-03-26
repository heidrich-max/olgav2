<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Produkt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProduktController extends Controller
{
    public function index(Request $request)
    {
        $query = Produkt::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('base_artikelnummer', 'like', "%{$search}%")
                  ->orWhere('produktname', 'like', "%{$search}%")
                  ->orWhere('produktname_hersteller', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('kategorie1', $request->category);
        }

        $sort = $request->get('sort', 'base_artikelnummer');
        $direction = $request->get('direction', 'asc');
        $allowedSorts = ['base_artikelnummer', 'produktname', 'kategorie1', 'preis'];
        if (!in_array($sort, $allowedSorts)) { $sort = 'base_artikelnummer'; }
        if (!in_array($direction, ['asc', 'desc'])) { $direction = 'asc'; }

        $produkte = $query->with('varianten.druckpositionen')->orderBy($sort, $direction)->paginate(50);
        $categories = Produkt::distinct()->where('kategorie1', '!=', '')->pluck('kategorie1');

        return view('products.index', compact('produkte', 'categories'));
    }

    public function show(Request $request, Produkt $product)
    {
        return view('products.show', [
            'produkt' => $product
        ]);
    }

    public function edit(Request $request, Produkt $produkt)
    {
        return view('products.edit', compact('produkt'));
    }

    public function update(Request $request, Produkt $product)
    {
        $product->update($request->all());
        return redirect()->route('products.index')->with('success', 'Produkt aktualisiert.');
    }

    public function export()
    {
        $produkte = Produkt::all();
        $xml = new SimpleXMLElementExtended('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><root/>');

        foreach ($produkte as $p) {
            $row = $xml->addChild('row');
            $row->addChild('Artikelnummer', '')->addCData($p->artikelnummer);
            $row->addChild('Hersteller', $p->hersteller_id);
            $row->addChild('ProduktnameHersteller', '')->addCData($p->produktname_hersteller);
            $row->addChild('Produktname', '')->addCData($p->produktname);
            $row->addChild('Material', '')->addCData($p->material);
            $row->addChild('Produktmasse', '')->addCData($p->produktmasse);
            $row->addChild('GewichtG', '')->addCData($p->gewicht_g);
            $row->addChild('Grammatur', (string)$p->grammatur);
            $row->addChild('MengeProKarton', $p->menge_pro_karton);
            $row->addChild('Herkunftsland', '')->addCData($p->herkunftsland);
            $row->addChild('Zolltarifnummer', $p->zolltarifnummer);
            $row->addChild('Mindestmenge', $p->mindestmenge);
            $row->addChild('Kategorie1', '')->addCData($p->kategorie1);
            $row->addChild('Kategorie2', '')->addCData($p->kategorie2);
            $row->addChild('Kategorie3', '')->addCData($p->kategorie3);
            $row->addChild('Kategorie4', '')->addCData($p->kategorie4);
            $row->addChild('Beschreibung', '')->addCData($p->beschreibung);
            $row->addChild('LieferzeitOhneDruckMin', $p->lieferzeit_ohne_druck_min);
            $row->addChild('LieferzeitOhneDruckMax', $p->lieferzeit_ohne_druck_max);
            $row->addChild('LieferzeitMitDruckMin', $p->lieferzeit_mit_druck_min);
            $row->addChild('LieferzeitMitDruckMax', $p->lieferzeit_mit_druck_max);
            $row->addChild('MasseProduktkarton', $p->masse_produktkarton);
            $row->addChild('BruttogewichtProduktkarton', $p->bruttogewicht_produktkarton);
            $row->addChild('Preis', str_replace('.', ',', (string)$p->preis));
            $row->addChild('Sale', $p->sale);
            $row->addChild('Minenfarbe', '')->addCData($p->minenfarbe);
            $row->addChild('Bearbeitungscode', $p->bearbeitungscode);
            $row->addChild('Hinweis', '')->addCData($p->hinweis);
            $row->addChild('Kapazitaet', $p->kapazitaet);
            $row->addChild('Farbe', '')->addCData($p->farbe);
            $row->addChild('Foto01', '')->addCData($p->foto01);
            $row->addChild('Foto02', '')->addCData($p->foto02);
            $row->addChild('Foto03', '')->addCData($p->foto03);
            $row->addChild('Foto04', '')->addCData($p->foto04);
        }

        $dom = new \DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        @$dom->loadXML($xml->asXML());

        return Response::make($dom->saveXML(), 200, [
            'Content-Type' => 'text/xml',
            'Content-Disposition' => 'attachment; filename="produkt_daten_export.xml"',
        ]);
    }
}

// Hilfsmethode für CDATA hinzufügen
class SimpleXMLElementExtended extends \SimpleXMLElement {
    public function addCData($cdata_text) {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection((string)$cdata_text));
    }
}
