<?php

namespace App\Services;

use App\Models\CompanyProject;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class ProjectMailService
{
    /**
     * Configure the mailer dynamically for a specific project.
     *
     * @param CompanyProject $project
     * @return void
     */
    public function configureMailer(CompanyProject $project)
    {
        if (!$project->smtp_host) {
            return;
        }

        $config = [
            'transport' => 'smtp',
            'host' => $project->smtp_host,
            'port' => $project->smtp_port ?? 587,
            'encryption' => $project->smtp_encryption ?: null,
            'username' => $project->smtp_user,
            'password' => $project->smtp_password,
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN') ?: 'dev.frankgroup.net',
        ];

        Config::set('mail.mailers.project_mailer', $config);
        Config::set('mail.from.address', $project->mail_from_address ?? config('mail.from.address'));
        Config::set('mail.from.name', $project->mail_from_name ?? $project->name);
        
        // Purge the mailer if it was already resolved to force reconfiguration
        Mail::purge('project_mailer');
    }

    /**
     * Get the mailer instance for the project.
     * 
     * @param CompanyProject $project
     * @return \Illuminate\Mail\Mailer
     */
    public function getMailer(CompanyProject $project)
    {
        $this->configureMailer($project);
        return Mail::mailer('project_mailer');
    }
}
