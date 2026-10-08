<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
class AuthController extends Controller {
    public function showLogin(){return view('auth.login');}
    public function showRegister(){return view('auth.register');}
    public function register(Request $request){
        $data=$request->validate(['name'=>['required','string','max:100'],'email'=>['required','email','max:255','unique:users,email'],'password'=>['required','confirmed',Password::min(8)]]);
        $user=User::create($data); Auth::login($user); $request->session()->regenerate();
        return redirect()->route('dashboard')->with('success','Account created successfully.');
    }
    public function login(Request $request){
        $credentials=$request->validate(['email'=>['required','email'],'password'=>['required','string']]);
        if(!Auth::attempt($credentials,$request->boolean('remember'))) return back()->withErrors(['email'=>'The email or password is incorrect.'])->onlyInput('email');
        $request->session()->regenerate(); return redirect()->intended(route('dashboard'));
    }
    public function logout(Request $request){
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('home')->with('success','You have been logged out.');
    }
}
