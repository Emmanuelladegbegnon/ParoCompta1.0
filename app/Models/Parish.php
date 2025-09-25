<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parish extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'enable_payment_tracking',
        'weekly_payment_amount',
        'storage_path',
        // Champs de facturation - Émetteur
        'invoice_company_name',
        'invoice_company_description',
        'invoice_company_address',
        'invoice_company_phone',
        'invoice_company_email',
        'invoice_company_ifu',
        // Champs de facturation - Client (paroisse)
        'invoice_parish_address',
        'invoice_parish_phone',
        'invoice_parish_contact_name',
        'invoice_parish_contact_phone',
        // Paramètres de facturation
        'invoice_payment_method',
        'invoice_legal_mentions'
    ];

    protected $casts = [
        'enable_payment_tracking' => 'boolean',
        'weekly_payment_amount' => 'decimal:2'
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Vérifier si le chemin de stockage est absolu (Windows/Linux)
     */
    public function isAbsoluteStorageBase(): bool
    {
        $path = $this->storage_path;

        if (empty($path) || $path === 'public') {
            return false;
        }

        // Windows: C:\ ou D:\ etc.
        if (preg_match('/^[A-Za-z]:\\\\/', $path)) {
            return true;
        }

        // Unix/Linux: commence par /
        if (str_starts_with($path, '/')) {
            return true;
        }

        return false;
    }

    /**
     * Obtenir le chemin de base pour le stockage
     */
    public function getStorageBasePath(): string
    {
        if ($this->isAbsoluteStorageBase()) {
            return rtrim($this->storage_path, '/\\');
        }

        return storage_path('app/' . $this->storage_path);
    }

    /**
     * Créer le dossier de stockage s'il n'existe pas
     */
    public function ensureStorageDirectoryExists(): bool
    {
        $basePath = $this->getStorageBasePath();

        if (!is_dir($basePath)) {
            return mkdir($basePath, 0755, true);
        }

        return true;
    }

    /**
     * Obtenir le chemin complet pour un fichier
     */
    public function getFullFilePath(string $relativePath): string
    {
        $basePath = $this->getStorageBasePath();
        return $basePath . DIRECTORY_SEPARATOR . ltrim($relativePath, '/\\');
    }

    /**
     * Vérifier si le dossier de stockage est accessible en écriture
     */
    public function isStorageWritable(): bool
    {
        $basePath = $this->getStorageBasePath();

        if (!is_dir($basePath)) {
            // Essayer de créer le dossier
            if (!$this->ensureStorageDirectoryExists()) {
                return false;
            }
        }

        return is_writable($basePath);
    }
}