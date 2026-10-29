<?php
/**
 * Club Yatri - SEO & OpenGraph Helper
 */

declare(strict_types=1);

global $defaultSeo;

$currentTitle = isset($pageTitle) && $pageTitle !== '' 
    ? e($pageTitle) . ' | ' . SITE_NAME 
    : e($defaultSeo['title']);

$currentDescription = isset($pageDescription) && $pageDescription !== '' 
    ? e($pageDescription) 
    : e($defaultSeo['description']);

$currentKeywords = isset($pageKeywords) && $pageKeywords !== '' 
    ? e($pageKeywords) 
    : e($defaultSeo['keywords']);

$currentOgImage = isset($pageOgImage) && $pageOgImage !== '' 
    ? e($pageOgImage) 
    : e($defaultSeo['og_image']);

$canonicalUrl = defined('BASE_URL') ? BASE_URL . '/' . basename($_SERVER['PHP_SELF'] ?? '') : '';
?>
  <title><?php echo $currentTitle; ?></title>
  <meta name="description" content="<?php echo $currentDescription; ?>">
  <meta name="keywords" content="<?php echo $currentKeywords; ?>">
  <link rel="canonical" href="<?php echo e($canonicalUrl); ?>">

  <!-- OpenGraph Metadata -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?php echo e(SITE_NAME); ?>">
  <meta property="og:title" content="<?php echo $currentTitle; ?>">
  <meta property="og:description" content="<?php echo $currentDescription; ?>">
  <meta property="og:image" content="<?php echo $currentOgImage; ?>">
  <meta property="og:url" content="<?php echo e($canonicalUrl); ?>">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo $currentTitle; ?>">
  <meta name="twitter:description" content="<?php echo $currentDescription; ?>">
  <meta name="twitter:image" content="<?php echo $currentOgImage; ?>">
