<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //index
    public function index(Request $request) {
        // $users = User::paginate(10);
        $users = DB::table('users')->when($request->keyword, function ($query) use ($request) {
            $query->Where('name', 'like', "%{$request->keyword}%")
                ->orWhere('email', 'like', "%{$request->keyword}%")
                ->orWhere('phone', 'like', "%{$request->keyword}%");
        })->orderBy('id', 'desc')->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    //create
    function create() {
        return view('pages.users.create');
    }

    //store
    public function store(Request $request) {
        $request->validate([
            'name' =>'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required',
            'role' => 'required'
        ]);

        User::create($request->all());
        return redirect()->route('users.index')->with('success', 'user created successfully');
    }

    //edit
    public function edit(User $user) {
        return view('pages.users.edit', compact('user'));
    }

    //update
    public function update(Request $request, User $user){
        // $request->validate([
        //     'name' => 'required',
        //     'email' => 'required|email|unique:user,email' . $user->id,
        //     // 'phone' => 'required',
        //     'role' => 'required',
        // ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();
        //check if phone is not empty
        if ($request->phone) {
            $user->update(['phone' => $request->phone]);
        }
        //check if password is not empty
        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        return redirect()->route('users.index')->with('success','User Update successfully');
    }

    //destroy
    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('users.index')->with('success','User Delete successfully');
    }
}
