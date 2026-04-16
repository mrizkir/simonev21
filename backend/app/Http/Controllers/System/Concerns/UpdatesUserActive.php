<?php

namespace App\Http\Controllers\System\Concerns;

use App\Models\User;
use Illuminate\Http\Request;

trait UpdatesUserActive
{
  /**
   * Ubah kolom active pada user (hanya untuk default_role yang sesuai modul).
   */
  public function updateActive(Request $request, $id)
  {
    $this->hasPermissionTo($this->usersActiveUpdatePermission());

    $this->validate($request, [
      'active' => 'required|integer|in:0,1',
    ]);

    $user = User::find($id);
    if (is_null($user) || $user->default_role !== $this->usersActiveExpectedDefaultRole()) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => ['User tidak ditemukan atau tidak sesuai.'],
      ], 422);
    }

    $active = (int) $request->input('active');
    $user->active = $active;
    $user->updated_at = \Carbon\Carbon::now()->toDateTimeString();
    $user->save();

    \App\Models\System\ActivityLog::log($request, [
      'object' => $this->guard()->user(),
      'object_id' => $this->guard()->user()->id,
      'user_id' => $this->getUserid(),
      'message' => 'Mengubah status aktif user ('.$user->username.') menjadi '.$active,
    ]);

    return Response()->json([
      'status' => 1,
      'pid' => 'update',
      'user' => $user,
      'message' => 'Status aktif user berhasil diubah.',
    ], 200);
  }
}
