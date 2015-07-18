<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Member;

class Tip extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'created_at',
        'created_by',
        'deleted_at',
        'updated_at',
        'updated_by',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $fillable = [
        'content',
    ];

    public function author()
    {
        return $this->belongsTo('App\Member', 'created_by');
    }

    public function edited_by()
    {
        return $this->belongsTo('App\Member', 'updated_by');
    }

    public function jsonSerialize()
    {
        $result = parent::jsonSerialize();
        $result['url'] = $this->url();

        return $result;
    }

    public function url()
    {
        return "/tips/" . $this->id;
    }
}