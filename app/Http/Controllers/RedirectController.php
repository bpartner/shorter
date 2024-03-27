<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use AshAllenDesign\ShortURL\Classes\Resolver;
use AshAllenDesign\ShortURL\Models\ShortURL;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke(Request $request, Resolver $resolver, string $shortURLKey): RedirectResponse
    {
        $shortURL = ShortURL::where('url_key', $shortURLKey)->firstOrFail();

        $resolver->handleVisit(request(), $shortURL);

        if ($shortURL->forward_query_params) {
            return redirect($this->forwardQueryParams($request, $shortURL), $shortURL->redirect_status_code);
        }

        return redirect($shortURL->destination_url, $shortURL->redirect_status_code);
    }

    private function forwardQueryParams(Request $request, ShortURL $shortURL): string
    {
        $queryString = parse_url($shortURL->destination_url, PHP_URL_QUERY);

        if (empty($request->query())) {
            return $shortURL->destination_url;
        }

        $separator = $queryString ? '&' : '?';

        return $shortURL->destination_url.$separator.http_build_query($request->query());
    }
}
