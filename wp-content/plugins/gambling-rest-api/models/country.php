<?php

use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends AbstractModel
{
    protected $table = 'gambling_rest_api_country';  // The table name in the database

    // The primary key associated with the table
    protected $primaryKey = 'id';

    // Whether the IDs are auto-incrementing
    public $incrementing = true;

    // The attributes that are mass assignable
    protected $fillable = ['name', 'official_name', 'cca2', 'cca3'];

    // Disabling timestamps (created_at, updated_at)
    public $timestamps = false;

    // Define any relationships (optional)
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id');
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(Currency::class);
    }

}