<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Contest extends Model implements HasMedia
{
    use SoftDeletes, HasFactory;
    use InteractsWithMedia;


    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
    protected $fillable = [
        'name',
        'slug',
        'semester',
        'semester',
        'description',
        'registration_fee',
        'registration_deadline',
        'registration_start_time',
        'countdown_text',
        'countdown_time',
        'sections',
        'departments',
        'lab_teacher_names',
        'student_id_rules',
        'student_id_rules_guide',
        'pickup_points',
        'dates',
        'room_data',
        'extra',
        'manual_payment_methods',
        'registration_limit',
        'public',
        'credentials_csv_path'
    ];

    protected function casts(): array
    {
        return [
            'registration_deadline' => 'datetime',
            'registration_start_time' => 'datetime',
            'countdown_time' => 'datetime',
            'sections' => 'array',
            'departments' => 'array',
            'manual_payment_methods' => 'array',
            'lab_teacher_names' => 'array',
            'dates' => 'array',
            'room_data' => 'array',
            'pickup_points' => 'array',
            'extra' => 'array',
            'public' => 'boolean'
        ];

    }
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('contest-banner-images')
            ->useFallbackUrl(asset('images/no-image.png'))
            ->useFallbackPath(public_path('/images/no-image.png'));

        $this
            ->addMediaCollection('credentials-csv')
            ->acceptsMimeTypes(['text/csv', 'text/plain', 'application/csv'])
            ->singleFile();
    }
    public function registerMediaConversions(Media|null $media = null): void
    {

        $this
            ->addMediaConversion('medium')
            ->fit(Fit::Crop, 1000, 700)
            ->queued();
    }
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
