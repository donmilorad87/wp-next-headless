<?php

use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Currency extends AbstractModel
{
    protected $table = 'gambling_rest_api_currency';  // The table name in the database

    // The primary key associated with the table
    protected $primaryKey = 'id';

    // Define the foreign key for the relationship
    public $foreignKey = 'country_id';  // The foreign key pointing to the 'country' table

    // Whether the IDs are auto-incrementing
    public $incrementing = true;

    // The attributes that are mass assignable
    protected $fillable = ['name', 'iso3', 'symbol', 'rate_to_eur'];

    // Disabling timestamps (created_at, updated_at)
    public $timestamps = false;

    // Define any relationships (optional)
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');  // Defining the foreign key relation to 'Country'
    }

}