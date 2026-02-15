<?php

namespace App\Controllers;

use App\Core\Controller;

class NominateController extends Controller
{
    public function index()
    {
        // Require Authentication
        if (!function_exists('isAuthenticated') || !isAuthenticated()) {
            $this->redirect('auth');
        }

        // Fetch countries from database (African countries for Biggest Talent Africa)
        $countries = [];
        $activeSeason = null;
        try {
            $db = \App\Core\Database::getInstance();
            $conn = $db->getConnection();

            // Ensure countries table exists
            $tableCheck = $conn->query("SHOW TABLES LIKE 'countries'");
            if (!$tableCheck || $tableCheck->num_rows === 0) {
                $conn->query("CREATE TABLE IF NOT EXISTS countries (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(100) NOT NULL,
                    iso_code CHAR(2) NOT NULL,
                    iso3_code CHAR(3),
                    phone_code VARCHAR(10),
                    INDEX idx_name (name)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            }

            $result = $conn->query("SELECT id, name, iso_code FROM countries ORDER BY name ASC");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $countries[] = $row;
                }
            }

            // If empty, populate with African countries
            if (empty($countries)) {
                $africanCountries = [
                    ['Algeria', 'DZ'], ['Angola', 'AO'], ['Benin', 'BJ'], ['Botswana', 'BW'],
                    ['Burkina Faso', 'BF'], ['Burundi', 'BI'], ['Cameroon', 'CM'], ['Cape Verde', 'CV'],
                    ['Central African Republic', 'CF'], ['Chad', 'TD'], ['Comoros', 'KM'],
                    ['Congo', 'CG'], ['Democratic Republic of the Congo', 'CD'], ['Djibouti', 'DJ'],
                    ['Egypt', 'EG'], ['Equatorial Guinea', 'GQ'], ['Eritrea', 'ER'], ['Ethiopia', 'ET'],
                    ['Gabon', 'GA'], ['Gambia', 'GM'], ['Ghana', 'GH'], ['Guinea', 'GN'],
                    ['Guinea-Bissau', 'GW'], ['Ivory Coast', 'CI'], ['Kenya', 'KE'], ['Lesotho', 'LS'],
                    ['Liberia', 'LR'], ['Libya', 'LY'], ['Madagascar', 'MG'], ['Malawi', 'MW'],
                    ['Mali', 'ML'], ['Mauritania', 'MR'], ['Mauritius', 'MU'], ['Morocco', 'MA'],
                    ['Mozambique', 'MZ'], ['Namibia', 'NA'], ['Niger', 'NE'], ['Nigeria', 'NG'],
                    ['Rwanda', 'RW'], ['Sao Tome and Principe', 'ST'], ['Senegal', 'SN'],
                    ['Seychelles', 'SC'], ['Sierra Leone', 'SL'], ['Somalia', 'SO'],
                    ['South Africa', 'ZA'], ['South Sudan', 'SS'], ['Sudan', 'SD'],
                    ['Eswatini', 'SZ'], ['Tanzania', 'TZ'], ['Togo', 'TG'], ['Tunisia', 'TN'],
                    ['Uganda', 'UG'], ['Zambia', 'ZM'], ['Zimbabwe', 'ZW'],
                ];
                $stmt = $conn->prepare("INSERT INTO countries (name, iso_code) VALUES (?, ?)");
                if ($stmt) {
                    foreach ($africanCountries as $c) {
                        $stmt->bind_param("ss", $c[0], $c[1]);
                        $stmt->execute();
                    }
                    $stmt->close();
                }
                $result = $conn->query("SELECT id, name, iso_code FROM countries ORDER BY name ASC");
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $countries[] = $row;
                    }
                }

                // Ensure states_provinces exists with Zambia provinces (for province dropdown)
                $statesTable = $conn->query("SHOW TABLES LIKE 'states_provinces'");
                if (!$statesTable || $statesTable->num_rows === 0) {
                    $conn->query("CREATE TABLE IF NOT EXISTS states_provinces (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        country_id INT NOT NULL,
                        name VARCHAR(100) NOT NULL,
                        state_code VARCHAR(10),
                        INDEX idx_country_id (country_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                }
                $zm = $conn->query("SELECT id FROM countries WHERE iso_code = 'ZM' LIMIT 1");
                if ($zm && $zm->num_rows > 0) {
                    $zid = (int) $zm->fetch_assoc()['id'];
                    $spCount = $conn->query("SELECT COUNT(*) as c FROM states_provinces WHERE country_id = $zid");
                    if ($spCount && (int) $spCount->fetch_assoc()['c'] === 0) {
                        $provinces = ['Central', 'Copperbelt', 'Eastern', 'Luapula', 'Lusaka', 'Muchinga', 'Northern', 'North-Western', 'Southern', 'Western'];
                        $stmtSp = $conn->prepare("INSERT INTO states_provinces (country_id, name) VALUES (?, ?)");
                        if ($stmtSp) {
                            foreach ($provinces as $p) {
                                $stmtSp->bind_param("is", $zid, $p);
                                $stmtSp->execute();
                            }
                            $stmtSp->close();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $countries = [];
        }

        // Fetch active season (separate try so we always attempt it)
        try {
            $seasonModel = $this->model('Season');
            $activeSeason = $seasonModel->getActiveSeason();
        } catch (\Exception $e) {
            $activeSeason = null;
        }
        // Fallback if model returned null (helper ensures columns exist and has fallback to most recent season)
        if (!$activeSeason && function_exists('getActiveSeasonSimple')) {
            $activeSeason = getActiveSeasonSimple();
        }

        // Load legacy logic helpers if needed, or implement fresh.
        // For now, we reuse the existing data gathering logic or mock it if features aren't migrated.
        $data = [
            'categories' => [],
            'contests' => [],
            'currentContest' => null,
            'countries' => $countries,
            'activeSeason' => $activeSeason,
            'hide_nav' => true // Hide navigation on nominate page
        ];

        // Check if user already has a nomination (one per account)
        $userId = (int) ($_SESSION['uid'] ?? 0);
        $data['already_nominated'] = false;
        if ($userId > 0 && function_exists('getUserNominations')) {
            $existing = getUserNominations($userId, 1);
            $data['already_nominated'] = !empty($existing);
        }

        // Check if Nominations are Open - default to OPEN (show form) unless explicitly closed
        // Only show "Nominations Closed" when is_nominations_open is explicitly 0
        // Treat 1, null, or missing as OPEN so we never block users incorrectly
        $data['nominations_closed'] = false;
        $data['voting_open'] = false;
        if ($activeSeason && array_key_exists('is_nominations_open', $activeSeason)) {
            $val = $activeSeason['is_nominations_open'];
            $data['nominations_closed'] = ($val === 0 || $val === '0');
        }
        if ($activeSeason && array_key_exists('is_voting_open', $activeSeason)) {
            $vval = $activeSeason['is_voting_open'];
            $data['voting_open'] = ($vval == 1 || $vval === '1');
        }

        // ... (Logic to populate $data similar to nominate.php)
        // Populate User Profile Data from Session for Dashboard Layout
        $userProfile = [
            'username' => $_SESSION['username'] ?? 'User',
            'fname' => $_SESSION['fname'] ?? '',
            'lname' => $_SESSION['lname'] ?? '',
            'pernum' => $_SESSION['pernum'] ?? '',
            'uid' => $_SESSION['uid'] ?? 0,
            'pic' => $_SESSION['pic'] ?? null // Access pic if stored, else Layout handles fallback
        ];

        // Since we are mocking the Model layer migration for now, passing empty arrays or basic data.

        $this->view('layouts/header', $data);

        $viewData = array_merge($data, ['userProfile' => $userProfile, 'page_title' => 'Make a Nomination']);
        $this->view('nominate/index', $viewData);

        // Footer removed for Nominate page as per user request to remove logo
        // $this->view('layouts/footer');
    }

    private function redirect($url)
    {
        header('Location: ' . URLROOT . '/' . $url);
        exit;
    }
}
