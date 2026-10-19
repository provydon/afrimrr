<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

/**
 * Renders the dynamic per-startup unfurl card (1200x630 PNG) used as the
 * og:image / twitter:image when a profile link is shared on X and elsewhere.
 *
 * Degrades safely: if GD or the bundled font is unavailable it redirects to
 * the static fallback card rather than erroring, so a crawler always gets an
 * image.
 */
class OgImageController extends Controller
{
    private const WIDTH = 1200;

    private const HEIGHT = 630;

    public function show(Startup $startup): Response|RedirectResponse
    {
        abort_unless($startup->is_public && ! $startup->is_anonymous, 404);

        $regular = resource_path('fonts/DejaVuSans.ttf');
        $bold = resource_path('fonts/DejaVuSans-Bold.ttf');

        if (! extension_loaded('gd') || ! function_exists('imagettftext') || ! is_file($bold)) {
            return redirect(config('app.url').'/og-image.png', 302);
        }

        $png = $this->render($startup, $regular, $bold);

        if ($png === null) {
            return redirect(config('app.url').'/og-image.png', 302);
        }

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function render(Startup $startup, string $regular, string $bold): ?string
    {
        $img = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        if ($img === false) {
            return null;
        }

        $bg = $this->color($img, 15, 23, 42);       // slate-900
        $green = $this->color($img, 34, 197, 94);    // green-500
        $greenDim = $this->color($img, 22, 163, 74); // green-600
        $white = $this->color($img, 248, 250, 252);
        $muted = $this->color($img, 148, 163, 184);  // slate-400

        imagefill($img, 0, 0, $bg);

        // Left accent spine.
        imagefilledrectangle($img, 0, 0, 14, self::HEIGHT, $greenDim);

        $pad = 90;

        // Brand + verified/listed pill (top row).
        $this->text($img, 34, $pad, 110, $green, $bold, 'AfricanMRR');

        $pillLabel = $startup->is_verified ? 'VERIFIED MRR' : 'LISTED MRR';
        $pillColor = $startup->is_verified ? $greenDim : $this->color($img, 71, 85, 105);
        [$pw] = $this->measure(18, $bold, $pillLabel);
        $pillW = $pw + 56;
        $pillX = self::WIDTH - $pad - $pillW;
        $this->roundedRect($img, $pillX, 72, $pillX + $pillW, 118, 23, $pillColor);
        $this->text($img, 18, $pillX + 28, 102, $white, $bold, $pillLabel);

        // Startup name (wrapped, up to 2 lines).
        $maxWidth = self::WIDTH - ($pad * 2);
        $lines = $this->wrap($startup->name, 58, $bold, $maxWidth, 2);
        $y = 220;
        foreach ($lines as $line) {
            $this->text($img, 58, $pad, $y, $white, $bold, $line);
            $y += 74;
        }

        // Big MRR figure.
        $mrr = Money::usd($startup->mrr_usd);
        $mrrY = 475;
        $this->text($img, 116, $pad, $mrrY, $green, $bold, $mrr);
        [$mw] = $this->measure(116, $bold, $mrr);
        $this->text($img, 38, $pad + $mw + 24, $mrrY, $muted, $regular, '/mo');

        // Footer line: country · active subscriptions · domain.
        $subs = number_format($startup->active_subscriptions);
        $footer = sprintf('%s   ·   %s active subs   ·   africanmrr.com', $startup->countryLabel(), $subs);
        $this->text($img, 28, $pad, 575, $muted, $regular, $footer);

        ob_start();
        imagepng($img);
        $png = (string) ob_get_clean();
        imagedestroy($img);

        return $png;
    }

    /**
     * Draw left-aligned text; $y is the text baseline-ish top (we add the size
     * so callers can think in top coordinates).
     */
    private function text(\GdImage $img, int $size, int $x, int $y, int $color, string $font, string $text): void
    {
        imagettftext($img, $size, 0, $x, $y, $color, $font, $text);
    }

    /**
     * Allocate a colour, falling back to black if the image rejects it.
     *
     * @param  int<0, 255>  $r
     * @param  int<0, 255>  $g
     * @param  int<0, 255>  $b
     */
    private function color(\GdImage $img, int $r, int $g, int $b): int
    {
        $c = imagecolorallocate($img, $r, $g, $b);

        return $c === false ? 0 : $c;
    }

    /**
     * @return array{0: int, 1: int} width, height of the rendered string
     */
    private function measure(int $size, string $font, string $text): array
    {
        $box = imagettfbbox($size, 0, $font, $text);

        if ($box === false) {
            return [0, 0];
        }

        $width = (int) abs($box[2] - $box[0]);
        $height = (int) abs($box[7] - $box[1]);

        return [$width, $height];
    }

    /**
     * Greedy word-wrap to a pixel width, capped at $maxLines (last line is
     * ellipsised if content overflows).
     *
     * @return list<string>
     */
    private function wrap(string $text, int $size, string $font, int $maxWidth, int $maxLines): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;
            [$w] = $this->measure($size, $font, $candidate);

            if ($w <= $maxWidth || $current === '') {
                $current = $candidate;

                continue;
            }

            $lines[] = $current;
            $current = $word;

            if (count($lines) === $maxLines) {
                break;
            }
        }

        if ($current !== '' && count($lines) < $maxLines) {
            $lines[] = $current;
        }

        // Ellipsis if we ran out of room with words remaining.
        if (count($lines) === $maxLines) {
            $last = $lines[$maxLines - 1];
            while ($this->measure($size, $font, $last.'…')[0] > $maxWidth && str_contains($last, ' ')) {
                $last = substr($last, 0, (int) strrpos($last, ' '));
            }
            $consumed = implode(' ', $lines);
            if (str_word_count($consumed) < count($words)) {
                $lines[$maxLines - 1] = rtrim($last).'…';
            }
        }

        return array_values($lines === [] ? [''] : $lines);
    }

    private function roundedRect(\GdImage $img, int $x1, int $y1, int $x2, int $y2, int $r, int $color): void
    {
        imagefilledrectangle($img, $x1 + $r, $y1, $x2 - $r, $y2, $color);
        imagefilledrectangle($img, $x1, $y1 + $r, $x2, $y2 - $r, $color);
        imagefilledarc($img, $x1 + $r, $y1 + $r, $r * 2, $r * 2, 180, 270, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x2 - $r, $y1 + $r, $r * 2, $r * 2, 270, 360, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x1 + $r, $y2 - $r, $r * 2, $r * 2, 90, 180, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x2 - $r, $y2 - $r, $r * 2, $r * 2, 0, 90, $color, IMG_ARC_PIE);
    }
}
