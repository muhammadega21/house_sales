<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Traits\HasFileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    use HasFileUpload;

    public function create(array $data, Model|string|null $model = null): User
    {
        $data['password'] = Hash::make($data['password']);

        if (($data['foto_profil'] ?? null) instanceof UploadedFile) {
            $data['foto_profil'] = $this->uploadFile($data['foto_profil'], 'foto-profil');
        } else {
            unset($data['foto_profil']);
        }

        unset($data['password_confirmation']);

        /** @var User $user */
        $user = parent::create($data, User::class);

        return $user;
    }

    public function update(array $data, Model|string|int $model, string|int|null $id = null): User
    {
        $id ??= $model;

        /** @var User $user */
        $user = $this->findById(User::class, $id);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (($data['foto_profil'] ?? null) instanceof UploadedFile) {
            $data['foto_profil'] = $this->uploadFile(
                $data['foto_profil'],
                'foto-profil',
                $user->foto_profil,
            );
        } elseif (!empty($data['remove_foto_profil'])) {
            // remove request: delete old file and set to null
            $this->deleteFile($user->foto_profil);
            $data['foto_profil'] = null;
        } else {
            unset($data['foto_profil']);
        }

        unset($data['password_confirmation']);

        $user->update($data);

        return $user;
    }

    /**
     * Deactivate users with operational history; otherwise delete permanently.
     *
     * @return array{deleted: bool, message: string}
     */
    public function delete(Model|string|int $model, string|int|null $id = null): array
    {
        $id ??= $model;

        /** @var User $user */
        $user = $this->findById(User::class, $id);

        $hasBooking = DB::table('booking')->where('id_marketing', $id)->exists();
        $hasProspek = DB::table('prospek')->where('id_marketing', $id)->exists();

        if ($hasBooking || $hasProspek) {
            $user->update(['status' => 'non_aktif']);

            return [
                'deleted' => false,
                'message' => 'Pengguna memiliki data transaksi terkait. Status diubah menjadi Non-Aktif.',
            ];
        }

        $this->deleteFile($user->foto_profil);
        $user->delete();

        return [
            'deleted' => true,
            'message' => 'Pengguna berhasil dihapus.',
        ];
    }

    public function toggleStatus(int|string $id): User
    {
        /** @var User $user */
        $user = $this->findById(User::class, $id);
        $user->update([
            'status' => $user->status === 'aktif' ? 'non_aktif' : 'aktif',
        ]);

        return $user;
    }

    public function getUsers(Request $request): LengthAwarePaginator
    {
        $filters = $request->all();
        $filters['per_page'] = 10;

        /** @var LengthAwarePaginator $users */
        $users = parent::getAllWithFilters(
            $filters,
            User::class,
            ['nama_lengkap', 'username', 'role'],
        );

        return $users;
    }

    public function createUser(array $data): User
    {
        return $this->create($data);
    }

    public function updateUser(array $data, int|string $id): User
    {
        return $this->update($data, $id);
    }

    /**
     * @return array{deleted: bool, message: string}
     */
    public function deleteUser(int|string $id): array
    {
        return $this->delete($id);
    }
}
