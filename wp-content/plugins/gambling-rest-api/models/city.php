<?php

use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class City extends AbstractModel
{
    // Define the table name (if different from the model's name)
    protected $table = 'gambling_rest_api_city';  // Define your table name

    // Define the primary key
    protected $primaryKey = 'id';  // The primary key for the City table

    // Define the foreign key for the relationship
    public $foreignKey = 'country_id';  // The foreign key pointing to the 'country' table

    // Define whether IDs are auto-incrementing
    public $incrementing = true;

    // Define the attributes that are mass assignable (fillable)
    protected $fillable = ['name', 'country_id', 'latitude', 'longitude'];  // Fields that you can insert

    // Disable timestamps (created_at, updated_at) if you don’t use them
    public $timestamps = false;

    // Define the relationship with the 'Country' model
    public function country(): BelongsTo
    {
        return $this->belongsTo('Country', 'country_id', 'id');  // Defining the foreign key relation to 'Country'
    }

    public function user(): HasOne
    {
        return $this->hasOne('User', 'city_id');
    }

    public function weather(): HasOne
    {
        return $this->hasOne('Weather', 'city_id');
    }
}