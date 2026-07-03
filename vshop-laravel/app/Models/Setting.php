<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'settings';

    // The primary key for the model
    protected $primaryKey = 'id';

    // The attributes that are mass assignable
    protected $fillable = [
        'key',
        'value',
        'site_name',
        'contact_email',
        'contact_phone',
        'system_logo',
        'favicon_icon',
        'lazyload_image',
        'shop_link_banner',
        'currency_format',
    ];

    // The attributes that should be hidden for arrays (sensitive data)
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * Get a setting value by key
     */
    public static function getByKey($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key (upsert)
     */
    public static function setByKey($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    // Accessor for system_logo to generate the full URL
    public function getSystemLogoUrlAttribute()
    {
        return asset('storage/' . $this->system_logo);
    }

    // Accessor for favicon_icon to generate the full URL
    public function getFaviconIconUrlAttribute()
    {
        return asset('storage/' . $this->favicon_icon);
    }

    // Accessor for lazyload_image to generate the full URL
    public function getLazyloadImageUrlAttribute()
    {
        return asset('storage/' . $this->lazyload_image);
    }

    // Accessor for shop_link_banner to generate the full URL
    public function getShopLinkBannerUrlAttribute()
    {
        return asset('storage/' . $this->shop_link_banner);
    }
}
