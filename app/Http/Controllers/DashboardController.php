<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $cognitiveAssessment = Assessment::where('code', 'cognitive-battery-v1')
            ->orWhereHas('category', fn($q) => $q->where('code', 'cognitive'))
            ->first();

        $personalityAssessment = Assessment::where('code', 'big-five-v1')
            ->orWhereHas('category', fn($q) => $q->where('code', 'personality'))
            ->first();

        return view('dashboard.index', [
            'user' => $user,
            'cognitiveAssessment' => $cognitiveAssessment,
            'personalityAssessment' => $personalityAssessment,
        ]);
    }
}
