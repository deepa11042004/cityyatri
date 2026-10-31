<?php
/**
 * Club Yatri - Video testimonials (shown under the written reviews on the homepage).
 *
 * Add one entry per member video. While this list is empty the homepage shows
 * "coming soon" placeholder cards instead.
 *
 * Each entry:
 *   'name'   => 'Arvind Kapadia',                        // required
 *   'detail' => 'Platinum Crest Member since 2022 • Mumbai',   // optional line under the name
 *   'quote'  => 'A short line from the video.',          // optional
 *   'youtube' => 'dQw4w9WgXcQ',                           // YouTube video ID (the 11 characters after v=)
 *   // ...or a video file you uploaded under assets/ (use ONE of youtube / mp4):
 *   'mp4'    => 'assets/videos/arvind.mp4',
 *   'poster' => 'assets/images/videos/arvind.jpg',        // optional thumbnail (YouTube videos use their own)
 *
 * Example:
 *   $videoTestimonials = [
 *       ['name' => 'Arvind Kapadia', 'detail' => 'Platinum Crest Member • Mumbai', 'youtube' => 'dQw4w9WgXcQ'],
 *   ];
 */

declare(strict_types=1);

$videoTestimonials = [];
