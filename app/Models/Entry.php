<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\FileOrganizer;
use Carbon\Carbon;

class Entry extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'parish_id',
        'week_label',
        'start_date',
        'end_date',
        'data_json',
        'generated_file',
    ];

    protected $casts = [
        'data_json' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtient le chemin complet du fichier Word généré
     */
    public function getFilePath(): string
    {
        return FileOrganizer::generateFilePath($this);
    }

    /**
     * Obtient le chemin du dossier de stockage
     */
    public function getDirectoryPath(): string
    {
        return FileOrganizer::generateDirectoryPath($this);
    }

    /**
     * Crée les dossiers nécessaires pour ce fichier
     */
    public function ensureDirectoryExists(): bool
    {
        return FileOrganizer::ensureDirectoryExists($this);
    }

    /**
     * Obtient le nom du fichier généré
     */
    public function getFileName(): string
    {
        return FileOrganizer::generateFileName($this);
    }

    /**
     * Obtient le trimestre de cette entry
     */
    public function getQuarter(): int
    {
        return FileOrganizer::getQuarter(Carbon::parse($this->start_date));
    }

    /**
     * Obtient le numéro de la semaine dans le mois
     */
    public function getWeekNumber(): int
    {
        return FileOrganizer::getWeekNumberInMonth(Carbon::parse($this->start_date));
    }

    /**
     * Vérifie si le fichier Word existe
     */
    public function fileExists(): bool
    {
        return file_exists($this->getFilePath());
    }

    /**
     * Obtient les informations du fichier s'il existe
     */
    public function getFileInfo(): array
    {
        if (!$this->fileExists()) {
            return [];
        }

        return FileOrganizer::getFileInfo($this->getFilePath());
    }

    /**
     * Obtient le nom formaté pour l'affichage
     */
    public function getDisplayName(): string
    {
        $startDate = Carbon::parse($this->start_date);
        $monthName = $startDate->locale('fr')->isoFormat('MMMM YYYY');
        $weekNumber = $this->getWeekNumber();

        return ucfirst($monthName) . " - Semaine {$weekNumber}";
    }
}
