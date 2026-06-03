<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemConfiguration extends Model
{
    protected $fillable = [
        'config_key',
        'config_value',
        'is_encrypted',
        'updated_by',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Helper to get config value
     */
    public static function getVal(string $key, $default = null)
    {
        $config = self::where('config_key', $key)->first();
        if (!$config) {
            return $default;
        }

        // Decrypt if encrypted (in a real app we'd decrypt here, for mockup/prototype we return config_value)
        return $config->config_value;
    }

    /**
     * Helper to set config value
     */
    public static function setVal(string $key, $value, $updatedBy = null)
    {
        return self::updateOrCreate(
            ['config_key' => $key],
            [
                'config_value' => $value,
                'updated_by' => $updatedBy
            ]
        );
    }
}
