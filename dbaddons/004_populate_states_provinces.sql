-- Migration Script 004: Populate States/Provinces
-- Purpose: Insert states/provinces for major African countries
-- Run this script after 003_populate_countries.sql

-- Zambia Provinces
INSERT INTO states_provinces (country_id, name, state_code) 
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

-- South Africa Provinces
INSERT INTO states_provinces (country_id, name, state_code)
SELECT id, 'Eastern Cape', 'EC' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Free State', 'FS' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Gauteng', 'GT' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'KwaZulu-Natal', 'NL' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Limpopo', 'LP' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Mpumalanga', 'MP' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Northern Cape', 'NC' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'North West', 'NW' FROM countries WHERE iso_code = 'ZA'
UNION ALL SELECT id, 'Western Cape', 'WC' FROM countries WHERE iso_code = 'ZA';

-- Nigeria States
INSERT INTO states_provinces (country_id, name, state_code)
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

-- Kenya Counties (Major ones)
INSERT INTO states_provinces (country_id, name, state_code)
SELECT id, 'Nairobi', '047' FROM countries WHERE iso_code = 'KE'
UNION ALL SELECT id, 'Mombasa', '001' FROM countries WHERE iso_code = 'KE'
UNION ALL SELECT id, 'Kisumu', '042' FROM countries WHERE iso_code = 'KE'
UNION ALL SELECT id, 'Nakuru', '032' FROM countries WHERE iso_code = 'KE'
UNION ALL SELECT id, 'Kiambu', '022' FROM countries WHERE iso_code = 'KE';
