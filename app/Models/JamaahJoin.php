<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JamaahJoin extends Model
{
    use HasFactory;

    protected $table = 'jamaahjoin';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null; // Composite key

    protected $fillable = [
        'TglJamJoin',
        'TglJamLeft',
        'IDVoiceMeet',
        'IDPaket',
        'IDPerson',
    ];

    /**
     * Relationship dengan SesiVoiceMeet
     */
    public function voiceMeet()
    {
        return $this->belongsTo(SesiVoiceMeet::class, 'IDVoiceMeet', 'IDVoiceMeet');
    }

    /**
     * Relationship dengan MstPerson
     */
    public function person()
    {
        return $this->belongsTo(MstPerson::class, 'IDPerson', 'IDPerson');
    }

    /**
     * Get active join (belum leave)
     */
    public static function getActiveJoin($IDVoiceMeet, $IDPerson)
    {
        return self::where('IDVoiceMeet', $IDVoiceMeet)
            ->where('IDPerson', $IDPerson)
            ->whereNull('TglJamLeft')
            ->first();
    }

    /**
     * Get all participants in a meet
     */
    public static function getParticipants($IDVoiceMeet)
    {
        return self::with('person:IDPerson,NamaJamaah,FotoProfil')
            ->where('IDVoiceMeet', $IDVoiceMeet)
            ->get();
    }

    /**
     * Get active participants (currently in meet)
     */
    public static function getActiveParticipants($IDVoiceMeet)
    {
        return self::with('person:IDPerson,NamaJamaah,FotoProfil')
            ->where('IDVoiceMeet', $IDVoiceMeet)
            ->whereNull('TglJamLeft')
            ->get();
    }

    /**
     * Calculate duration in minutes
     */
    public function getDurationAttribute()
    {
        if (!$this->TglJamJoin || !$this->TglJamLeft) {
            return null;
        }

        $join = \Carbon\Carbon::parse($this->TglJamJoin, 'UTC');
        $left = \Carbon\Carbon::parse($this->TglJamLeft, 'UTC');

        return $join->diffInMinutes($left);
    }
}
