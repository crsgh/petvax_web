<?php

/*
|--------------------------------------------------------------------------
| Vercel Front Controller
|--------------------------------------------------------------------------
|
| This file lives at the project root (NOT inside the reserved "api/"
| directory) so that Vercel does not strip the leading "/api" segment from
| the request path before it reaches Laravel. Keeping it here preserves the
| full URI, so Laravel's "api/*" routes resolve normally (e.g. a request to
| /api/clinic/all reaches the route named api/clinic/all as expected).
|
*/

require __DIR__ . '/public/index.php';
