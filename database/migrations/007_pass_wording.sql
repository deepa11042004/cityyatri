-- Remove "5-star" wording from the 25-year pass description. Safe to re-run.
SET NAMES utf8mb4;
UPDATE `passes` SET `short_description` = REPLACE(`short_description`, 'multi-generational 5-star vacation security', 'multi-generational luxury vacation security') WHERE `short_description` LIKE '%multi-generational 5-star vacation security%';
