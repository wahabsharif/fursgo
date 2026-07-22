<?php

namespace App\Http\Controllers;

use App\Support\AccountDataExport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AccountDataExportController extends Controller
{
    public function __invoke()
    {
        $owner = Auth::guard('groomer_spacer')->user() ?? Auth::guard('web')->user();

        abort_unless($owner instanceof Model, 401);

        return AccountDataExport::pdfResponse($owner);
    }
}
