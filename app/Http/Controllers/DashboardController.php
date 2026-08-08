<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Template;
use App\Models\TemplateSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        $docsCount = Document::where('user_id', $userId)->count();
        $templatesCount = Template::where('user_id', $userId)->count();
        $submissionsCount = TemplateSubmission::whereHas('template', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $recentDocuments = Document::where('user_id', $userId)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $recentTemplates = Template::where('user_id', $userId)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('docsCount', 'templatesCount', 'submissionsCount', 'recentDocuments', 'recentTemplates'));
    }
}
