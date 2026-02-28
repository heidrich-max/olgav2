<?php

namespace App\Mail;

use App\Models\CompanyProject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $offer;
    public $customSubject;
    public $customText;

    /**
     * Create a new message instance.
     */
    public function __construct(CompanyProject $project, $offer)
    {
        $this->project = $project;
        $this->offer = $offer;

        // Platzhalter ersetzen
        $this->customSubject = $this->replacePlaceholders($project->reminder_subject ?? 'Zahlungserinnerung zu Angebot {angebotsnummer}');
        
        $baseText = $project->reminder_text ?: "{anrede}\n\nhiermit möchten wir Sie an unser Angebot {angebotsnummer} vom {erstelldatum} erinnern.\n\nMit freundlichen Grüßen,\n{bearbeiter}\n\n---\n{signatur}";
        $this->customText = $this->replacePlaceholders($baseText);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject($this->customSubject)
                    ->html(nl2br(e($this->customText)));

        if ($this->project->bcc_enabled && $this->project->bcc_address) {
            $mail->bcc($this->project->bcc_address);
        }

        return $mail;
    }

    /**
     * Replace placeholders in text.
     */
    private function replacePlaceholders($text)
    {
        // Dynamische Anrede generieren
        $anrede = 'Sehr geehrte Damen und Herren,';
        
        $anrede_ap = trim($this->offer->anrede_ap ?? '');
        $titel = trim($this->offer->titel_ap ?? '');
        $nachname = trim($this->offer->nachname_ap ?? '');

        if ($anrede_ap && $nachname) {
            $titelString = $titel ? " $titel" : "";
            if (strtolower($anrede_ap) === 'herr') {
                $anrede = "Sehr geehrter Herr{$titelString} {$nachname},";
            } elseif (strtolower($anrede_ap) === 'frau') {
                $anrede = "Sehr geehrte Frau{$titelString} {$nachname},";
            }
        }

        // Signatur generieren
        $p = $this->project;
        $signaturParts = [];
        
        // Zeile 1: Name - c/o
        $line1 = $p->name;
        if ($p->co) {
            $line1 .= " - c/o " . $p->co;
        }
        $signaturParts[] = $line1;

        // Zeile 2: Adresse
        if ($p->strasse || ($p->plz && $p->ort)) {
            $addr = "";
            if ($p->strasse) $addr .= $p->strasse;
            if ($p->plz || $p->ort) {
                if ($addr) $addr .= " - ";
                $addr .= trim(($p->plz ?? "") . " " . ($p->ort ?? ""));
            }
            if ($addr) $signaturParts[] = $addr;
        }

        // Zeile 3: Telefon
        if ($p->telefon) {
            $signaturParts[] = "Telefon: " . $p->telefon;
        }

        // Zeile 4: Inhaber & UST-ID
        $line4Parts = [];
        if ($p->inhaber) $line4Parts[] = "Inhaber: " . $p->inhaber;
        if ($p->ust_id) $line4Parts[] = "Umsatzsteuer-Identnr.: " . $p->ust_id;
        if ($line4Parts) $signaturParts[] = implode(" - ", $line4Parts);

        // Zeile 5: Handelsregister
        if ($p->handelsregister) {
            $signaturParts[] = "Handelsregister: " . $p->handelsregister;
        }

        $signatur = implode("\n", $signaturParts);

        $placeholders = [
            '{anrede}' => $anrede,
            '{stadt}' => $this->offer->ort ?? '',
            '{status}' => $this->offer->projektname ?? '',
            '{bearbeiter}' => $this->offer->benutzer ?? '',
            '{angebotsnummer}' => $this->offer->angebotsnummer,
            '{erstelldatum}' => date('d.m.Y', strtotime($this->offer->erstelldatum)),
            '{firmenname}' => $this->offer->firmenname,
            '{summe}' => number_format($this->offer->angebotssumme, 2, ',', '.') . ' €',
            '{signatur}' => $signatur,
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }
}
