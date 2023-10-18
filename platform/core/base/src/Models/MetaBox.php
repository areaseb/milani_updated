<?php

namespace Botble\Base\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaBox extends BaseModel
{
    protected $fillable = [
        'reference_id',
        'reference_type',
        'meta_key',
        'meta_value',
    ];

    protected $table = 'meta_boxes';

    protected $casts = [
        'meta_value' => 'array',
    ];

    public function reference(): BelongsTo
    {
        return $this->morphTo();
    }
}
