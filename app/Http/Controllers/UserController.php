<?php

// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;

class UserController extends Controller
{
    
    public function index()
    {
        $users = User::all();
        return view('user', compact('users')); 
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'roles' => 'required|string',
        ]);
    
        try {
            $user = User::findOrFail($id);
            
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'roles' => $request->roles,
            ]);
    
            return redirect()->route('users.index')->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Gagal mengubah data. Silahkan coba lagi.');
        }
    }    

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required|string',
        ]);
    
        try {
            User::create([
                'name' => $request->name,
                'password' => $request->password, 
                'email' => $request->email,
                'roles' => $request->roles,
            ]);
    
            return redirect()->route('users.index')->with('success', 'Register berhasil');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Gagal register data. Silahkan coba lagi.');
        }
    }    

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('users.index')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Gagal menghapus data. Silakan coba lagi.');
        }
    }

}
