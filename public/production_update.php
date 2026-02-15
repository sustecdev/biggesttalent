<?php
// public/production_update.php
// Script to fix database schema on production AND filter to African countries

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();

echo "<h1>Production Database Update (African Countries Only)</h1>";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "<p style='color:green'>Database connection successful.</p>";
} catch (Exception $e) {
    die("<p style='color:red'>Database connection failed: " . $e->getMessage() . "</p>");
}

// 1. Create Countries Table
$sqlCreateCountries = "
CREATE TABLE IF NOT EXISTS countries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    iso_code CHAR(2) NOT NULL UNIQUE COMMENT 'ISO 3166-1 alpha-2 code',
    iso3_code CHAR(3) COMMENT 'ISO 3166-1 alpha-3 code',
    phone_code VARCHAR(10) COMMENT 'International dialing code',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_iso_code (iso_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sqlCreateCountries)) {
    echo "<p>Checked 'countries' table.</p>";
} else {
    echo "<p style='color:red'>Error creating 'countries' table: " . $conn->error . "</p>";
}

// 1.1 CLEANUP: Remove non-African countries
// List of African ISO codes to KEEP
$africanIsos = [
    'DZ',
    'AO',
    'BJ',
    'BW',
    'BF',
    'BI',
    'CM',
    'CV',
    'CF',
    'TD',
    'KM',
    'CG',
    'CD',
    'DJ',
    'EG',
    'GQ',
    'ER',
    'ET',
    'GA',
    'GM',
    'GH',
    'GN',
    'GW',
    'CI',
    'KE',
    'LS',
    'LR',
    'LY',
    'MG',
    'MW',
    'ML',
    'MR',
    'MU',
    'MA',
    'MZ',
    'NA',
    'NE',
    'NG',
    'RW',
    'ST',
    'SN',
    'SC',
    'SL',
    'SO',
    'ZA',
    'SS',
    'SD',
    'SZ',
    'TZ',
    'TG',
    'TN',
    'UG',
    'ZM',
    'ZW'
];
$isoList = "'" . implode("','", $africanIsos) . "'";

echo "<p>Cleaning up non-African countries...</p>";
$sqlCleanup = "DELETE FROM countries WHERE iso_code NOT IN ($isoList)";
if ($conn->query($sqlCleanup)) {
    echo "<p>Removed non-African countries (if any).</p>";
} else {
    echo "<p style='color:orange'>Warning during cleanup: " . $conn->error . "</p>";
}

// 2. Populate Countries Table (only absent ones)
echo "<p>Populating 'countries' table with African nations...</p>";
$sqlPopulateCountries = "INSERT IGNORE INTO countries (name, iso_code, iso3_code, phone_code) VALUES
('Algeria', 'DZ', 'DZA', '+213'),
('Angola', 'AO', 'AGO', '+244'),
('Benin', 'BJ', 'BEN', '+229'),
('Botswana', 'BW', 'BWA', '+267'),
('Burkina Faso', 'BF', 'BFA', '+226'),
('Burundi', 'BI', 'BDI', '+257'),
('Cameroon', 'CM', 'CMR', '+237'),
('Cape Verde', 'CV', 'CPV', '+238'),
('Central African Republic', 'CF', 'CAF', '+236'),
('Chad', 'TD', 'TCD', '+235'),
('Comoros', 'KM', 'COM', '+269'),
('Congo', 'CG', 'COG', '+242'),
('Democratic Republic of the Congo', 'CD', 'COD', '+243'),
('Djibouti', 'DJ', 'DJI', '+253'),
('Egypt', 'EG', 'EGY', '+20'),
('Equatorial Guinea', 'GQ', 'GNQ', '+240'),
('Eritrea', 'ER', 'ERI', '+291'),
('Ethiopia', 'ET', 'ETH', '+251'),
('Gabon', 'GA', 'GAB', '+241'),
('Gambia', 'GM', 'GMB', '+220'),
('Ghana', 'GH', 'GHA', '+233'),
('Guinea', 'GN', 'GIN', '+224'),
('Guinea-Bissau', 'GW', 'GNB', '+245'),
('Ivory Coast', 'CI', 'CIV', '+225'),
('Kenya', 'KE', 'KEN', '+254'),
('Lesotho', 'LS', 'LSO', '+266'),
('Liberia', 'LR', 'LBR', '+231'),
('Libya', 'LY', 'LBY', '+218'),
('Madagascar', 'MG', 'MDG', '+261'),
('Malawi', 'MW', 'MWI', '+265'),
('Mali', 'ML', 'MLI', '+223'),
('Mauritania', 'MR', 'MRT', '+222'),
('Mauritius', 'MU', 'MUS', '+230'),
('Morocco', 'MA', 'MAR', '+212'),
('Mozambique', 'MZ', 'MOZ', '+258'),
('Namibia', 'NA', 'NAM', '+264'),
('Niger', 'NE', 'NER', '+227'),
('Nigeria', 'NG', 'NGA', '+234'),
('Rwanda', 'RW', 'RWA', '+250'),
('Sao Tome and Principe', 'ST', 'STP', '+239'),
('Senegal', 'SN', 'SEN', '+221'),
('Seychelles', 'SC', 'SYC', '+248'),
('Sierra Leone', 'SL', 'SLE', '+232'),
('Somalia', 'SO', 'SOM', '+252'),
('South Africa', 'ZA', 'ZAF', '+27'),
('South Sudan', 'SS', 'SSD', '+211'),
('Sudan', 'SD', 'SDN', '+249'),
('Swaziland', 'SZ', 'SWZ', '+268'),
('Tanzania', 'TZ', 'TZA', '+255'),
('Togo', 'TG', 'TGO', '+228'),
('Tunisia', 'TN', 'TUN', '+216'),
('Uganda', 'UG', 'UGA', '+256'),
('Zambia', 'ZM', 'ZMB', '+260'),
('Zimbabwe', 'ZW', 'ZWE', '+263');";

if ($conn->query($sqlPopulateCountries)) {
    echo "<p>Populated 'countries' table.</p>";
} else {
    echo "<p style='color:red'>Error populating 'countries' table: " . $conn->error . "</p>";
}


// 3. Create States/Provinces Table
$sqlCreateStates = "
CREATE TABLE IF NOT EXISTS states_provinces (
    id INT PRIMARY KEY AUTO_INCREMENT,
    country_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    state_code VARCHAR(10) COMMENT 'State/Province code',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE,
    INDEX idx_country_id (country_id),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sqlCreateStates)) {
    echo "<p>Checked 'states_provinces' table.</p>";
} else {
    echo "<p style='color:red'>Error creating 'states_provinces' table: " . $conn->error . "</p>";
}

// 3.1 CLEANUP: Delete states for countries that don't satisfy the African filter?
// Since we have ON DELETE CASCADE on foreign key, deleting from countries above ALREADY deleted their states!
// So just need to repopulate missing ones.

// 4. Populate States/Provinces Table (only if empty/missing)
// We use INSERT IGNORE logic or check counts? 
// Simplest is to just try insert. But we need IDs.
// The queries use SELECT id FROM countries... so they map correctly even if IDs change.

echo "<p>Populating 'states_provinces' table...</p>";
$sqlPopulateStates = "
INSERT IGNORE INTO states_provinces (country_id, name, state_code) 
SELECT id, 'Central', 'CE' FROM countries WHERE iso_code = 'ZM'
UNION ALL SELECT id, 'Copperbelt', 'CB' FROM countries WHERE iso_code = 'ZM'
UNION ALL SELECT id, 'Eastern', 'EA' FROM countries WHERE iso_code = 'ZM'
UNION ALL SELECT id, 'Luapula', 'LP' FROM countries WHERE iso_code = 'ZM'
UNION ALL SELECT id, 'Lusaka', 'LK' FROM countries WHERE iso_code = 'ZM'
UNION ALL SELECT id, 'Muchinga', 'MU' FROM countries WHERE iso_code = 'ZM'
UNION ALL SELECT id, 'Northern', 'NO' FROM countries WHERE iso_code = 'ZM'
UNION ALL SELECT id, 'North-Western', 'NW' FROM countries WHERE iso_code = 'ZM'
UNION ALL SELECT id, 'Southern', 'SO' FROM countries WHERE iso_code = 'ZM'
UNION ALL SELECT id, 'Western', 'WE' FROM countries WHERE iso_code = 'ZM';

INSERT IGNORE INTO states_provinces (country_id, name, state_code)
SELECT id, 'Eastern Cape', 'EC' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Free State', 'FS' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Gauteng', 'GT' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'KwaZulu-Natal', 'NL' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Limpopo', 'LP' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Mpumalanga', 'MP' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Northern Cape', 'NC' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'North West', 'NW' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Western Cape', 'WC' FROM countries WHERE iso_code = 'ZA';

INSERT IGNORE INTO states_provinces (country_id, name, state_code)
SELECT id, 'Abia', 'AB' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Adamawa', 'AD' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Akwa Ibom', 'AK' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Anambra', 'AN' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Bauchi', 'BA' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Bayelsa', 'BY' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Benue', 'BE' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Borno', 'BO' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Cross River', 'CR' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Delta', 'DE' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Ebonyi', 'EB' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Edo', 'ED' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Ekiti', 'EK' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Enugu', 'EN' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'FCT', 'FC' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Gombe', 'GO' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Imo', 'IM' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Jigawa', 'JI' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Kaduna', 'KD' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Kano', 'KN' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Katsina', 'KT' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Kebbi', 'KE' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Kogi', 'KO' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Kwara', 'KW' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Lagos', 'LA' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Nasarawa', 'NA' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Niger', 'NI' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Ogun', 'OG' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Ondo', 'ON' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Osun', 'OS' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Oyo', 'OY' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Plateau', 'PL' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Rivers', 'RI' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Sokoto', 'SO' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Taraba', 'TA' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Yobe', 'YO' FROM countries WHERE iso_code = 'NG'
UNION ALL SELECT id, 'Zamfara', 'ZA' FROM countries WHERE iso_code = 'NG';

INSERT IGNORE INTO states_provinces (country_id, name, state_code)
SELECT id, 'Nairobi', '047' FROM countries WHERE iso_code = 'KE'
UNION ALL SELECT id, 'Mombasa', '001' FROM countries WHERE iso_code = 'KE'
UNION ALL SELECT id, 'Kisumu', '042' FROM countries WHERE iso_code = 'KE'
UNION ALL SELECT id, 'Nakuru', '032' FROM countries WHERE iso_code = 'KE'
UNION ALL SELECT id, 'Kiambu', '022' FROM countries WHERE iso_code = 'KE';
";

if ($conn->multi_query($sqlPopulateStates)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "<p>Populated 'states_provinces' table.</p>";
} else {
    echo "<p style='color:red'>Error populating 'states_provinces' table: " . $conn->error . "</p>";
}


// 5. Alter bt_nominations table to add province column
$checkColumn = $conn->query("SHOW COLUMNS FROM bt_nominations LIKE 'province'");
if ($checkColumn->num_rows == 0) {
    echo "<p>Adding 'province' column to 'bt_nominations' table...</p>";
    $sqlAlter = "ALTER TABLE bt_nominations ADD COLUMN province VARCHAR(255) AFTER country";
    if ($conn->query($sqlAlter)) {
        echo "<p>Successfully added 'province' column.</p>";
    } else {
        echo "<p style='color:red'>Error adding column: " . $conn->error . "</p>";
    }
} else {
    echo "<p>'province' column already exists in 'bt_nominations'.</p>";
}

echo "<h2>Update Complete</h2>";
print "<a href='index.php'>Go Back</a>";
