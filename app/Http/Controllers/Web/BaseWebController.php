<?php


namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class BaseWebController extends Controller
{
    protected function getRepresentativeId(): int
    {
        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('dispatcher')) {
                return $user->dispatcher->representative;
            } elseif ($user->hasRole('representative')) {
                return $user->id;
            }
        }

        return 0;
    }
}