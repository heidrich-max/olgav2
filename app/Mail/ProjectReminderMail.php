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
        $this->customText = $this->replacePlaceholders($project->reminder_text ?: "{anrede}\n\nhiermit möchten wir Sie an unser Angebot {angebotsnummer} vom {erstelldatum} erinnern.");
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

        $placeholders = [
            '{anrede}' => $anrede,
            '{stadt}' => $this->offer->ort ?? '',
            '{angebotsnummer}' => $this->offer->angebotsnummer,
            '{erstelldatum}' => date('d.m.Y', strtotime($this->offer->erstelldatum)),
            '{firmenname}' => $this->offer->firmenname,
            '{summe}' => number_format($this->offer->angebotssumme, 2, ',', '.') . ' €',
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }
}
