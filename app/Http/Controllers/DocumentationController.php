<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentationController extends Controller
{
    /**
     * Display the documentation page.
     */
    public function index()
    {
        $docPath = Setting::getValue('api_documentation_pdf');
        $docUrl = $docPath ? Storage::url($docPath) : null;

        return view('documentation.index', compact('docUrl'));
    }
}
