<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\SesiVoiceMeet;
use App\Models\JamaahJoin;
use App\Models\MobileModels\Person;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MeetController extends BaseApiController
{
    private string $jitsiSecret;
    private string $jitsiAppId;
    private string $jitsiDomain;

    public function __construct()
    {
        $this->jitsiSecret = (string) config('services.jitsi.secret');
        $this->jitsiAppId = (string) config('services.jitsi.app_id', 'haromain_meet');
        $this->jitsiDomain = (string) config('services.jitsi.domain', 'meet.oldmapps.com');
    }

    public function list(Request $request): JsonResponse
    {
        $IDPerson = $request->input('IDPerson');

        if (!$IDPerson) {
            return $this->validationError('IDPerson harus diisi');
        }

        try {
            // Get all packages where user is registered (as leader or jamaah)
            $jamaahPakets = DB::table('jamaahpaket as jp')
                ->join('mstpaket as pk', function ($join) {
                    $join->on('pk.IDTravel', '=', 'jp.IDTravel')
                        ->on('pk.IDPaket', '=', 'jp.IDPaket');
                })
                ->where('jp.IDPerson', $IDPerson)
                ->where('jp.IsPaid', 1)
                ->where('pk.IsAktif', 1)
                ->whereRaw("DATE(pk.TglPulang) >= ?", [date('Y-m-d')])
                ->select('jp.IDPaket')
                ->get();

            if ($jamaahPakets->isEmpty()) {
                return $this->success([], 'Data jadwal meet berhasil diambil');
            }

            // Get all package IDs
            $paketIds = $jamaahPakets->pluck('IDPaket')->toArray();

            // Auto-close expired meetings
            $currentTime = now('UTC');
            Log::info('Auto-close check: Current time = ' . $currentTime->format('Y-m-d H:i:s'));

            $expiredMeets = SesiVoiceMeet::whereIn('IDPaket', $paketIds)
                ->where('StatusSesi', '!=', 'SELESAI')
                ->whereNotNull('TglJamSelesai')
                ->get();

            foreach ($expiredMeets as $meet) {
                $endTime = Carbon::parse($meet->TglJamSelesai, 'UTC');
                Log::info('Checking meet ' . $meet->IDVoiceMeet . ': End time = ' . $endTime->format('Y-m-d H:i:s'));

                if ($currentTime->gt($endTime)) {
                    $meet->update(['StatusSesi' => 'SELESAI']);
                    Log::info('Auto-closed expired meet: ' . $meet->IDVoiceMeet);
                }
            }

            // Ambil meet untuk semua paket user (exclude yang sudah SELESAI)
            $meets = SesiVoiceMeet::whereIn('IDPaket', $paketIds)
                ->where('StatusSesi', '!=', 'SELESAI')
                ->with(['person:IDPerson,NamaJamaah', 'paket:IDPaket,NamaPaket'])
                ->orderBy('TglJamMulai', 'desc')
                ->get();

            $data = $meets->map(function ($meet) {
                return $meet->toApiResponse();
            });

            return $this->success($data, 'Data jadwal meet berhasil diambil');
        } catch (\Exception $e) {
            Log::error('Meet list error: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jadwal meet', 500);
        }
    }

    /**
     * GET /api/meet/detail
     * Get detail of a single meet
     */
    public function detail(Request $request): JsonResponse
    {
        $IDMeet = $request->input('IDMeet');

        if (!$IDMeet) {
            return $this->validationError('IDMeet harus diisi');
        }

        try {
            $meet = SesiVoiceMeet::getDetailWithCreator($IDMeet);

            if (!$meet) {
                return $this->notFound('Jadwal meet tidak ditemukan');
            }

            return $this->success($meet->toApiResponse(), 'Detail jadwal meet berhasil diambil');
        } catch (\Exception $e) {
            Log::error('Meet detail error: ' . $e->getMessage());
            return $this->error('Gagal mengambil detail jadwal meet', 500);
        }
    }

    /**
     * POST /api/meet/create
     * Create new meet schedule (Leader only)
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'NamaSesi' => 'required|string|max:255',
            'TglJamMulai' => 'required|date',
            'TglJamSelesai' => 'required|date|after:TglJamMulai',
            'IDPerson' => 'required|string',
            'IDPaket' => 'nullable|string', // Optional: for multi-package leaders
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->first());
        }

        $IDPerson = $request->input('IDPerson');
        $requestedIDPaket = $request->input('IDPaket');

        // Parse to UTC explicitly
        $startUtc = Carbon::parse($request->input('TglJamMulai'))->utc();
        $endUtc = Carbon::parse($request->input('TglJamSelesai'))->utc();

        // If IDPaket is provided, verify leader owns this package
        if ($requestedIDPaket) {
            $leaderPaket = DB::table('jamaahpaket')
                ->where('IDPerson', $IDPerson)
                ->where('IDPaket', $requestedIDPaket)
                ->where('StatusJamaah', 'Leader')
                ->first();

            if (!$leaderPaket) {
                return $this->error('Anda bukan leader di paket ini', 403);
            }
        } else {
            // Auto-assign: get first package where user is leader
            $leaderPaket = $this->getLeaderPackage($IDPerson);

            if (!$leaderPaket) {
                return $this->error('Hanya leader yang dapat membuat jadwal meet', 403);
            }
        }

        try {
            DB::beginTransaction();

            $IDVoiceMeet = SesiVoiceMeet::generateID();

            $meet = SesiVoiceMeet::create([
                'IDVoiceMeet' => $IDVoiceMeet,
                'NamaSesi' => $request->input('NamaSesi'),
                'TglJamMulai' => $startUtc->format('Y-m-d H:i:s'),
                'TglJamSelesai' => $endUtc->format('Y-m-d H:i:s'),
                'StatusSesi' => 'TERJADWAL',
                'IDPerson' => $IDPerson,
                'IDPaket' => $leaderPaket->IDPaket,
                'IDTravel' => $leaderPaket->IDTravel,
            ]);

            DB::commit();

            // Reload with creator data
            $meet = SesiVoiceMeet::getDetailWithCreator($IDVoiceMeet);

            Log::info('Meet created: ' . $IDVoiceMeet . ' by ' . $IDPerson . ' for package ' . $leaderPaket->IDPaket);

            return $this->success(
                $meet->toApiResponse(),
                'Jadwal meet berhasil dibuat',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Meet create error: ' . $e->getMessage());
            return $this->error('Gagal membuat jadwal meet', 500);
        }
    }

    /**
     * PUT /api/meet/update
     * Update meet schedule (Leader only)
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'IDMeet' => 'required|string',
            'IDPerson' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->first());
        }

        $IDMeet = $request->input('IDMeet');
        $IDPerson = $request->input('IDPerson');

        // Check if user is a leader
        if (!$this->isLeader($IDPerson)) {
            return $this->error('Hanya leader yang dapat mengubah jadwal meet', 403);
        }

        try {
            $meet = SesiVoiceMeet::where('IDVoiceMeet', $IDMeet)->first();

            if (!$meet) {
                return $this->notFound('Jadwal meet tidak ditemukan');
            }

            // Prepare update data
            $updateData = [];

            if ($request->has('NamaSesi')) {
                $updateData['NamaSesi'] = $request->input('NamaSesi');
            }

            if ($request->has('TglJamMulai')) {
                $validator = Validator::make($request->all(), [
                    'TglJamMulai' => 'date',
                ]);
                if ($validator->fails()) {
                    return $this->validationError($validator->errors()->first());
                }
                // Explicitly convert to UTC
                $updateData['TglJamMulai'] = Carbon::parse($request->input('TglJamMulai'))->utc()->format('Y-m-d H:i:s');
            }

            if ($request->has('TglJamSelesai')) {
                $validator = Validator::make($request->all(), [
                    'TglJamSelesai' => 'date',
                ]);
                if ($validator->fails()) {
                    return $this->validationError($validator->errors()->first());
                }
                // Explicitly convert to UTC
                $updateData['TglJamSelesai'] = Carbon::parse($request->input('TglJamSelesai'))->utc()->format('Y-m-d H:i:s');
            }

            if ($request->has('StatusSesi')) {
                $updateData['StatusSesi'] = $request->input('StatusSesi');
            }



            if (empty($updateData)) {
                return $this->validationError('Tidak ada data yang diubah');
            }

            $meet->update($updateData);

            // Reload with creator data
            $meet = SesiVoiceMeet::getDetailWithCreator($IDMeet);

            Log::info('Meet updated: ' . $IDMeet . ' by ' . $IDPerson);

            return $this->success(
                $meet->toApiResponse(),
                'Jadwal meet berhasil diperbarui'
            );
        } catch (\Exception $e) {
            Log::error('Meet update error: ' . $e->getMessage());
            return $this->error('Gagal memperbarui jadwal meet', 500);
        }
    }

    /**
     * DELETE /api/meet/delete
     * Delete meet schedule (Leader only)
     */
    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'IDMeet' => 'required|string',
            'IDPerson' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->first());
        }

        $IDMeet = $request->input('IDMeet');
        $IDPerson = $request->input('IDPerson');

        // Check if user is a leader
        if (!$this->isLeader($IDPerson)) {
            return $this->error('Hanya leader yang dapat menghapus jadwal meet', 403);
        }

        try {
            $meet = SesiVoiceMeet::where('IDVoiceMeet', $IDMeet)->first();

            if (!$meet) {
                return $this->notFound('Jadwal meet tidak ditemukan');
            }

            $meet->delete();

            Log::info('Meet deleted: ' . $IDMeet . ' by ' . $IDPerson);

            return $this->success(null, 'Jadwal meet berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Meet delete error: ' . $e->getMessage());
            return $this->error('Gagal menghapus jadwal meet', 500);
        }
    }

    /**
     * POST /api/meet/join
     * Jamaah join meet
     */
    // public function join(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'IDVoiceMeet' => 'required|string',
    //         'IDPerson' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->validationError($validator->errors()->first());
    //     }

    //     $IDVoiceMeet = $request->input('IDVoiceMeet');
    //     $IDPerson = $request->input('IDPerson');

    //     try {
    //         // Cek apakah meet exists
    //         $meet = SesiVoiceMeet::where('IDVoiceMeet', $IDVoiceMeet)->first();

    //         if (!$meet) {
    //             return $this->notFound('Jadwal meet tidak ditemukan');
    //         }

    //         // Get all jamaah packages (jamaah bisa terdaftar di multiple paket)
    //         $jamaahPakets = DB::table('jamaahpaket')
    //             ->where('IDPerson', $IDPerson)
    //             ->get();

    //         if ($jamaahPakets->isEmpty()) {
    //             return $this->error('Anda belum terdaftar di paket manapun', 404);
    //         }

    //         // Validate: Jamaah harus terdaftar di paket yang sama dengan meet
    //         $paketIds = $jamaahPakets->pluck('IDPaket')->toArray();
    //         if (!in_array($meet->IDPaket, $paketIds)) {
    //             return $this->error(
    //                 'Anda tidak dapat join meeting ini karena bukan anggota paket ' . $meet->IDPaket,
    //                 403
    //             );
    //         }

    //         // Validate: Meeting must have started
    //         if ($meet->TglJamMulai && now()->lt(Carbon::parse($meet->TglJamMulai))) {
    //             $meetStartTime = Carbon::parse($meet->TglJamMulai);
    //             $minutesUntil = now()->diffInMinutes($meetStartTime, false);

    //             if ($minutesUntil > 60) {
    //                 $hoursUntil = round($minutesUntil / 60, 1);
    //                 return $this->error("Meeting belum dimulai. Dimulai dalam $hoursUntil jam lagi.", 400);
    //             } else {
    //                 return $this->error("Meeting belum dimulai. Dimulai dalam $minutesUntil menit lagi.", 400);
    //             }
    //         }

    //         // Auto-close if meeting has expired
    //         if ($meet->TglJamSelesai && now()->gt(Carbon::parse($meet->TglJamSelesai))) {
    //             $meet->update(['StatusSesi' => 'SELESAI']);
    //             Log::info('Meeting auto-closed on join attempt (expired): ' . $IDVoiceMeet);
    //             return $this->error('Meeting sudah berakhir', 400);
    //         }

    //         // Cek apakah sudah pernah join sebelumnya
    //         $existingJoin = JamaahJoin::where('IDVoiceMeet', $IDVoiceMeet)
    //             ->where('IDPerson', $IDPerson)
    //             ->first();

    //         if ($existingJoin) {
    //             // Only update if user has left (re-joining)
    //             if ($existingJoin->TglJamLeft !== null) {
    //                 // User is re-joining after leaving
    //                 $existingJoin->update([
    //                     'TglJamJoin' => now()->format('Y-m-d H:i:s'),
    //                     'TglJamLeft' => null,
    //                 ]);
    //                 Log::info('User re-joined meet: ' . $IDPerson . ' re-joined ' . $IDVoiceMeet);
    //             } else {
    //                 // User is still in meeting, don't update
    //                 Log::info('User already in meeting: ' . $IDPerson . ' already in ' . $IDVoiceMeet);
    //             }
    //         } else {
    //             // Create new record - use meet's IDPaket, not jamaah's
    //             JamaahJoin::create([
    //                 'TglJamJoin' => now()->format('Y-m-d H:i:s'),
    //                 'TglJamLeft' => null,
    //                 'IDVoiceMeet' => $IDVoiceMeet,
    //                 'IDPaket' => $meet->IDPaket, // Use meet's package, not jamaah's
    //                 'IDPerson' => $IDPerson,
    //             ]);
    //             Log::info('User joined meet for first time: ' . $IDPerson . ' joined ' . $IDVoiceMeet);
    //         }

    //         Log::info('Jamaah joined meet: ' . $IDPerson . ' joined ' . $IDVoiceMeet);

    //         return $this->success(null, 'Berhasil join meet');
    //     } catch (\Exception $e) {
    //         Log::error('Meet join error: ' . $e->getMessage());
    //         return $this->error('Gagal join meet', 500);
    //     }
    // }

    public function join(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'IDVoiceMeet' => 'required|string',
            'IDPerson' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->first());
        }

        $IDVoiceMeet = $request->input('IDVoiceMeet');
        $IDPerson = $request->input('IDPerson');

        try {
            // Cek apakah meet exists
            $meet = SesiVoiceMeet::where('IDVoiceMeet', $IDVoiceMeet)->first();

            if (!$meet) {
                return $this->notFound('Jadwal meet tidak ditemukan');
            }

            // Get all jamaah packages (jamaah bisa terdaftar di multiple paket)
            $jamaahPakets = DB::table('jamaahpaket')
                ->where('IDPerson', $IDPerson)
                ->get();

            if ($jamaahPakets->isEmpty()) {
                return $this->error('Anda belum terdaftar di paket manapun', 404);
            }

            // Validate: Jamaah harus terdaftar di paket yang sama dengan meet
            $paketIds = $jamaahPakets->pluck('IDPaket')->toArray();
            if (!in_array($meet->IDPaket, $paketIds)) {
                return $this->error(
                    'Anda tidak dapat join meeting ini karena bukan anggota paket ' . $meet->IDPaket,
                    403
                );
            }

            // Validate: Meeting must have started (allow 5 minutes early)
            if ($meet->TglJamMulai) {
                $meetStartTime = Carbon::parse($meet->TglJamMulai, 'UTC');
                $nowUtc = now('UTC');
                
                // Allow join if current time is not less than (start time - 5 minutes)
                if ($nowUtc->lt($meetStartTime->copy()->subMinutes(5))) {
                    $minutesUntil = $nowUtc->diffInMinutes($meetStartTime, false);

                    if ($minutesUntil > 60) {
                        $hoursUntil = round($minutesUntil / 60, 1);
                        return $this->error("Meeting belum dimulai. Dimulai dalam $hoursUntil jam lagi.", 400);
                    } else {
                        return $this->error("Meeting belum dimulai. Dimulai dalam $minutesUntil menit lagi.", 400);
                    }
                }
            }

            // Auto-close if meeting has expired
            if ($meet->TglJamSelesai && now('UTC')->gt(Carbon::parse($meet->TglJamSelesai, 'UTC'))) {
                $meet->update(['StatusSesi' => 'SELESAI']);
                Log::info('Meeting auto-closed on join attempt (expired): ' . $IDVoiceMeet);
                return $this->error('Meeting sudah berakhir', 400);
            }

            // Cek apakah sudah pernah join sebelumnya
            $existingJoin = JamaahJoin::where('IDVoiceMeet', $IDVoiceMeet)
                ->where('IDPerson', $IDPerson)
                ->first();

            if ($existingJoin) {
                // Only update if user has left (re-joining)
                if ($existingJoin->TglJamLeft !== null) {
                    // User is re-joining after leaving
                    $existingJoin->update([
                        'TglJamJoin' => now('UTC')->format('Y-m-d H:i:s'),
                        'TglJamLeft' => null,
                    ]);
                    Log::info('User re-joined meet: ' . $IDPerson . ' re-joined ' . $IDVoiceMeet);
                } else {
                    // User is still in meeting, don't update
                    Log::info('User already in meeting: ' . $IDPerson . ' already in ' . $IDVoiceMeet);
                }
            } else {
                // Create new record - use meet's IDPaket, not jamaah's
                JamaahJoin::create([
                    'TglJamJoin' => now('UTC')->format('Y-m-d H:i:s'),
                    'TglJamLeft' => null,
                    'IDVoiceMeet' => $IDVoiceMeet,
                    'IDPaket' => $meet->IDPaket, // Use meet's package, not jamaah's
                    'IDPerson' => $IDPerson,
                ]);
                Log::info('User joined meet for first time: ' . $IDPerson . ' joined ' . $IDVoiceMeet);
            }

            Log::info('Jamaah joined meet: ' . $IDPerson . ' joined ' . $IDVoiceMeet);

            return $this->success(null, 'Berhasil join meet');
        } catch (\Exception $e) {
            Log::error('Meet join error: ' . $e->getMessage());
            return $this->error('Gagal join meet', 500);
        }
    }

    public function leave(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'IDVoiceMeet' => 'required|string',
            'IDPerson' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->first());
        }

        $IDVoiceMeet = $request->input('IDVoiceMeet');
        $IDPerson = $request->input('IDPerson');

        try {
            Log::info('Leave meet request: ' . $IDPerson . ' leaving ' . $IDVoiceMeet);

            $join = JamaahJoin::getActiveJoin($IDVoiceMeet, $IDPerson);

            if (!$join) {
                Log::warning('Leave meet failed: No active join found for ' . $IDPerson);
                return $this->error('Anda belum join meet ini', 404);
            }

            // Convert UTC join time to App Timezone for date comparison
            $joinDate = Carbon::parse($join->TglJamJoin, 'UTC')->timezone(config('app.timezone'))->format('Y-m-d');
            $today = now()->format('Y-m-d');

            if ($joinDate !== $today) {
                Log::warning('Leave meet rejected: Join date (' . $joinDate . ') is not today (' . $today . ') for ' . $IDPerson);
                return $this->error('Tidak dapat leave meeting dari hari lain. Silakan hubungi admin jika ada masalah.', 400);
            }

            $leaveTime = now('UTC')->format('Y-m-d H:i:s'); // Explicit UTC
            Log::info('Updating TglJamLeft to: ' . $leaveTime . ' for ' . $IDPerson . ' in meet ' . $IDVoiceMeet);

            $affectedRows = JamaahJoin::where('IDVoiceMeet', $IDVoiceMeet)
                ->where('IDPerson', $IDPerson)
                ->whereNull('TglJamLeft')
                ->update(['TglJamLeft' => $leaveTime]);

            if ($affectedRows === 0) {
                Log::warning('No rows updated for ' . $IDPerson . ' - possible race condition');
                return $this->error('Gagal update leave time', 500);
            }

            Log::info('Successfully updated TglJamLeft for ' . $IDPerson . ' (' . $affectedRows . ' row(s) affected)');

            $meet = SesiVoiceMeet::where('IDVoiceMeet', $IDVoiceMeet)->first();

            if ($meet && $meet->TglJamSelesai && now('UTC')->gt(Carbon::parse($meet->TglJamSelesai, 'UTC'))) {
                $meet->update(['StatusSesi' => 'SELESAI']);
                Log::info('Meeting auto-closed: Time expired ' . $IDVoiceMeet);
            }

            Log::info('Jamaah left meet: ' . $IDPerson . ' left ' . $IDVoiceMeet);

            return $this->success(null, 'Berhasil leave meet');
        } catch (\Exception $e) {
            Log::error('Meet leave error: ' . $e->getMessage());
            return $this->error('Gagal leave meet', 500);
        }
    }

   
    public function participants(Request $request): JsonResponse
    {
        $IDVoiceMeet = $request->input('IDVoiceMeet');

        if (!$IDVoiceMeet) {
            return $this->validationError('IDVoiceMeet harus diisi');
        }

        try {
            $participants = JamaahJoin::getParticipants($IDVoiceMeet);

            $data = $participants->map(function ($participant) {
                return [
                    'IDPerson' => $participant->IDPerson,
                    'NamaJamaah' => $participant->person ? $participant->person->NamaJamaah : null,
                    'FotoProfil' => $participant->person ? $participant->person->FotoProfil : null,
                    'TglJamJoin' => $participant->TglJamJoin ? Carbon::parse($participant->TglJamJoin, 'UTC')->toIso8601String() : null,
                    'TglJamLeft' => $participant->TglJamLeft ? Carbon::parse($participant->TglJamLeft, 'UTC')->toIso8601String() : null,
                    'IsActive' => $participant->TglJamLeft === null,
                ];
            });

            return $this->success($data, 'Data peserta meet berhasil diambil');
        } catch (\Exception $e) {
            Log::error('Meet participants error: ' . $e->getMessage());
            return $this->error('Gagal mengambil data peserta meet', 500);
        }
    }

    public function checkLeaderJoined(Request $request): JsonResponse
    {
        $IDVoiceMeet = $request->input('IDVoiceMeet');

        if (!$IDVoiceMeet) {
            return $this->validationError('IDVoiceMeet harus diisi');
        }

        try {
            // Get meet info
            $meet = SesiVoiceMeet::where('IDVoiceMeet', $IDVoiceMeet)->first();

            if (!$meet) {
                return $this->notFound('Jadwal meet tidak ditemukan');
            }

            // Check if creator (leader) has joined
            $leaderJoined = JamaahJoin::where('IDVoiceMeet', $IDVoiceMeet)
                ->where('IDPerson', $meet->IDPerson) // Creator is the leader
                ->whereNull('TglJamLeft') // Still in meeting
                ->exists();

            return $this->success([
                'leader_joined' => $leaderJoined,
                'leader_id' => $meet->IDPerson,
            ], 'Status leader berhasil dicek');
        } catch (\Exception $e) {
            Log::error('Check leader joined error: ' . $e->getMessage());
            return $this->error('Gagal mengecek status leader', 500);
        }
    }

    public function getJitsiToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'IDPerson' => 'required|string',
            'IDVoiceMeet' => 'required|string',
            'IsLeader' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->first());
        }

        if ($this->jitsiSecret === '') {
            Log::error('Jitsi secret is not configured');
            return response()->json([
                'success' => false,
                'message' => 'Jitsi configuration error',
            ], 500);
        }

        $IDPerson = $request->input('IDPerson');
        $IDVoiceMeet = $request->input('IDVoiceMeet');
        $isLeaderRequested = (bool) $request->input('IsLeader', false);

        try {
            $user = Person::where('IDPerson', $IDPerson)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $meet = SesiVoiceMeet::where('IDVoiceMeet', $IDVoiceMeet)->first();

            if (!$meet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meeting not found',
                ], 404);
            }

            $isLeader = false;

            if ($isLeaderRequested) {
                $isLeaderInPaket = DB::table('jamaahpaket')
                    ->where('IDPerson', $IDPerson)
                    ->where('IDPaket', $meet->IDPaket)
                    ->where('StatusJamaah', 'Leader')
                    ->exists();

                $isCreator = $meet->IDPerson === $IDPerson;

                $isLeader = $isLeaderInPaket || $isCreator;
            }

            $roomName = 'meet-' . $IDVoiceMeet;

            $payload = [
                'aud' => 'jitsi',
                'iss' => $this->jitsiAppId,
                'sub' => $this->jitsiDomain,
                'room' => $roomName,
                'exp' => time() + (365 * 24 * 60 * 60),
                'nbf' => time() - 10,
                'context' => [
                    'user' => [
                        'id' => $IDPerson,
                        'name' => $user->NamaJamaah ?? 'User',
                        'email' => $user->Email ?? '',
                        'avatar' => $user->FotoProfil ?? '',
                        'moderator' => $isLeader ? 'true' : 'false',
                    ],
                ],
            ];

            $token = JWT::encode($payload, $this->jitsiSecret, 'HS256');

            return response()->json([
                'success' => true,
                'token' => $token,
                'message' => 'Token generated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Get Jitsi token error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate token',
            ], 500);
        }
    }

    private function isLeader($IDPerson): bool
    {
        return DB::table('jamaahpaket')
            ->where('IDPerson', $IDPerson)
            ->where('StatusJamaah', 'Leader')
            ->exists();
    }

   
    private function getLeaderPackage($IDPerson)
    {
        return DB::table('jamaahpaket')
            ->where('IDPerson', $IDPerson)
            ->where('StatusJamaah', 'Leader')
            ->first();
    }
}
