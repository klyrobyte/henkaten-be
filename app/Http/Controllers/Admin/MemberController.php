<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig) {}

    // ──────────────────────────────────────────────────────────────────
    // HALAMAN UTAMA — mengganti page-members + renderMembers() + updateStats()
    // GET /admin/members
    // ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $factory = $request->get('factory', 'all');
        $shift   = $request->get('shift',   'all');
        $search  = $request->get('q',       '');

        $query = Member::query();
        if ($factory !== 'all') $query->where('factory', $factory);
        if ($shift   !== 'all') $query->where('shift',   $shift);
        if ($search)            $query->where('nama', 'like', "%{$search}%");
        $members = $query->orderBy('nama')->get();

        // Stats bar (mengganti updateStats() JS)
        $stats = [
            'total'      => Member::count(),
            'f2'         => Member::where('factory', 'Factory 2')->count(),
            'f34'        => Member::where('factory', 'Factory 3 & 4')->count(),
            'absen_today'=> \App\Models\AbsenceRecord::where('tanggal', today())
                               ->where('status', 'absen')->count(),
        ];

        // Daftar mesin per factory (mengganti const factoryMachines JS)
        $mesinList = [
            'Factory 2'     => $this->factoryConfig->getAllMachines('Factory 2'),
            'Factory 3 & 4' => $this->factoryConfig->getAllMachines('Factory 3 & 4'),
        ];

        return view('admin.member.index',
            compact('members', 'stats', 'factory', 'shift', 'search', 'mesinList'));
    }

    // ──────────────────────────────────────────────────────────────────
    // DETAIL (JSON) — untuk AJAX sheet panel
    // GET /admin/members/{member}
    // ──────────────────────────────────────────────────────────────────
    public function show(Member $member)
    {
        $history = $member->absenceRecords()
            ->orderByDesc('tanggal')
            ->limit(10)
            ->get(['tanggal','status','reason']);

        return response()->json([
            'member'  => $member,
            'history' => $history,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // SIMPAN BARU — mengganti saveMember() JS (mode tambah)
    // POST /admin/members
    // ──────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $this->validateMember($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members', 'public');
        } elseif ($request->filled('photo_base64')) {
            $data['photo'] = $this->storeBase64Photo($request->photo_base64);
        }

        $member = Member::create($data);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'member' => $member]);
        }
        return back()->with('success', '✅ Member ditambah: ' . $member->nama);
    }

    // ──────────────────────────────────────────────────────────────────
    // UPDATE — mengganti saveMember() JS (mode edit)
    // PUT /admin/members/{member}
    // ──────────────────────────────────────────────────────────────────
    public function update(Request $request, Member $member)
    {
        $data = $this->validateMember($request);

        if ($request->hasFile('photo')) {
            if ($member->photo && !str_starts_with($member->photo, 'data:')) {
                Storage::disk('public')->delete($member->photo);
            }
            $data['photo'] = $request->file('photo')->store('members', 'public');
        } elseif ($request->filled('photo_base64')) {
            $data['photo'] = $this->storeBase64Photo($request->photo_base64);
        }

        $member->update($data);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'member' => $member->fresh()]);
        }
        return back()->with('success', '✅ Member diupdate: ' . $member->nama);
    }

    // ──────────────────────────────────────────────────────────────────
    // HAPUS — mengganti deleteMember(id) JS
    // DELETE /admin/members/{member}
    // ──────────────────────────────────────────────────────────────────
    public function destroy(Member $member)
    {
        if ($member->photo && !str_starts_with($member->photo, 'data:')) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Member dihapus');
    }

    // ──────────────────────────────────────────────────────────────────
    // HAPUS SEMUA — mengganti clearAllMembers() JS
    // DELETE /admin/members/clear-all
    // ──────────────────────────────────────────────────────────────────
    public function clearAll()
    {
        Member::all()->each(function ($m) {
            if ($m->photo && !str_starts_with($m->photo, 'data:')) {
                Storage::disk('public')->delete($m->photo);
            }
            $m->delete();
        });
        return response()->json(['ok' => true]);
    }

    // ──────────────────────────────────────────────────────────────────
    // LIST JSON (AJAX) — untuk halaman lain (dailyassignment, dll)
    // GET /admin/members/list?factory=&shift=
    // ──────────────────────────────────────────────────────────────────
    public function list(Request $request)
    {
        $members = Member::query()
            ->when($request->factory, fn($q) => $q->where('factory', $request->factory))
            ->when($request->shift,   fn($q) => $q->where('shift', $request->shift))
            ->where('status', 'active')
            ->orderBy('nama')
            ->get(['id', 'nama', 'jabatan', 'shift', 'factory', 'mesin', 'photo']);

        return response()->json($members);
    }

    // ──────────────────────────────────────────────────────────────────
    // IMPORT — mengganti confirmImport() + processExcelFile() JS
    // JS tetap parse Excel di browser, lalu kirim JSON array ke sini
    // POST /admin/members/import
    // ──────────────────────────────────────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'members'           => 'required|array|min:1',
            'members.*.name'    => 'required|string|max:100',
            'members.*.factory' => 'required|string',
            'members.*.shift'   => 'required|in:A,B',
            'replace'           => 'boolean',
        ]);

        // replace=true → hapus semua dulu (mengganti mode "Ganti semua" di JS)
        if ($request->boolean('replace')) {
            Member::all()->each(fn($m) => $m->delete());
        }

        $added = 0; $skipped = 0;
        foreach ($request->members as $row) {
            $exists = Member::where('nama', $row['name'])
                ->where('factory', $row['factory'])
                ->where('shift',   $row['shift'])
                ->exists();

            if ($exists && !$request->boolean('replace')) {
                $skipped++;
                continue;
            }

            Member::updateOrCreate(
                ['nama' => $row['name'], 'factory' => $row['factory'], 'shift' => $row['shift']],
                [
                    'jabatan' => $row['role']  ?? 'Operator',
                    'mesin'   => $row['mesin'] ?? '',
                    'nik'     => $row['nik']   ?? '',
                    'status'  => 'active',
                ]
            );
            $added++;
        }

        return response()->json([
            'ok'      => true,
            'added'   => $added,
            'skipped' => $skipped,
            'total'   => Member::count(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // EXPORT CSV — mengganti exportMembers() JS (pakai SheetJS)
    // GET /admin/members/export
    // ──────────────────────────────────────────────────────────────────
    public function export()
    {
        $members  = Member::orderBy('factory')->orderBy('shift')->orderBy('nama')->get();
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
            foreach ($rows as $r) fputcsv($h, $r);
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // DOWNLOAD TEMPLATE — mengganti downloadTemplate() JS
    // GET /admin/members/template
    // ──────────────────────────────────────────────────────────────────
    public function downloadTemplate()
    {
        $rows = [
            ['Nama', 'Factory', 'Shift', 'Jabatan', 'NIK', 'Mesin'],
            ['Contoh: Budi Santoso', 'Factory 2',     'A', 'Operator', '12345', 'Robot 1'],
            ['Contoh: Siti Rahma',   'Factory 3 & 4', 'B', 'SPV',      '67890', '#01-1300T'],
        ];

        return Response::stream(function () use ($rows) {
            $h = fopen('php://output', 'w');
            foreach ($rows as $r) fputcsv($h, $r);
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="HENKATEN_Template_Member.csv"',
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function validateMember(Request $request): array
    {
        return $request->validate([
            'nama'    => 'required|string|max:100',
            'nik'     => 'nullable|string|max:50',
            'jabatan' => 'required|in:Operator,SPV,TL,GL,KY',
            'factory' => 'required|in:Factory 2,Factory 3 & 4',
            'shift'   => 'required|in:A,B',
            'mesin'   => 'nullable|string|max:100',
            'status'  => 'required|in:active,inactive',
        ]);
    }

    private function storeBase64Photo(string $base64): string
    {
        if (!str_starts_with($base64, 'data:image')) return $base64;
        $ext  = explode('/', explode(';', $base64)[0])[1];
        $data = base64_decode(explode(',', $base64)[1]);
        $path = 'members/' . uniqid() . '.' . $ext;
        Storage::disk('public')->put($path, $data);
        return $path;
    }
}
