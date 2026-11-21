-- Remove star-rating wording ("3-star") from a handful of imported hotel descriptions. Safe to re-run.
SET NAMES utf8mb4;
UPDATE `hotels` SET `description` = REPLACE(`description`, 'This 3-star hotel offers', 'This hotel offers') WHERE `description` LIKE '%This 3-star hotel offers%';
UPDATE `hotels` SET `description` = REPLACE(`description`, 'A TCB accredited 3-star hotel.', 'A TCB accredited hotel.') WHERE `description` LIKE '%A TCB accredited 3-star hotel.%';
UPDATE `hotels` SET `description` = REPLACE(`description`, 'has 3-star accommodation with', 'has accommodation with') WHERE `description` LIKE '%has 3-star accommodation with%';
UPDATE `hotels` SET `description` = REPLACE(`description`, 'family run hotel in the 3 star categories focused', 'family run hotel focused') WHERE `description` LIKE '%family run hotel in the 3 star categories focused%';
UPDATE `hotels` SET `description` = REPLACE(`description`, 'a 3 star hotel at Nagpur', 'a hotel at Nagpur') WHERE `description` LIKE '%a 3 star hotel at Nagpur%';
UPDATE `hotels` SET `description` = REPLACE(`description`, 'the best 3 star hotel in Shimla', 'the best hotel in Shimla') WHERE `description` LIKE '%the best 3 star hotel in Shimla%';
