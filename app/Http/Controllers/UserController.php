<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Apply search if provided
        if ($request->has('search') && $request->get('search') != '') {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        // Get number of entries per page
        $entries = $request->get('entries', 5);

        // Get users with pagination
        $users = $query->paginate($entries);

        // Transform the data for the data-table component
        $rows = $users->through(function ($user, $index) use ($users) {
            return [
                'id' => $users->firstItem() + $index,
                'name' => $user->name,
                'email' => $user->email,
                'user_id' => $user->id,
            ];
        });

        $actions = [
            [
                'view' => null,
                'inline' => function ($row) {
                    return '<button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target="#deleteUserModal" onclick="setDeleteUser('.$row['user_id'].')">
                            <i class="fa fa-trash"></i> Delete
                        </button>';
                },
            ],
        ];

        return view('admin.all-users', compact('users', 'rows', 'actions'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.all-users')->with('success', 'User deleted successfully.');
    }
}
