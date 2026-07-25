<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\HasDataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    use HasDataTable;

    protected array $searchable    = ['nama_lengkap', 'username', 'email', 'role', 'no_hp'];
    protected array $filterable    = ['role', 'status'];
    protected array $sortable      = ['nama_lengkap', 'username', 'role', 'status', 'created_at'];
    protected string $defaultSortBy = 'created_at';

    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(Request $request): View
    {
        $query = User::query();
        $users = $this->buildDataTableQuery($query, $request);

        $totalAdmin    = User::where('role', Role::Admin->value)->count();
        $totalMarketing = User::where('role', Role::Marketing->value)->count();
        $totalManajemen = User::where('role', Role::Manajemen->value)->count();
        $totalAll       = User::count();

        return view('admin.users.index', array_merge(
            compact('users', 'totalAdmin', 'totalMarketing', 'totalManajemen', 'totalAll'),
            $this->dataTableMeta($request),
        ));
    }

    public function create(): View
    {
        return view('admin.users.create', ['roles' => Role::cases()]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => Role::cases()]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        if ($currentUser->is($user) && $request->input('role') !== $currentUser->role->value) {
            return back()
                ->withInput()
                ->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $this->userService->update($request->validated(), $user->id);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        if ($currentUser->is($user)) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $result = $this->userService->delete($user->id);

        return redirect()
            ->route('admin.users.index')
            ->with($result['deleted'] ? 'success' : 'warning', $result['message']);
    }
}