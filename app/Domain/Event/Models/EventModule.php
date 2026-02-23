<?php

declare(strict_types=1);

namespace App\Domain\Event\Models;

use App\Infrastructure\Persistence\Scopes\EventScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property int    $event_id
 * @property string $type
 * @property bool   $is_active
 * @property int    $order
 * @property array|null $settings
 */
final class EventModule extends Model
{
    use HasFactory;

    protected $table = 'event_modules';

    protected $fillable = [
        'event_id',
        'type',
        'is_active',
        'order',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings'  => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new EventScope());
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
