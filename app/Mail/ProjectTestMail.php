<?php

namespace App\Mail;

use App\Models\CompanyProject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;

    /**
     * Create a new message instance.
     */
    public function __construct(CompanyProject $project)
    {
        $this->project = $project;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $p = $this->project;
        
        // Signatur generieren
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

        $body = "Dies ist eine Test-E-Mail von OLGA für das Projekt: {$p->name}\n\n"
              . "Die SMTP-Einstellungen wurden erfolgreich verifiziert.\n\n"
              . "Herzliche Grüße,\n"
              . "Ihr OLGA System\n\n"
              . "---\n"
              . $signatur;

        return $this->subject("OLGA - SMTP Test: " . $p->name)
                    ->html(nl2br(e($body)));
    }
}
