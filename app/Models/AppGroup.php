<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'tagline', 'logo', 'sort_order', 'is_active'])]
class AppGroup extends Model
{
    use Sortable;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn () => upload_url($this->logo));
    }

    protected function initials(): Attribute
    {
        return Attribute::get(fn () => initials($this->name));
    }
}
