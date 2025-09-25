<?php

namespace App\Services;

use App\Models\Payment;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\Style\Cell;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Générer une facture normalisée pour un paiement
     */
    public function generateInvoice(Payment $payment): string
    {
        // Générer le numéro de facture s'il n'existe pas
        if (!$payment->invoice_number) {
            $payment->invoice_number = $this->generateInvoiceNumber($payment);
            $payment->save();
        }

        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'marginTop' => 1000,
            'marginBottom' => 1000,
            'marginLeft' => 1200,
            'marginRight' => 1200,
        ]);

        // Styles
        $headerStyle = ['bold' => true, 'size' => 16, 'color' => '2E86AB'];
        $titleStyle = ['bold' => true, 'size' => 14, 'color' => '333333'];
        $normalStyle = ['size' => 11, 'color' => '333333'];
        $boldStyle = ['bold' => true, 'size' => 11, 'color' => '333333'];

        // En-tête de la facture
        $this->addInvoiceHeader($section, $payment, $headerStyle, $titleStyle);
        
        // Informations client et facture
        $this->addClientAndInvoiceInfo($section, $payment, $normalStyle, $boldStyle);
        
        // Détails du paiement
        $this->addPaymentDetails($section, $payment, $normalStyle, $boldStyle);
        
        // Récapitulatif
        $this->addSummary($section, $payment, $normalStyle, $boldStyle);
        
        // Pied de page
        $this->addFooter($section, $payment, $normalStyle);

        // Sauvegarder le fichier
        $filePath = $this->saveInvoiceFile($phpWord, $payment);
        
        // Mettre à jour le paiement avec le chemin du fichier
        $payment->update([
            'invoice_file' => $filePath,
            'invoice_generated_at' => now(),
        ]);

        return $filePath;
    }

    /**
     * Générer un numéro de facture unique conforme aux normes légales
     */
    private function generateInvoiceNumber(Payment $payment): string
    {
        $date = Carbon::parse($payment->payment_date);
        $year = $date->year;
        $month = $date->format('m');

        // Compter les factures du mois pour cette paroisse
        $count = Payment::where('parish_id', $payment->parish_id)
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $date->month)
            ->whereNotNull('invoice_number')
            ->count() + 1;

        // Format: FCT-YYYY-MM-NNNN (conforme aux standards légaux)
        return sprintf('FCT-%s-%s-%04d', $year, $month, $count);
    }

    /**
     * Ajouter l'en-tête de la facture
     */
    private function addInvoiceHeader($section, Payment $payment, array $headerStyle, array $titleStyle): void
    {
        // En-tête avec informations légales de l'émetteur
        $table = $section->addTable(['borderSize' => 0, 'cellMargin' => 100]);
        $table->addRow();

        // Informations émetteur (à gauche)
        $parish = $payment->parish;
        $cell1 = $table->addCell(4500);
        $cell1->addText('ÉMETTEUR', ['bold' => true, 'size' => 12, 'color' => '2E86AB']);
        $cell1->addText($parish->invoice_company_name ?: 'ParoCompta Services', ['bold' => true, 'size' => 11]);
        $cell1->addText($parish->invoice_company_description ?: 'Système de Suivi des Recettes Paroissiales', ['size' => 9, 'color' => '666666']);

        if ($parish->invoice_company_address) {
            $cell1->addText('Adresse : ' . $parish->invoice_company_address, ['size' => 9]);
        }
        if ($parish->invoice_company_phone) {
            $cell1->addText('Téléphone : ' . $parish->invoice_company_phone, ['size' => 9]);
        }
        $cell1->addText('Email : ' . ($parish->invoice_company_email ?: 'contact@parocompta.local'), ['size' => 9]);

        if ($parish->invoice_company_ifu) {
            $cell1->addText('IFU : ' . $parish->invoice_company_ifu, ['size' => 9]);
        }

        // Numéro de facture et date (à droite)
        $cell2 = $table->addCell(3500, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);
        $cell2->addText('FACTURE', ['bold' => true, 'size' => 18, 'color' => '2E86AB']);
        $cell2->addText('N° ' . $payment->invoice_number, ['bold' => true, 'size' => 12]);
        $cell2->addText('Date d\'émission : ' . Carbon::parse($payment->payment_date)->format('d/m/Y'), ['size' => 10]);
        $cell2->addText('Heure : ' . Carbon::parse($payment->payment_date)->format('H:i'), ['size' => 9, 'color' => '666666']);

        $section->addTextBreak(2);
    }

    /**
     * Ajouter les informations client et facture
     */
    private function addClientAndInvoiceInfo($section, Payment $payment, array $normalStyle, array $boldStyle): void
    {
        $table = $section->addTable(['borderSize' => 0, 'cellMargin' => 100]);
        $table->addRow();
        
        // Informations client (destinataire)
        $clientCell = $table->addCell(4000);
        $clientCell->addText('CLIENT / DESTINATAIRE :', $boldStyle);
        $clientCell->addText($payment->parish->name, $normalStyle);

        // Contact principal ou utilisateur
        $contactName = $payment->parish->invoice_parish_contact_name ?: $payment->user->name;
        $clientCell->addText('Contact: ' . $contactName, $normalStyle);
        $clientCell->addText('Email: ' . $payment->user->email, $normalStyle);

        // Téléphone du contact ou de la paroisse
        if ($payment->parish->invoice_parish_contact_phone) {
            $clientCell->addText('Téléphone: ' . $payment->parish->invoice_parish_contact_phone, $normalStyle);
        } elseif ($payment->parish->invoice_parish_phone) {
            $clientCell->addText('Téléphone: ' . $payment->parish->invoice_parish_phone, $normalStyle);
        }

        // Adresse de la paroisse
        if ($payment->parish->invoice_parish_address) {
            $clientCell->addText('Adresse: ' . $payment->parish->invoice_parish_address, $normalStyle);
        }
        
        // Informations facture
        $invoiceCell = $table->addCell(4000);
        $invoiceCell->addText('DÉTAILS FACTURE :', $boldStyle);
        $invoiceCell->addText('Date de facture: ' . Carbon::parse($payment->payment_date)->format('d/m/Y'), $normalStyle);
        $invoiceCell->addText('Période: ' . $payment->period_name, $normalStyle);
        $invoiceCell->addText('Mode de paiement: ' . ($payment->payment_method ?: 'Non spécifié'), $normalStyle);

        $section->addTextBreak(2);
    }

    /**
     * Ajouter les détails du paiement
     */
    private function addPaymentDetails($section, Payment $payment, array $normalStyle, array $boldStyle): void
    {
        $section->addText('DÉTAILS DU PAIEMENT', $boldStyle);
        $section->addTextBreak(1);

        // Tableau des détails
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => 'CCCCCC',
            'cellMargin' => 80,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
        ];
        
        $table = $section->addTable($tableStyle);
        
        // En-tête du tableau
        $table->addRow(null, ['tblHeader' => true]);
        $table->addCell(3000, ['bgColor' => 'F8F9FA'])->addText('Description', $boldStyle);
        $table->addCell(1500, ['bgColor' => 'F8F9FA'])->addText('Quantité', $boldStyle);
        $table->addCell(1500, ['bgColor' => 'F8F9FA'])->addText('Prix unitaire', $boldStyle);
        $table->addCell(2000, ['bgColor' => 'F8F9FA'])->addText('Montant', $boldStyle);

        // Ligne de détail
        $table->addRow();
        $table->addCell(3000)->addText('Saisie comptable - ' . $payment->period_name, $normalStyle);
        $table->addCell(1500)->addText($payment->weeks_worked . ' semaine(s)', $normalStyle);
        $table->addCell(1500)->addText(number_format($payment->parish->weekly_payment_amount, 0, ',', ' ') . ' FCFA', $normalStyle);
        $table->addCell(2000)->addText(number_format($payment->amount_received, 0, ',', ' ') . ' FCFA', $normalStyle);

        $section->addTextBreak(1);
    }

    /**
     * Ajouter le récapitulatif
     */
    private function addSummary($section, Payment $payment, array $normalStyle, array $boldStyle): void
    {
        // Tableau de récapitulatif aligné à droite
        $table = $section->addTable(['borderSize' => 0, 'cellMargin' => 80]);
        
        // Espace vide à gauche, récapitulatif à droite
        $table->addRow();
        $table->addCell(5000); // Cellule vide
        $summaryCell = $table->addCell(3000);
        
        $summaryTable = $summaryCell->addTable(['borderSize' => 6, 'borderColor' => 'CCCCCC']);
        
        $summaryTable->addRow();
        $summaryTable->addCell(2000)->addText('Sous-total:', $normalStyle);
        $summaryTable->addCell(1000)->addText(number_format($payment->amount_received, 0, ',', ' ') . ' FCFA', $normalStyle);
        
        $summaryTable->addRow();
        $summaryTable->addCell(2000)->addText('TVA (0%):', $normalStyle);
        $summaryTable->addCell(1000)->addText('0 FCFA', $normalStyle);
        
        $summaryTable->addRow();
        $summaryTable->addCell(2000, ['bgColor' => 'F8F9FA'])->addText('TOTAL:', $boldStyle);
        $summaryTable->addCell(1000, ['bgColor' => 'F8F9FA'])->addText(number_format($payment->amount_received, 0, ',', ' ') . ' FCFA', $boldStyle);

        $section->addTextBreak(2);
    }

    /**
     * Ajouter le pied de page
     */
    private function addFooter($section, Payment $payment, array $normalStyle): void
    {
        // Notes du paiement
        $section->addText('NOTES:', ['bold' => true, 'size' => 10]);
        if ($payment->notes) {
            $section->addText($payment->notes, ['size' => 10, 'color' => '666666']);
        } else {
            $section->addText('Paiement pour services de suivi des recettes paroissiales.', ['size' => 10, 'color' => '666666']);
        }

        $section->addTextBreak(2);

        // Mode de paiement
        $paymentMethod = $payment->parish->invoice_payment_method ?: 'Espèces';
        $section->addText('MODE DE PAIEMENT: ' . $paymentMethod, ['bold' => true, 'size' => 10, 'color' => '2E86AB']);

        $section->addTextBreak(1);

        // Mentions légales configurables
        $section->addText('MENTIONS LÉGALES', ['bold' => true, 'size' => 10, 'color' => '2E86AB']);

        $legalMentions = $payment->parish->invoice_legal_mentions ?:
            'Facture établie selon les normes en vigueur. Application destinée au suivi des recettes et quêtes paroissiales uniquement.';

        // Diviser les mentions en lignes si elles contiennent des points
        $mentions = explode('.', $legalMentions);
        foreach ($mentions as $mention) {
            $mention = trim($mention);
            if (!empty($mention)) {
                $section->addText('• ' . $mention . '.', ['size' => 8, 'color' => '666666']);
            }
        }

        $section->addTextBreak(1);

        // Avertissement sur la nature de l'application
        $section->addText('IMPORTANT: Cette application est destinée au suivi des recettes et quêtes paroissiales uniquement.',
                         ['bold' => true, 'size' => 9, 'color' => 'FF6B35']);
        $section->addText('Elle ne constitue pas un système de comptabilité paroissiale complète.',
                         ['size' => 8, 'color' => 'FF6B35']);

        $section->addTextBreak(1);

        // Pied de page
        $section->addText('Merci pour votre confiance !', ['bold' => true, 'size' => 12, 'color' => '2E86AB']);
        $section->addText('ParoCompta - Système de suivi des recettes paroissiales', ['size' => 8, 'color' => '999999']);
        $section->addText('Facture générée le ' . now()->format('d/m/Y à H:i'), ['size' => 8, 'color' => '999999']);
    }

    /**
     * Sauvegarder le fichier de facture
     */
    private function saveInvoiceFile(PhpWord $phpWord, Payment $payment): string
    {
        $parish = $payment->parish;
        $date = Carbon::parse($payment->payment_date);
        
        // Créer le chemin de stockage dédié pour TOUTES les factures
        $basePath = $parish->storage_path ?: 'public';
        $invoicesPath = $basePath . '/FACTURES_PAROCOMPTA/' . $date->year . '/' . $date->format('m');
        
        // Nom du fichier
        $filename = 'Facture_' . $payment->invoice_number . '.docx';
        $fullPath = $invoicesPath . '/' . $filename;
        
        // Créer le répertoire s'il n'existe pas
        if ($parish->isAbsoluteStorageBase()) {
            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($fullPath);
        } else {
            $tempFile = tempnam(sys_get_temp_dir(), 'invoice_') . '.docx';
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);
            
            Storage::put($fullPath, file_get_contents($tempFile));
            unlink($tempFile);
        }
        
        return $fullPath;
    }

    /**
     * Vérifier si une facture existe pour un paiement
     */
    public function hasInvoice(Payment $payment): bool
    {
        return !empty($payment->invoice_file) && !empty($payment->invoice_number);
    }

    /**
     * Obtenir le chemin complet de la facture
     */
    public function getInvoicePath(Payment $payment): ?string
    {
        if (!$this->hasInvoice($payment)) {
            return null;
        }

        $parish = $payment->parish;
        
        if ($parish->isAbsoluteStorageBase()) {
            return $payment->invoice_file;
        } else {
            return storage_path('app/' . $payment->invoice_file);
        }
    }
}
