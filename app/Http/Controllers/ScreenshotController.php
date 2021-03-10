<?php

namespace App\Http\Controllers;

use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScreenshotController extends Controller
{
	public function pdf(Request $request)
	{
		if(!$request->url) {
			abort(400);
		}

		$filename = $request->filename
					? str_replace('.pdf', '', $request->filename) . '.pdf'
					: (Str::slug($request->url) . '.pdf');
		$filename = Str::random(8) . '-' . $filename;

		$path = storage_path('app/public') . '/' . $filename;
		$file = Browsershot::url($request->url)
					->dismissDialogs()
					->showBackground()
					->format('Letter')
					->delay($request->delay * 1000 ?? 0)
					->savePdf($path);

		return $request->download
				? response()->download($path)
				: response()->json(['path' => config('app.url') . '/storage/' . $filename]);
	}
}
