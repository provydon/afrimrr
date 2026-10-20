<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Contracts\View\View;

class UnsubscribeController extends Controller
{
    public function __invoke(Subscriber $subscriber): View
    {
        // Idempotent: a second click just reaffirms the state.
        if ($subscriber->isSubscribed()) {
            $subscriber->forceFill(['unsubscribed_at' => now()])->save();
        }

        return view('newsletter.unsubscribed', [
            'email' => $subscriber->email,
        ]);
    }
}
