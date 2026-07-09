<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Factory;
use App\Services\FactoryConfigService;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

/**
 * @group Member
 * 
 * APIs for managing Member.
 */
class MemberController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig)
    {
    }

    //                                  
    // HALAMAN UTAMA  - mengganti page-members + renderMembers() + updateStats()
    // GET /admin/member
    // @rizky
    //                                  
    public function index(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $isSuperAdmin = $user->isSuperAdmin();
        $allowedFactories = (array) $user->factory;

        $factory = $request->get('factory', 'all');
        $shift = $request->get('shift', 'all');
        $search = $request->get('q', '');

        $query = Member::where('sc_id', $scId);

        if (!$isSuperAdmin) {
            if ($factory === 'all') {
                if (!empty($allowedFactories)) {
                    $query->whereIn('factory', $allowedFactories);
                }
            } else {
                if (!empty($allowedFactories) && !in_array($factory, $allowedFactories)) {
                    abort(403, 'Unauthorized factory access.');
                }
                $query->where('factory', $factory);
            }
        } else {
            if ($factory !== 'all') {
                $query->where('factory', $factory);
            }
        }

        if ($shift !== 'all')
            $query->whereIn('shift', \App\Services\NonShiftResolver::shiftsFor($shift));
        if ($search)
            $query->where('nama', 'like', "%{$search}%");
        $members = $query->orderBy('nama')->get();

        // Stats bar
        $stats = [
            'total' => (!$isSuperAdmin && !empty($allowedFactories))
                ? Member::where('sc_id', $scId)->whereIn('factory', $allowedFactories)->count()
                : Member::where('sc_id', $scId)->count(),
            'absen_today' => \App\Models\AbsenceRecord::where('sc_id', $scId)->where('tanggal', today())
                ->where('status', 'absen')
                ->when(!$isSuperAdmin && !empty($allowedFactories), function ($q) use ($allowedFactories) {
                    return $q->whereHas('member', function ($mq) use ($allowedFactories) {
                        $mq->whereIn('factory', $allowedFactories);
                    });
                })
                ->count(),
        ];

        $factories = Factory::where('sc_id', $scId)->get();
        if (!$isSuperAdmin && !empty($allowedFactories)) {
            $factories = $factories->filter(fn($f) => in_array($f->name, $allowedFactories));
        }

        foreach ($factories as $fac) {
            $stats['f_' . $fac->id] = [
                'name' => $fac->short_label,
                'count' => Member::where('sc_id', $scId)->where('factory', $fac->name)->count(),
            ];
        }

        // Daftar mesin per factory (mengganti const factoryMachines JS)
        $mesinList = [];
        foreach ($factories as $fac) {
            $mesinList[$fac->name] = $this->factoryConfig->getAllMachines($fac->name);
        }

        return view(
            'admin.member.index',
            compact('members', 'stats', 'factory', 'shift', 'search', 'mesinList', 'factories')
        );
    }

    //                                  
    // DETAIL (JSON)  - untuk AJAX sheet panel
    // GET /admin/members/{member}
    //                                  
    public function show(Member $member)
    {
        $user = Auth::user();
        $scId = ScContext::id();

        if ($member->sc_id != $scId) {
            abort(403, 'Unauthorized access to this member.');
        }

        if (!$user->isSuperAdmin() && !in_array($member->factory, (array) $user->factory)) {
            abort(403, 'Unauthorized factory access.');
        }

        $history = $member->absenceRecords()
            ->orderByDesc('tanggal')
            ->limit(10)
            ->get(['tanggal', 'status', 'reason']);

        return response()->json([
            'member' => $member,
            'history' => $history,
        ]);
    }

    //                                  
    // SIMPAN BARU  - mengganti saveMember() JS (mode tambah)
    // POST /admin/members
    //                                  
    public function store(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $data = $this->validateMember($request);

        if (!$user->isSuperAdmin() && !in_array($data['factory'], (array) $user->factory)) {
            abort(403, 'Unauthorized factory access.');
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $data['photo'] = $this->processAndStorePhoto(
                file_get_contents($file->getRealPath()),
                $file->hashName()
            );
        } elseif ($request->filled('photo_base64')) {
            $data['photo'] = $this->storeBase64Photo($request->photo_base64);
        }

        $data['sc_id'] = $scId;
        $member = Member::create($data);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'member' => $member]);
        }
        return back()->with('success', '✅ Member ditambah: ' . $member->nama);
    }

    //                                  
    // UPDATE  - mengganti saveMember() JS (mode edit)
    // PUT /admin/members/{member}
    //                                  
    public function update(Request $request, Member $member)
    {
        $user = Auth::user();
        $scId = ScContext::id();

        if ($member->sc_id != $scId) {
            abort(403, 'Unauthorized access to this member.');
        }

        if (!$user->isSuperAdmin() && !in_array($member->factory, (array) $user->factory)) {
            abort(403, 'Unauthorized factory access.');
        }

        $data = $this->validateMember($request);

        if (!$user->isSuperAdmin() && !in_array($data['factory'], (array) $user->factory)) {
            abort(403, 'Unauthorized factory access.');
        }

        if ($request->hasFile('photo')) {
            if ($member->photo && !str_starts_with($member->photo, 'data:')) {
                $delPath = str_starts_with($member->photo, 'members/')
                    ? basename($member->photo) : $member->photo;
                Storage::disk('members')->delete($delPath);
            }
            $file = $request->file('photo');
            $data['photo'] = $this->processAndStorePhoto(
                file_get_contents($file->getRealPath()),
                $file->hashName()
            );
        } elseif ($request->filled('photo_base64')) {
            $data['photo'] = $this->storeBase64Photo($request->photo_base64);
        }

        $member->update($data);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'member' => $member->fresh()]);
        }
        return back()->with('success', '✅ Member diupdate: ' . $member->nama);
    }

    //                                  
    // HAPUS  - mengganti deleteMember(id) JS
    // DELETE /admin/members/{member}
    //                                  
    public function destroy(Member $member)
    {
        $user = Auth::user();
        $scId = ScContext::id();

        if ($member->sc_id != $scId) {
            abort(403, 'Unauthorized access to this member.');
        }

        if (!$user->isSuperAdmin() && !in_array($member->factory, (array) $user->factory)) {
            abort(403, 'Unauthorized factory access.');
        }

        if ($member->photo && !str_starts_with($member->photo, 'data:')) {
            $delPath = str_starts_with($member->photo, 'members/')
                ? basename($member->photo) : $member->photo;
            Storage::disk('members')->delete($delPath);
        }
        $member->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Member dihapus');
    }

    //                                  
    // HAPUS SEMUA  - mengganti clearAllMembers() JS
    // DELETE /admin/members/clear-all
    //
    // Requires explicit confirmation header to prevent accidental or
    // automated mass-deletion. The client must send:
    //   X-Confirm-Action: DELETE_ALL_MEMBERS
    //                                  
    public function clearAll(Request $request)
    {
        // Guard: require explicit confirmation header to prevent accidental mass-delete
        if ($request->header('X-Confirm-Action') !== 'DELETE_ALL_MEMBERS') {
            return response()->json([
                'ok' => false,
                'message' => 'Konfirmasi penghapusan tidak valid.',
            ], 422);
        }

        $user = Auth::user();
        $scId = ScContext::id();
        $query = Member::where('sc_id', $scId);
        if (!$user->isSuperAdmin()) {
            $query->whereIn('factory', (array) $user->factory);
        }

        $query->get()->each(function ($m) {
            if ($m->photo && !str_starts_with($m->photo, 'data:')) {
                $delPath = str_starts_with($m->photo, 'members/')
                    ? basename($m->photo) : $m->photo;
                Storage::disk('members')->delete($delPath);
            }
            $m->delete();
        });
        return response()->json(['ok' => true]);
    }

    //                                  
    // LIST JSON (AJAX)  - untuk halaman lain (dailyassignment, dll)
    // GET /admin/members/list?factory=&shift=
    //                                  
    public function list(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $query = Member::where('sc_id', $scId);

        if (!$user->isSuperAdmin()) {
            $allowed = (array) $user->factory;
            $query->whereIn('factory', $allowed);
            if ($request->factory && !in_array($request->factory, $allowed)) {
                return response()->json([]);
            }
        }

        $members = $query
            ->when($request->factory, fn($q) => $q->where('factory', $request->factory))
            ->when($request->shift, fn($q) => $q->whereIn('shift', \App\Services\NonShiftResolver::shiftsFor($request->shift)))
            ->where('status', 'active')
            ->orderBy('nama')
            ->get(['id', 'nama', 'jabatan', 'shift', 'factory', 'mesin', 'photo']);

        return response()->json($members);
    }

    //                                  
    // IMPORT  - mengganti confirmImport() + processExcelFile() JS
    // JS tetap parse Excel di browser, lalu kirim JSON array ke sini
    // POST /admin/members/import
    //                                  
    public function import(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $isSuperAdmin = $user->isSuperAdmin();
        $allowedFactories = (array) $user->factory;

        $request->validate([
            'members' => 'required|array|min:1',
            'members.*.name' => 'required|string|max:100',
            'members.*.factory' => 'required|string',
            'members.*.shift' => 'required|in:A,B,NS',
            'replace' => 'boolean',
        ]);

        foreach ($request->members as $row) {
            if (!$isSuperAdmin && !in_array($row['factory'], $allowedFactories)) {
                abort(403, "Unauthorized factory access for: " . $row['factory']);
            }
        }

        // replace=true → hapus semua dulu (hanya yang diijinkan)
        if ($request->boolean('replace')) {
            $delQuery = Member::where('sc_id', $scId);
            if (!$isSuperAdmin) {
                $delQuery->whereIn('factory', $allowedFactories);
            }
            $delQuery->get()->each(fn($m) => $m->delete());
        }

        $added = 0;
        $skipped = 0;
        foreach ($request->members as $row) {
            $exists = Member::where('sc_id', $scId)
                ->where('nama', $row['name'])
                ->where('factory', $row['factory'])
                ->where('shift', $row['shift'])
                ->exists();

            if ($exists && !$request->boolean('replace')) {
                $skipped++;
                continue;
            }

            Member::updateOrCreate(
                ['sc_id' => $scId, 'nama' => $row['name'], 'factory' => $row['factory'], 'shift' => $row['shift']],
                [
                    'jabatan' => $row['role'] ?? 'Operator',
                    'mesin' => $row['mesin'] ?? '',
                    'nik' => $row['nik'] ?? '',
                    'status' => 'active',
                ]
            );
            $added++;
        }

        return response()->json([
            'ok' => true,
            'added' => $added,
            'skipped' => $skipped,
            'total' => Member::where('sc_id', $scId)->count(),
        ]);
    }

    //                                  
    // EXPORT CSV  - mengganti exportMembers() JS (pakai SheetJS)
    // GET /admin/members/export
    //                                  
    public function export()
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $query = Member::where('sc_id', $scId);
        if (!$user->isSuperAdmin()) {
            $query->whereIn('factory', (array) $user->factory);
        }

        $members = $query->orderBy('factory')->orderBy('shift')->orderBy('nama')->get();
        $filename = 'HENKATEN_Members_' . today()->toDateString() . '.csv';

        $rows = [
            ['DAFTAR MEMBER HENKATEN BOARD'],
            ['Total: ' . $members->count()],
            [],
            ['Nama', 'NIK', 'Jabatan', 'Mesin', 'Factory', 'Shift', 'Status'],
        ];
        foreach ($members as $m) {
            $rows[] = [$m->nama, $m->nik ?? '', $m->jabatan, $m->mesin ?? '', $m->factory, $m->shift, $m->status];
        }

        return Response::stream(function () use ($rows) {
            $h = fopen('php://output', 'w');
            foreach ($rows as $r)
                fputcsv($h, $r);
            fclose($h);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    //                                  
    // DOWNLOAD TEMPLATE  - mengganti downloadTemplate() JS
    // GET /admin/members/template
    //                                  
    public function downloadTemplate()
    {
        $rows = [
            ['Nama', 'Factory', 'Shift', 'Jabatan', 'NIK', 'Mesin'],
            ['Contoh: Budi Santoso', 'Factory 2', 'A', 'Operator', '12345', 'Robot 1'],
            ['Contoh: Siti Rahma', 'Factory 3 & 4', 'B', 'SPV', '67890', '#01-1300T'],
        ];

        return Response::stream(function () use ($rows) {
            $h = fopen('php://output', 'w');
            foreach ($rows as $r)
                fputcsv($h, $r);
            fclose($h);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="HENKATEN_Template_Member.csv"',
        ]);
    }

    //   Private helpers                        ─

    private function validateMember(Request $request): array
    {
        return $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => 'nullable|string|max:50',
            'jabatan' => 'required|in:Operator,SPV,TL,GL,CL,KY', // ponytail: CL added above GL/TL
            'factory' => 'required|string',
            // @rizkydaffy: added NS (Non-Shift) validation
            'shift' => 'required|in:A,B,NS',
            'mesin' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);
    }

    private function storeBase64Photo(string $base64): string
    {
        if (!str_starts_with($base64, 'data:image'))
            return $base64;

        // Extract declared extension from data URI
        $ext = explode('/', explode(';', $base64)[0])[1];
        $data = base64_decode(explode(',', $base64)[1]);

        //   Magic-byte MIME validation  - prevent disguised non-image uploads  -
        // Inspect the first 12 bytes of the decoded binary to verify it is
        // actually an image, regardless of what the data URI header claims.
        $allowedMimes = [
            'image/jpeg' => ["\xFF\xD8\xFF"],
            'image/png' => ["\x89PNG\r\n\x1a\n"],
            'image/gif' => ['GIF87a', 'GIF89a'],
            'image/webp' => ['RIFF'],  // RIFF....WEBP checked below
        ];
        $header = substr($data, 0, 12);
        $validImage = false;
        foreach ($allowedMimes as $mime => $signatures) {
            foreach ($signatures as $sig) {
                if (str_starts_with($header, $sig)) {
                    // Extra check for WebP: bytes 8-11 must be 'WEBP'
                    if ($mime === 'image/webp' && substr($header, 8, 4) !== 'WEBP') {
                        continue;
                    }
                    $validImage = true;
                    break 2;
                }
            }
        }

        if (!$validImage) {
            \Log::warning('MemberController: rejected invalid base64 photo upload (magic byte check failed)', [
                'declared_ext' => $ext,
                'header_hex' => bin2hex($header),
            ]);
            abort(422, 'Format foto tidak valid.');
        }

        $allowedExts = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($ext), $allowedExts)) {
            abort(422, 'Ekstensi foto tidak diizinkan.');
        }

        return $this->processAndStorePhoto($data, uniqid() . '.' . $ext);
    }

    private function processAndStorePhoto(string $imageData, string $filename): string
    {
        $maxWidth = 300;
        $maxHeight = 300;
        $jpegQuality = 80;

        $image = @imagecreatefromstring($imageData);
        if (!$image) {
            // Fallback if imagecreatefromstring fails but magic bytes passed
            $filename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
            Storage::disk('members')->put($filename, $imageData);
            return $filename;
        }

        $origW = imagesx($image);
        $origH = imagesy($image);

        $ratio = min($maxWidth / $origW, $maxHeight / $origH);
        // Only downscale if needed, but always process to JPEG
        $newW = $origW;
        $newH = $origH;
        if ($ratio < 1) {
            $newW = (int) round($origW * $ratio);
            $newH = (int) round($origH * $ratio);
        }

        $resized = imagecreatetruecolor($newW, $newH);
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $white);

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        ob_start();
        imagejpeg($resized, null, $jpegQuality);
        $processedData = ob_get_clean();

        imagedestroy($image);
        imagedestroy($resized);

        $filename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
        Storage::disk('members')->put($filename, $processedData);

        return $filename;
    }
}
