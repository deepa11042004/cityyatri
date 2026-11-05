<?php
/**
 * Club Yatri Admin - sub-navigation shared by the Passes & Subscriptions screens.
 */

declare(strict_types=1);

function passes_tabs(string $active): void
{
    $tabs = ['passes' => ['Holiday passes', 'passes/index.php', 'bi-ticket-perforated'], 'seasons' => ['Seasonal passes', 'passes/seasons.php', 'bi-sun'], 'terms' => ['Core terms', 'passes/terms.php', 'bi-file-text']];
    echo '<ul class="nav nav-tabs mb-3">';
    foreach ($tabs as $k => [$label, $href, $icon]) {
        echo '<li class="nav-item"><a class="nav-link' . ($k === $active ? ' active fw-bold' : '') . '" href="' . e(admin_url($href)) . '"><i class="bi ' . $icon . '"></i> ' . e($label) . '</a></li>';
    }
    echo '</ul>';
}
