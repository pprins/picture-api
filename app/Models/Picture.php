<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $table = 'pictures';

    protected $primaryKey = 'pic_id';

    protected $guarded = ['pic_id'];

    protected $hidden = ['user_id'];

    /**
     * Relation to the associated user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    /**
     * Check if the given image url equals the existing stored url
     * @param $picture_url
     * @return bool
     */
    public function hasUpdatedPictureUrl($picture_url): bool
    {
        return $this->pic_url != $picture_url || !$this->pic_filename;
    }
}
