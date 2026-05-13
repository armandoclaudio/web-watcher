<?php

namespace App\Models;

use Database\Factories\WebNotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['alert_id', 'external_id'])]
class WebNotification extends Model
{
    /** @use HasFactory<WebNotificationFactory> */
    use HasFactory;

    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }
}
