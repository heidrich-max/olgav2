<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'offer_id', 'task', 'is_completed', 'is_system'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Entfernt automatisch generierte To-Dos für ein spezifisches Angebot.
     */
    public static function cleanupForOffer($offerNumber, $offerId = null)
    {
        if (empty($offerNumber) && empty($offerId)) return;
        
        $query = static::where('is_system', true);

        if ($offerId) {
            $query->where('offer_id', $offerId);
        } else {
            $query->where(function($q) use ($offerNumber) {
                $q->where('task', 'like', "Angebots-Nachverfolgung%: {$offerNumber}%")
                  ->orWhere('task', 'like', "Wiedervorlage Angebot {$offerNumber}%");
            });
        }

        $query->delete();
    }
}
