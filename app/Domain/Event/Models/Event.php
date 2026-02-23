<?php

declare(strict_types=1);

namespace App\Domain\Event\Models;

use App\Domain\Event\ValueObjects\EventStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $slug
 * @property string      $subdomain
 * @property string|null $description
 * @property string|null $logo_path
 * @property string      $primary_color
 * @property string      $secondary_color
 * @property string      $accent_color
 * @property string      $bg_color
 * @property string      $surface_color
 * @property EventStatus $status
 * @property bool        $access_enabled
 * @property string|null $access_code_hash
 * @property \Carbon\Carbon|null $access_expires_at
 * @property int         $created_by
 */
final class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'description',
        'logo_path',
        'carousel_images',
        'primary_color',
        'secondary_color',
        'accent_color',
        'bg_color',
        'surface_color',
        'status',
        'access_enabled',
        'access_code_hash',
        'access_expires_at',
        'created_by',
    ];

    protected $hidden = [
        'access_code_hash',
    ];

    protected $casts = [
        'status'            => EventStatus::class,
        'access_enabled'    => 'boolean',
        'access_expires_at' => 'datetime',
        'carousel_images'   => 'array',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function modules(): HasMany
    {
        return $this->hasMany(EventModule::class, 'event_id')->orderBy('order');
    }

    // -------------------------------------------------------
    // Business logic
    // -------------------------------------------------------

    public function isAccessible(): bool
    {
        return $this->status->isPubliclyAccessible();
    }

    public function setAccessCode(string $plainCode): void
    {
        $this->access_code_hash = Hash::make($plainCode);
    }

    public function verifyAccessCode(string $plainCode): bool
    {
        if (empty($this->access_code_hash)) {
            return false;
        }

        return Hash::check($plainCode, $this->access_code_hash);
    }

    public function isAccessExpired(): bool
    {
        if ($this->access_expires_at === null) {
            return false;
        }

        return $this->access_expires_at->isPast();
    }

    public function requiresAccessCode(): bool
    {
        return $this->access_enabled
            && !empty($this->access_code_hash)
            && !$this->isAccessExpired();
    }
}
