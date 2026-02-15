<?php
/**
 * Populate states/provinces for ALL African countries.
 * Run: php dbaddons/populate_all_states.php
 * Or visit: http://localhost/btanew/dbaddons/populate_all_states.php
 */
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();
\App\Core\Config::load();

$db = \App\Core\Database::getInstance();
$conn = $db->getConnection();

header('Content-Type: text/plain; charset=utf-8');
echo "Populating states/provinces for all African countries...\n\n";

// Ensure countries table exists and has African countries
$countriesCheck = $conn->query("SHOW TABLES LIKE 'countries'");
$countriesCount = 0;
if ($countriesCheck && $countriesCheck->num_rows > 0) {
    $r = $conn->query("SELECT COUNT(*) as c FROM countries");
    $countriesCount = $r ? (int)$r->fetch_assoc()['c'] : 0;
}
if (!$countriesCheck || $countriesCheck->num_rows === 0 || $countriesCount === 0) {
    $conn->query("CREATE TABLE IF NOT EXISTS countries (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        iso_code CHAR(2) NOT NULL,
        iso3_code CHAR(3),
        phone_code VARCHAR(10),
        INDEX idx_name (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $africanCountries = [
        ['Algeria','DZ'],['Angola','AO'],['Benin','BJ'],['Botswana','BW'],['Burkina Faso','BF'],
        ['Burundi','BI'],['Cameroon','CM'],['Cape Verde','CV'],['Central African Republic','CF'],
        ['Chad','TD'],['Comoros','KM'],['Congo','CG'],['Democratic Republic of the Congo','CD'],
        ['Djibouti','DJ'],['Egypt','EG'],['Equatorial Guinea','GQ'],['Eritrea','ER'],['Ethiopia','ET'],
        ['Gabon','GA'],['Gambia','GM'],['Ghana','GH'],['Guinea','GN'],['Guinea-Bissau','GW'],
        ['Ivory Coast','CI'],['Kenya','KE'],['Lesotho','LS'],['Liberia','LR'],['Libya','LY'],
        ['Madagascar','MG'],['Malawi','MW'],['Mali','ML'],['Mauritania','MR'],['Mauritius','MU'],
        ['Morocco','MA'],['Mozambique','MZ'],['Namibia','NA'],['Niger','NE'],['Nigeria','NG'],
        ['Rwanda','RW'],['Sao Tome and Principe','ST'],['Senegal','SN'],['Seychelles','SC'],
        ['Sierra Leone','SL'],['Somalia','SO'],['South Africa','ZA'],['South Sudan','SS'],['Sudan','SD'],
        ['Eswatini','SZ'],['Tanzania','TZ'],['Togo','TG'],['Tunisia','TN'],['Uganda','UG'],
        ['Zambia','ZM'],['Zimbabwe','ZW'],
    ];
    $ins = $conn->prepare("INSERT INTO countries (name, iso_code) VALUES (?, ?)");
    foreach ($africanCountries as $c) {
        $ins->bind_param("ss", $c[0], $c[1]);
        $ins->execute();
    }
    $ins->close();
    echo "Created/refreshed countries table with African nations.\n";
}

// Ensure states_provinces table exists with unique constraint to avoid duplicates
$t = $conn->query("SHOW TABLES LIKE 'states_provinces'");
if (!$t || $t->num_rows === 0) {
    $conn->query("CREATE TABLE IF NOT EXISTS states_provinces (
        id INT PRIMARY KEY AUTO_INCREMENT,
        country_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        state_code VARCHAR(10),
        INDEX idx_country_id (country_id),
        UNIQUE KEY uk_country_name (country_id, name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Created states_provinces table.\n";
} else {
    // Add unique constraint if missing (for INSERT IGNORE) - skip if duplicate data exists
    $idx = $conn->query("SHOW INDEX FROM states_provinces WHERE Key_name = 'uk_country_name'");
    if (!$idx || $idx->num_rows === 0) {
        $dup = $conn->query("SELECT country_id, name, COUNT(*) c FROM states_provinces GROUP BY country_id, name HAVING c > 1");
        if ($dup && $dup->num_rows === 0) {
            $conn->query("ALTER TABLE states_provinces ADD UNIQUE KEY uk_country_name (country_id, name)");
        }
    }
}

// ISO code => array of [name, code] or just [name]
$allStates = [
    'DZ' => [['Adrar','AR'],['Aïn Defla','AD'],['Aïn Témouchent','AT'],['Algiers','AL'],['Annaba','AN'],['Batna','BT'],['Béchar','BC'],['Béjaïa','BJ'],['Biskra','BS'],['Blida','BL'],['Bordj Bou Arréridj','BB'],['Boumerdès','BM'],['Chlef','CH'],['Constantine','CO'],['Djelfa','DJ'],['El Bayadh','EB'],['El Oued','EO'],['El Tarf','ET'],['Ghardaïa','GR'],['Guelma','GL'],['Illizi','IL'],['Jijel','JJ'],['Khenchela','KH'],['Laghouat','LG'],['Mascara','MC'],['Médéa','MD'],['Mila','ML'],['Mostaganem','MG'],['Msila','MS'],['Naama','NA'],['Oran','OR'],['Ouargla','OG'],['Oum El Bouaghi','OB'],['Relizane','RE'],['Saïda','SD'],['Sétif','SF'],['Sidi Bel Abbès','SB'],['Skikda','SK'],['Souk Ahras','SA'],['Tamanrasset','TM'],['Tébessa','TB'],['Tiaret','TR'],['Tindouf','TN'],['Tipaza','TP'],['Tissemsilt','TS'],['Tizi Ouzou','TO'],['Tlemcen','TL']],
    'AO' => [['Bengo','BG'],['Benguela','BN'],['Bié','BI'],['Cabinda','CB'],['Cuando Cubango','CC'],['Cuanza Norte','CN'],['Cuanza Sul','CS'],['Cunene','CU'],['Huambo','HU'],['Huíla','HI'],['Luanda','LU'],['Lunda Norte','LN'],['Lunda Sul','LS'],['Malanje','ML'],['Moxico','MX'],['Namibe','NA'],['Uíge','UI'],['Zaire','ZA']],
    'BJ' => [['Alibori','AL'],['Atakora','AK'],['Atlantique','AQ'],['Borgou','BO'],['Collines','CO'],['Couffo','CF'],['Donga','DO'],['Littoral','LI'],['Mono','MO'],['Ouémé','OU'],['Plateau','PL'],['Zou','ZO']],
    'BW' => [['Central','CE'],['Ghanzi','GH'],['Kgalagadi','KG'],['Kgatleng','KL'],['Kweneng','KW'],['North-East','NE'],['North-West','NW'],['South-East','SE'],['Southern','SO']],
    'BF' => [['Boucle du Mouhoun','BM'],['Cascades','CA'],['Centre','CE'],['Centre-Est','CE'],['Centre-Nord','CN'],['Centre-Ouest','CO'],['Centre-Sud','CS'],['Est','ES'],['Hauts-Bassins','HB'],['Nord','NO'],['Plateau-Central','PC'],['Sahel','SA'],['Sud-Ouest','SO']],
    'BI' => [['Bubanza','BB'],['Bujumbura Mairie','BM'],['Bujumbura Rural','BR'],['Bururi','BU'],['Cankuzo','CA'],['Cibitoke','CI'],['Gitega','GI'],['Karusi','KA'],['Kayanza','KY'],['Kirundo','KI'],['Makamba','MA'],['Muramvya','MU'],['Muyinga','MY'],['Ngozi','NG'],['Rumonge','RU'],['Rutana','RT'],['Ruyigi','RY']],
    'CM' => [['Adamawa','AD'],['Centre','CE'],['East','ES'],['Far North','FN'],['Littoral','LI'],['North','NO'],['North-West','NW'],['South','SO'],['South-West','SW'],['West','WE']],
    'CV' => [['Boa Vista','BV'],['Brava','BR'],['Fogo','FO'],['Maio','MA'],['Sal','SL'],['Santa Luzia','SL'],['Santo Antão','SA'],['São Nicolau','SN'],['São Vicente','SV'],['Santiago','ST']],
    'CF' => [['Bamingui-Bangoran','BB'],['Bangui','BG'],['Basse-Kotto','BK'],['Haute-Kotto','HK'],['Haut-Mbomou','HM'],['Kémo','KE'],['Lobaye','LO'],['Mambéré-Kadéï','MK'],['Mbomou','MB'],['Nana-Grébizi','NG'],['Nana-Mambéré','NM'],['Ombella-M\'Poko','OM'],['Ouaka','OU'],['Ouham','OH'],['Ouham-Pendé','OP'],['Sangha-Mbaéré','SM'],['Vakaga','VA']],
    'TD' => [['Bahr el Gazel','BG'],['Batha','BA'],['Borkou','BO'],['Chari-Baguirmi','CB'],['Ennedi-Est','EE'],['Ennedi-Ouest','EO'],['Guéra','GU'],['Hadjer-Lamis','HL'],['Kanem','KA'],['Lac','LC'],['Logone Occidental','LO'],['Logone Oriental','LR'],['Mandoul','MA'],['Mayo-Kebbi Est','MK'],['Mayo-Kebbi Ouest','MO'],['Moyen-Chari','MC'],['Ouaddaï','OU'],['Salamat','SA'],['Sila','SI'],['Tandjilé','TA'],['Tibesti','TI'],['Ville de Ndjamena','ND'],['Wadi Fira','WF']],
    'KM' => [['Anjouan','AN'],['Grande Comore','GC'],['Mohéli','MO']],
    'CG' => [['Bouenza','BO'],['Brazzaville','BR'],['Cuvette','CU'],['Cuvette-Ouest','CO'],['Kouilou','KO'],['Lékoumou','LE'],['Likouala','LI'],['Niari','NI'],['Plateaux','PL'],['Pointe-Noire','PN'],['Pool','PO'],['Sangha','SA']],
    'DJ' => [['Ali Sabieh','AS'],['Arta','AR'],['Dikhil','DI'],['Djibouti','DJ'],['Obock','OB'],['Tadjourah','TA']],
    'CD' => [['Bas-Uélé','BU'],['Équateur','EQ'],['Haut-Katanga','HK'],['Haut-Lomami','HL'],['Haut-Uélé','HU'],['Ituri','IT'],['Kasaï','KS'],['Kasaï Central','KC'],['Kasaï Oriental','KO'],['Kinshasa','KN'],['Kongo Central','KK'],['Kwango','KW'],['Kwilu','KZ'],['Lomami','LO'],['Lualaba','LU'],['Mai-Ndombe','MN'],['Maniema','MA'],['Mongala','MO'],['Nord-Kivu','NK'],['Nord-Ubangi','NU'],['Sankuru','SA'],['Sud-Kivu','SK'],['Sud-Ubangi','SU'],['Tanganyika','TA'],['Tshopo','TS'],['Tshuapa','TU']],
    'CI' => [['Abidjan','AB'],['Agnéby-Tiassa','AT'],['Bafing','BF'],['Bagoué','BG'],['Bélier','BL'],['Béré','BR'],['Bounkani','BK'],['Cavally','CV'],['Folon','FL'],['Gboklédou','GB'],['Gbôklé','GO'],['Gôh','GH'],['Guémon','GM'],['Hambol','HB'],['Iffou','IF'],['Indénié-Djuablin','ID'],['Kabadougou','KB'],['Poro','PO'],['San-Pédro','SP'],['Sud-Comoé','SC'],['Tchologo','TC'],['Tonkpi','TK'],['Worodougou','WO'],['Yamoussoukro','YM'],['Zanzan','ZN']],
    'EG' => [['Alexandria','AL'],['Aswan','AS'],['Asyut','AY'],['Beheira','BH'],['Beni Suef','BS'],['Cairo','CA'],['Dakahlia','DK'],['Damietta','DM'],['Fayoum','FY'],['Gharbia','GH'],['Giza','GZ'],['Ismailia','IS'],['Kafr el-Sheikh','KZ'],['Luxor','LX'],['Matrouh','MT'],['Minya','MN'],['Monufia','MF'],['New Valley','NV'],['North Sinai','NS'],['Port Said','PS'],['Qalyubia','QL'],['Qena','QN'],['Red Sea','RS'],['Sharqia','SQ'],['Sohag','SH'],['South Sinai','SS'],['Suez','SU']],
    'GQ' => [['Annobón','AN'],['Bioko Norte','BN'],['Bioko Sur','BS'],['Centro Sur','CS'],['Kié-Ntem','KN'],['Litoral','LI'],['Wele-Nzas','WN']],
    'ER' => [['Anseba','AN'],['Debub','DU'],['Debubawi K\'eyih Bahri','DK'],['Gash-Barka','GB'],['Ma\'akel','MA'],['Semenawi K\'eyih Bahri','SK']],
    'ET' => [['Addis Ababa','AA'],['Afar','AF'],['Amhara','AM'],['Benishangul-Gumuz','BE'],['Dire Dawa','DD'],['Gambela','GA'],['Harari','HA'],['Oromia','OR'],['Somali','SO'],['Southern Nations','SN'],['Tigray','TI']],
    'GA' => [['Estuaire','ES'],['Haut-Ogooué','HO'],['Moyen-Ogooué','MO'],['Ngounié','NG'],['Nyanga','NY'],['Ogooué-Ivindo','OI'],['Ogooué-Lolo','OL'],['Ogooué-Maritime','OM'],['Woleu-Ntem','WN']],
    'GM' => [['Banjul','BA'],['Central River','CR'],['Lower River','LR'],['North Bank','NB'],['Upper River','UR'],['Western','WE']],
    'GH' => [['Ahafo','AH'],['Ashanti','AS'],['Bono','BO'],['Bono East','BE'],['Central','CE'],['Eastern','EA'],['Greater Accra','GA'],['Northern','NO'],['North East','NE'],['Oti','OT'],['Savannah','SV'],['Upper East','UE'],['Upper West','UW'],['Volta','VO'],['Western','WE'],['Western North','WN']],
    'GN' => [['Boké','BO'],['Conakry','CN'],['Faranah','FA'],['Kankan','KA'],['Kindia','KI'],['Labé','LA'],['Mamou','MA'],['Nzérékoré','NZ']],
    'GW' => [['Bafatá','BA'],['Biombo','BM'],['Bissau','BS'],['Bolama','BL'],['Cacheu','CA'],['Gabú','GA'],['Oio','OI'],['Quinara','QU'],['Tombali','TO']],
    'ST' => [['Água Grande','AG'],['Cantagalo','CA'],['Caué','CU'],['Lembá','LE'],['Lobata','LO'],['Mé-Zóchi','MZ'],['Príncipe','PR']],
    'KE' => [['Baringo','01'],['Bomet','02'],['Bungoma','03'],['Busia','04'],['Elgeyo-Marakwet','05'],['Embu','06'],['Garissa','07'],['Homa Bay','08'],['Isiolo','09'],['Kajiado','10'],['Kakamega','11'],['Kericho','12'],['Kiambu','13'],['Kilifi','14'],['Kirinyaga','15'],['Kisii','16'],['Kisumu','17'],['Kitui','18'],['Kwale','19'],['Laikipia','20'],['Lamu','21'],['Machakos','22'],['Makueni','23'],['Mandera','24'],['Marsabit','25'],['Meru','26'],['Migori','27'],['Mombasa','28'],['Murang\'a','29'],['Nairobi','30'],['Nakuru','31'],['Nandi','32'],['Narok','33'],['Nyamira','34'],['Nyandarua','35'],['Nyeri','36'],['Samburu','37'],['Siaya','38'],['Taita-Taveta','39'],['Tana River','40'],['Tharaka-Nithi','41'],['Trans Nzoia','42'],['Turkana','43'],['Uasin Gishu','44'],['Vihiga','45'],['Wajir','46'],['West Pokot','47']],
    'LS' => [['Berea','BE'],['Butha-Buthe','BB'],['Leribe','LE'],['Mafeteng','MF'],['Maseru','MS'],['Mohale\'s Hoek','MH'],['Mokhotlong','MK'],['Qacha\'s Nek','QN'],['Quthing','QT'],['Thaba-Tseka','TT']],
    'LR' => [['Bomi','BM'],['Bong','BG'],['Gbarpolu','GP'],['Grand Bassa','GB'],['Grand Cape Mount','CM'],['Grand Gedeh','GG'],['Grand Kru','GK'],['Lofa','LF'],['Margibi','MG'],['Maryland','MY'],['Montserrado','MO'],['Nimba','NI'],['River Cess','RC'],['River Gee','RG'],['Sinoe','SI']],
    'LY' => [['Al Jabal al Akhdar','JA'],['Al Jabal al Gharbi','JG'],['Al Jafarah','JF'],['Al Jufrah','JU'],['Al Kufrah','KF'],['Al Marj','MJ'],['Al Wahat','WH'],['Benghazi','BA'],['Derna','DR'],['Ghat','GH'],['Misrata','MI'],['Murqub','MB'],['Nalut','NL'],['Nuqat al Khams','NQ'],['Sabha','SB'],['Sirte','SR'],['Tripoli','TB'],['Wadi al Hayaa','WD'],['Wadi al Shatii','WS'],['Zawiya','ZA']],
    'MG' => [['Antananarivo','AN'],['Antsiranana','AS'],['Fianarantsoa','FI'],['Mahajanga','MH'],['Toamasina','TM'],['Toliara','TL']],
    'MW' => [['Balaka','BA'],['Blantyre','BL'],['Chikwawa','CK'],['Chiradzulu','CZ'],['Chitipa','CT'],['Dedza','DE'],['Dowa','DO'],['Karonga','KA'],['Kasungu','KS'],['Likoma','LK'],['Lilongwe','LL'],['Machinga','MA'],['Mangochi','MG'],['Mchinji','MC'],['Mulanje','MU'],['Mwanza','MW'],['Mzimba','MZ'],['Nkhata Bay','NB'],['Nkhotakota','NK'],['Nsanje','NS'],['Ntcheu','NT'],['Ntchisi','NC'],['Phalombe','PH'],['Rumphi','RU'],['Salima','SA'],['Thyolo','TH'],['Zomba','ZO']],
    'ML' => [['Bamako','BA'],['Gao','GA'],['Kayes','KY'],['Kidal','KD'],['Koulikoro','KO'],['Mopti','MO'],['Ségou','SE'],['Sikasso','SI'],['Tombouctou','TB']],
    'MR' => [['Adrar','AD'],['Assaba','AS'],['Brakna','BR'],['Dakhlet Nouadhibou','DN'],['Gorgol','GO'],['Guidimaka','GU'],['Hodh Ech Chargui','HC'],['Hodh El Gharbi','HG'],['Inchiri','IN'],['Nouakchott Nord','NN'],['Nouakchott Ouest','NO'],['Nouakchott Sud','NS'],['Tagant','TA'],['Tiris Zemmour','TZ'],['Trarza','TR']],
    'MU' => [['Agalega','AG'],['Black River','BL'],['Flacq','FL'],['Grand Port','GP'],['Moka','MO'],['Pamplemousses','PA'],['Plaines Wilhems','PW'],['Port Louis','PL'],['Rivière du Rempart','RR'],['Rodrigues','RO'],['Savanne','SA']],
    'MA' => [['Beni Mellal-Khénifra','05'],['Casablanca-Settat','06'],['Dakhla-Oued Ed-Dahab','12'],['Drâa-Tafilalet','08'],['Fès-Meknès','03'],['Guelmim-Oued Noun','10'],['Laâyoune-Sakia El Hamra','11'],['Marrakech-Safi','07'],['Oriental','02'],['Rabat-Salé-Kénitra','04'],['Souss-Massa','09'],['Tanger-Tétouan-Al Hoceïma','01']],
    'MZ' => [['Cabo Delgado','CD'],['Gaza','GA'],['Inhambane','IN'],['Manica','MA'],['Maputo','MP'],['Maputo City','MC'],['Nampula','NA'],['Niassa','NI'],['Sofala','SO'],['Tete','TE'],['Zambezia','ZA']],
    'NA' => [['Erongo','ER'],['Hardap','HA'],['Karas','KA'],['Kavango East','KE'],['Kavango West','KW'],['Khomas','KH'],['Kunene','KU'],['Ohangwena','OW'],['Omaheke','OH'],['Omusati','OS'],['Oshana','ON'],['Oshikoto','OT'],['Otjozondjupa','OD'],['Zambezi','ZA']],
    'NE' => [['Agadez','AG'],['Diffa','DI'],['Dosso','DO'],['Maradi','MA'],['Niamey','NI'],['Tahoua','TH'],['Tillabéri','TL'],['Zinder','ZI']],
    'NG' => [['Abia','AB'],['Adamawa','AD'],['Akwa Ibom','AK'],['Anambra','AN'],['Bauchi','BA'],['Bayelsa','BY'],['Benue','BE'],['Borno','BO'],['Cross River','CR'],['Delta','DE'],['Ebonyi','EB'],['Edo','ED'],['Ekiti','EK'],['Enugu','EN'],['FCT','FC'],['Gombe','GO'],['Imo','IM'],['Jigawa','JI'],['Kaduna','KD'],['Kano','KN'],['Katsina','KT'],['Kebbi','KE'],['Kogi','KO'],['Kwara','KW'],['Lagos','LA'],['Nasarawa','NA'],['Niger','NI'],['Ogun','OG'],['Ondo','ON'],['Osun','OS'],['Oyo','OY'],['Plateau','PL'],['Rivers','RI'],['Sokoto','SO'],['Taraba','TA'],['Yobe','YO'],['Zamfara','ZA']],
    'RW' => [['Eastern','EA'],['Kigali','KG'],['Northern','NO'],['Southern','SO'],['Western','WE']],
    'SN' => [['Dakar','DK'],['Diourbel','DB'],['Fatick','FK'],['Kaffrine','KF'],['Kaolack','KL'],['Kédougou','KE'],['Kolda','KO'],['Louga','LG'],['Matam','MT'],['Saint-Louis','SL'],['Sédhiou','SE'],['Tambacounda','TC'],['Thiès','TH'],['Ziguinchor','ZG']],
    'SL' => [['Eastern','EA'],['Northern','NO'],['North Western','NW'],['Southern','SO'],['Western','WE']],
    'SO' => [['Awdal','AW'],['Bakool','BK'],['Banaadir','BN'],['Bari','BR'],['Bay','BY'],['Galguduud','GA'],['Gedo','GE'],['Hiiraan','HI'],['Jubbada Dhexe','JD'],['Jubbada Hoose','JH'],['Mudug','MU'],['Nugaal','NU'],['Sanaag','SA'],['Shabeellaha Dhexe','SD'],['Shabeellaha Hoose','SH'],['Sool','SO'],['Togdheer','TO'],['Woqooyi Galbeed','WG']],
    'ZA' => [['Eastern Cape','EC'],['Free State','FS'],['Gauteng','GT'],['KwaZulu-Natal','NL'],['Limpopo','LP'],['Mpumalanga','MP'],['Northern Cape','NC'],['North West','NW'],['Western Cape','WC']],
    'SS' => [['Central Equatoria','CE'],['Eastern Equatoria','EE'],['Jonglei','JO'],['Lakes','LA'],['Northern Bahr el Ghazal','NB'],['Unity','UN'],['Upper Nile','UN'],['Warrap','WR'],['Western Bahr el Ghazal','WB'],['Western Equatoria','WE']],
    'SD' => [['Blue Nile','BN'],['Central Darfur','CD'],['East Darfur','ED'],['Gezira','GZ'],['Kassala','KA'],['Khartoum','KH'],['North Darfur','ND'],['North Kordofan','NK'],['Northern','NO'],['Red Sea','RS'],['River Nile','RN'],['Sennar','SE'],['South Darfur','SD'],['South Kordofan','SK'],['West Darfur','WD'],['White Nile','WN']],
    'SZ' => [['Hhohho','HH'],['Lubombo','LU'],['Manzini','MA'],['Shiselweni','SH']],
    'TZ' => [['Arusha','AR'],['Dar es Salaam','DS'],['Dodoma','DO'],['Geita','GE'],['Iringa','IR'],['Kagera','KA'],['Katavi','KT'],['Kigoma','KI'],['Kilimanjaro','KJ'],['Lindi','LI'],['Manyara','MY'],['Mara','MR'],['Mbeya','MB'],['Mjini Magharibi','MM'],['Morogoro','MO'],['Mtwara','MT'],['Mwanza','MW'],['Njombe','NJ'],['Pemba North','PN'],['Pemba South','PS'],['Pwani','PW'],['Rukwa','RK'],['Ruvuma','RU'],['Shinyanga','SH'],['Simiyu','SI'],['Singida','SN'],['Songwe','SG'],['Tabora','TB'],['Tanga','TN'],['Zanzibar North','ZN'],['Zanzibar South','ZS'],['Zanzibar West','ZW']],
    'TG' => [['Centrale','CE'],['Kara','KA'],['Maritime','MA'],['Plateaux','PL'],['Savanes','SA']],
    'TN' => [['Ariana','AR'],['Béja','BJ'],['Ben Arous','BA'],['Bizerte','BZ'],['Gabès','GB'],['Gafsa','GF'],['Jendouba','JD'],['Kairouan','KR'],['Kasserine','KS'],['Kébili','KB'],['Kef','KF'],['Mahdia','MH'],['Manouba','MN'],['Médenine','MD'],['Monastir','MS'],['Nabeul','NB'],['Sfax','SF'],['Sidi Bouzid','SB'],['Siliana','SL'],['Sousse','SS'],['Tataouine','TT'],['Tozeur','TZ'],['Tunis','TU'],['Zaghouan','ZG']],
    'UG' => [['Central','CE'],['Eastern','EA'],['Northern','NO'],['Western','WE'],['Kampala','KM'],['Kalangala','KL'],['Kiboga','KG'],['Luwero','LW'],['Masaka','MS'],['Mpigi','MP'],['Mubende','MU'],['Mukono','MU'],['Nakasongola','NA'],['Rakai','RA'],['Sembabule','SE'],['Kayunga','KY'],['Wakiso','WA'],['Lyantonde','LY'],['Mityana','MT'],['Nakaseke','NA'],['Buikwe','BK'],['Bukomansimbi','BM'],['Butambala','BB'],['Buvuma','BV'],['Gomba','GO'],['Kalungu','KA'],['Kyankwanzi','KY'],['Lwengo','LW'],['Bugiri','BG'],['Busia','BA'],['Iganga','IG'],['Jinja','JI'],['Kamuli','KM'],['Kapchorwa','KP'],['Katakwi','KT'],['Kumi','KU'],['Mbale','MB'],['Pallisa','PA'],['Soroti','SO'],['Tororo','TO'],['Kaberamaido','KB'],['Mayuge','MY'],['Sironko','SI'],['Amuria','AR'],['Budaka','BU'],['Bududa','BD'],['Bukedea','BK'],['Bukwa','BW'],['Bulambuli','BL'],['Busiki','BS'],['Butaleja','BT'],['Kaliro','KL'],['Manafwa','MA'],['Namutumba','NA'],['Serere','SE'],['Abim','AB'],['Amolatar','AM'],['Amuru','AU'],['Apac','AP'],['Arua','AR'],['Gulu','GU'],['Kitgum','KT'],['Kotido','KT'],['Lira','LI'],['Moroto','MO'],['Moyo','MO'],['Nakapiripirit','NA'],['Nebbi','NE'],['Pader','PA'],['Yumbe','YU'],['Adjumani','AD'],['Agago','AG'],['Alebtong','AL'],['Amudat','AU'],['Dokolo','DO'],['Kaabong','KA'],['Koboko','KO'],['Kole','KO'],['Lamwo','LA'],['Maracha','MA'],['Nwoya','NW'],['Omoro','OM'],['Otuke','OT'],['Oyam','OY'],['Zombo','ZO'],['Bundibugyo','BN'],['Bushenyi','BS'],['Hoima','HO'],['Kabale','KB'],['Kabarole','KR'],['Kamwenge','KW'],['Kanungu','KN'],['Kasese','KS'],['Kibaale','KI'],['Kisoro','KS'],['Kyenjojo','KY'],['Masindi','MI'],['Mbarara','MR'],['Ntungamo','NT'],['Rukungiri','RU'],['Sheema','SH'],['Buhweju','BH'],['Buliisa','BU'],['Ibanda','IB'],['Isingiro','IS'],['Kiruhura','KR'],['Kiryandongo','KY'],['Mitooma','MI'],['Rubirizi','RU'],['Rukiga','RU'],['Rubanda','RU']],
    'ZM' => [['Central','CE'],['Copperbelt','CB'],['Eastern','EA'],['Luapula','LP'],['Lusaka','LK'],['Muchinga','MU'],['Northern','NO'],['North-Western','NW'],['Southern','SO'],['Western','WE']],
    'ZW' => [['Bulawayo','BU'],['Harare','HA'],['Manicaland','MA'],['Mashonaland Central','MC'],['Mashonaland East','ME'],['Mashonaland West','MW'],['Masvingo','MV'],['Matabeleland North','MN'],['Matabeleland South','MS'],['Midlands','MI']],
];

$checkStmt = $conn->prepare("SELECT id FROM states_provinces WHERE country_id = ? AND name = ? LIMIT 1");
$stmt = $conn->prepare("INSERT INTO states_provinces (country_id, name, state_code) VALUES (?, ?, ?)");
if (!$stmt || !$checkStmt) {
    die("Prepare failed: " . $conn->error . "\n");
}

$total = 0;
foreach ($allStates as $iso => $states) {
    $countryRes = $conn->query("SELECT id FROM countries WHERE iso_code = '" . $conn->real_escape_string($iso) . "' LIMIT 1");
    if (!$countryRes || $countryRes->num_rows === 0) {
        echo "Skipping $iso (country not in DB)\n";
        continue;
    }
    $countryId = (int) $countryRes->fetch_assoc()['id'];
    $count = 0;
    foreach ($states as $s) {
        $name = is_array($s) ? $s[0] : $s;
        $code = (is_array($s) && isset($s[1])) ? $s[1] : '';
        $checkStmt->bind_param("is", $countryId, $name);
        $checkStmt->execute();
        $res = $checkStmt->get_result();
        if ($res && $res->num_rows > 0) continue; // already exists
        $stmt->bind_param("iss", $countryId, $name, $code);
        if ($stmt->execute()) $count++;
    }
    if ($count > 0) {
        echo "  $iso: added $count states/provinces\n";
        $total += $count;
    }
}
$stmt->close();
$checkStmt->close();

echo "\nDone. Total new entries: $total\n";
