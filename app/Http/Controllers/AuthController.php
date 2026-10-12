<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return inertia('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/tasks');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegistrationForm()
    {
        return inertia('Auth/Register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            auth()->login($user);

            return redirect('/tasks');

        } catch (QueryException $e) {
            logger()->error('Database error creating user: '.$validatedData['email']);

            if ($e->getCode() === '23000') {
                return back()->withErrors([
                    'email' => 'The provided email is already in use.',
                ]);
            }

            return back()->withErrors([
                'general' => 'An unexpected database error occurred. Please try again later.',
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, 'creating');
        }
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function handleException(\Exception $e, string $action): \Illuminate\Http\RedirectResponse
    {
        logger()->error("Error {$action} user: ", [
            'code' => $e->getCode(),
            'exception_class' => get_class($e),
        ]);

        return back()->withErrors([
            'general' => "There was an error {$action} the user. Please try again.",
        ]);
    }
}
