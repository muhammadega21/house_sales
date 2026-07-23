# CODING STANDARDS

## PHP / Laravel

## Setup Project (Laravel 12)

- Middleware didaftarkan di `bootstrap/app.php`
- Service provider didaftarkan di `bootstrap/providers.php`
- Auth menggunakan Laravel Fortify / starter kit baru

### Naming Convention

- Class: PascalCase (`UnitRumahController`, `Konsumen`)
- Method: camelCase (`storeBooking`, `getLaporanPenjualan`)
- Variable: camelCase (`$totalPenjualan`, `$idKonsumen`)
- Constant: UPPER_SNAKE_CASE (`MAX_UPLOAD_SIZE`)
- Table: snake_case plural (`unit_rumah`, `konsumen`)
- Column: snake_case (`nama_lengkap`, `tanggal_booking`)
- Route name: dot notation (`admin.unit-rumah.index`)
- View file: kebab-case (`unit-rumah/index.blade.php`)

### Controller Structure

```php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitRumah;
use App\Http\Requests\UnitRumahRequest;
use App\Services\UnitRumahService;

class UnitRumahController extends Controller
{
    public function __construct(
        private readonly UnitRumahService $service
    ) {}

    public function index()
    {
        $units = $this->service->getAllWithFilters(request());
        return view('admin.unit-rumah.index', compact('units'));
    }

    public function store(UnitRumahRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('admin.unit-rumah.index')
            ->with('success', 'Unit rumah berhasil ditambahkan.');
    }
}
```

### Model Structure

```php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitRumah extends Model
{
    protected string $table = 'unit_rumah';

    protected array $fillable = [
        'id_perumahan', 'kode_unit', 'tipe_rumah', 'kategori',
        'jenis_ketersediaan', 'luas_tanah', 'luas_bangunan',
        'harga_jual', 'status_unit',
    ];

    protected function casts(): array
    {
        return [
            'harga_jual' => 'decimal:2',
            'luas_tanah' => 'decimal:2',
        ];
    }

    public function perumahan(): BelongsTo
    {
        return $this->belongsTo(Perumahan::class, 'id_perumahan');
    }

    public function booking(): HasMany
    {
        return $this->hasMany(Booking::class, 'id_unit');
    }

    public function scopeTersedia($query)
    {
        return $query->where('status_unit', 'tersedia');
    }

}
```

### Form Request Validation

```php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitRumahRequest extends FormRequest
{
public function authorize(): bool
{
return true;
}

    public function rules(): array
    {
        return [
            'id_perumahan' => 'required|exists:perumahan,id',
            'kode_unit' => 'required|string|max:20',
            'tipe_rumah' => 'required|string|max:50',
            'kategori' => 'required|in:subsidi,non_subsidi',
            'jenis_ketersediaan' => 'required|in:ready_stock,indent',
            'luas_tanah' => 'required|numeric|min:0',
            'luas_bangunan' => 'required|numeric|min:0|lte:luas_tanah',
            'harga_jual' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'luas_bangunan.lte' => 'Luas bangunan tidak boleh lebih dari luas tanah.',
            'harga_jual.min' => 'Harga jual harus lebih dari 0.',
        ];
    }

}
```

### Service Layer

```php
declare(strict_types=1);

namespace App\Services;

use App\Models\UnitRumah;
use App\Models\Perumahan;
use Illuminate\Support\Facades\DB;

class UnitRumahService
{
public function create(array $data): UnitRumah
    {
        return DB::transaction(function () use ($data) {
$unit = UnitRumah::create($data);

            // Update total unit di perumahan
            Perumahan::where('id', $data['id_perumahan'])
                ->increment('total_unit');

            return $unit;
        });
    }

}
```

### Blade Template

#### Layout Structure

resources/views/
├── layouts/
│ ├── app.blade.php (main layout)
│ ├── sidebar.blade.php
│ ├── topbar.blade.php
│ └── footer.blade.php
├── components/
│ ├── card.blade.php
│ ├── table.blade.php
│ ├── modal.blade.php
│ ├── alert.blade.php
│ ├── badge.blade.php
│ ├── pagination.blade.php
│ └── form-input.blade.php
├── admin/
│ ├── dashboard.blade.php
│ ├── unit-rumah/
│ │ ├── index.blade.php
│ │ ├── create.blade.php
│ │ └── edit.blade.php
│ └── ...
├── marketing/
│ └── ...
├── manajemen/
│ └── ...
└── auth/
└── login.blade.php

Blade Best Practices

- Gunakan @extends, @section, @yield
- Gunakan components untuk elemen reusable
- Escape output: {{ $variable }} (bukan {!! !!})
- Gunakan @csrf di setiap form
- Gunakan @method('PUT') / @method('DELETE') untuk method spoofing

JavaScript

- Minimal JavaScript (gunakan Alpine.js 3 untuk interaktivitas ringan)
- Chart.js 4 untuk grafik
- AJAX hanya untuk fitur yang memerlukan (simulasi pembayaran, search)
- Hindari jQuery (gunakan vanilla JS / Alpine.js 3)

Git Commit Convention

feat: tambah fitur booking rumah
fix: perbaiki validasi NIK duplikat
docs: update dokumentasi API
style: perbaiki tampilan dashboard
refactor: refactor service pembayaran
chore: update dependency
