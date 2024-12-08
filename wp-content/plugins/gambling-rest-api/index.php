<?php

/*
 * Plugin Name:       Gambling REST API
 * Plugin URI:        https://blazingsun.space/ -> its fake url
 * Description:       Gambling REST API.
 * Version:           1.0.0
 * Requires at least: 6.4.1
 * Requires PHP:      8.2
 * Author:            Milorad Đuković
 * Author URI:        https://blazingsun.space/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://blazingsun.space/update -> its fake url
 * Text Domain:       gambling_rest_api
 * Domain Path:       /languages/
 */


ini_set(option: 'max_execution_time', value: 120);
ini_set(option: 'memory_limit', value: '1024M');
require __DIR__ . '/vendor/autoload.php';

require_once plugin_dir_path(__FILE__) . '/include/Helpers.php';
require_once plugin_dir_path(__FILE__) . '/models/country.php';
require_once plugin_dir_path(__FILE__) . '/models/city.php';
require_once plugin_dir_path(__FILE__) . '/models/currency.php';
require_once plugin_dir_path(__FILE__) . '/models/user.php';
require_once plugin_dir_path(__FILE__) . '/models/weather.php';
require_once plugin_dir_path(__FILE__) . '/models/salary.php';

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Gambling_REST_API')) {

    class Gambling_REST_API
    {

        use Helpers;
        private $plugin_directory;
        private $plugin_version = '1.0.0';
        public function __construct()
        {

            $this->define_constants();
            $this->plugin_directory = plugin_dir_url(__FILE__);

            add_action('init', [$this, 'add_cors_http_header']);
            add_action('admin_menu', [$this, 'gambling_rest_api_menu'], 100, 0);

            if (wp_doing_ajax()) {
                add_action('wp_ajax_admin_seed_submit', [$this, 'admin_seed_submit'], 100, 0);
            }

            add_action('rest_api_init', [$this, 'gambling_rest_apis'], 100, 0);

            add_action('admin_enqueue_scripts', [$this, 'add_scripts_and_styles'], 100, 0);

            add_filter('script_loader_tag', function ($tag, $handle, $src) {

                switch ($handle) {
                    case 'gambling_api_script':
                        return '<script type="module" src="' . esc_url($src) . '"></script>';
                        break;

                    default:
                        return $tag;
                        break;
                }
            }, 10, 3);

        }
        public function add_cors_http_header()
        {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Headers: Authorization, Content-Type");
        }
        public function gambling_rest_apis(): void
        {

            add_filter('rest_pre_serve_request', function ($value) {
                $this->add_cors_http_header();
                return $value;
            });

            register_rest_route('gambling_api/v1', '/set_user_salary_for_mont_and_return_annual_by_full_name', [
                'methods' => 'POST',
                'callback' => [$this, 'set_user_salary_for_mont_and_return_annual_by_full_name'],
                'permission_callback' => [$this, 'logged_content'],
                'args' => [
                    'date' => [
                        'type' => 'string',
                        'required' => true,
                        'validate_callback' => function ($param) {
                            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $param) && strtotime($param);
                        }
                    ],
                    'salary' => [
                        'validate_callback' => function ($param, $request, $key) {
                            return is_float($param) ? true : (is_integer($param) ? true : false);
                        },
                        'required' => true,
                    ],
                    'user_id' => [
                        'type' => 'number',
                        'required' => true

                    ]
                ]
            ]);

            register_rest_route('gambling_api/v1', '/weather_suggestion/(?P<id>\d+)', [
                'methods' => 'GET',
                'permission_callback' => [$this, 'logged_content'],
                'callback' => [$this, 'get_weather_suggestion'],
                'args' => [
                    'id' => [
                        'validate_callback' => function ($param) {
                            return is_numeric($param); // Ensure the ID is numeric
                        },
                    ],
                ],

            ]);
            register_rest_route('gambling_api/v1', '/get_users', [
                'methods' => 'GET',
                'permission_callback' => [$this, 'logged_content'],
                'callback' => [$this, 'get_users']

            ]);

        }

        public function get_users()
        {
            $users = User::select('id', 'first_name', 'last_name')->get();

            if (count($users) === 0) {
                return wp_send_json_error('No users found');
            }

            wp_send_json_success([
                'message' => 'Users successfuly retrieved',
                'users' => $users
            ]);
        }

        public function get_weather_suggestion($request)
        {
            $user_id = $request['id'];
            // Your logic to handle the user ID
            $city = User::where('id', $user_id)->first()->city;
            $weather = Weather::where('city_id', $city->id)->first();

            $today = $this->getCustomDate();
            $weather_forecast = json_decode($weather->forecast, true);
            $today_forecast = $weather_forecast[$today];

            if ($today_forecast === 'sunny') {
                wp_send_json_success([
                    'message' => 'it is a perfect day to go out and enjoy the weather',
                    'code' => 1,
                    'condition' => 'Today is sunny in your city'
                ]);
            } else {

                $yesterday = $this->getCustomDate(-1);
                $yesterday_forecast = $weather_forecast[$yesterday];
                $tomorrow = $this->getCustomDate(1);
                $tomorrow_forecast = $weather_forecast[$tomorrow];
                $today = $this->getCustomDate();

                if ($yesterday_forecast === 'rainy' && $today_forecast === 'rainy' && $tomorrow_forecast === 'rainy') {
                    $country = Country::where('id', $city->country_id)->first();
                    $cities = $country->cities;

                    $citiesArray = [];

                    foreach ($cities as $city) {
                        $weather = Weather::where('city_id', $city->id)->first();
                        $forecast = json_decode($weather->forecast, true);

                        if ($forecast[$today] === 'sunny') {
                            $citiesArray[] = $city->name;
                        }

                    }

                    if (count($cities) > 0) {
                        wp_send_json_success([

                            'cities' => $citiesArray,
                            'message' => 'Yesterday, today and tomorrow are rainy in your city. But not all cities in your country are rainy today.',
                            'code' => 2,
                            'condtion' => 'Yesterday, today and tomorrow are rainy in your city. But not all cities in your country are rainy today.'
                        ]);
                    } else {
                        wp_send_json_success([
                            'message' => 'its rainy in your country today, but if you love to dance in the rain you can go outside and enjoy the weather.',
                            'code' => 3,
                            'condition' => 'Yesterday, today and tomorrow are rainy in your city. All cities in your country are rainy also.'
                        ]);
                    }


                } else {
                    wp_send_json_success([
                        'message' => 'In your ciry is not so sunny today, but you should go outside and enjoy the weather.',
                        'code' => 4,
                        'condition' => 'Today is not sunny, but yesterday, today, and tomorrow in your city are not all rainy.'
                    ]);
                }

            }

        }
        public function getFirstAndLastName($fullName)
        {
            $nameParts = explode(' ', trim($fullName));
            $firstName = $nameParts[0]; // First part
            $lastName = end($nameParts); // Last part


            return ['first' => $firstName, 'last' => $lastName];
        }
        public function set_user_salary_for_mont_and_return_annual_by_full_name(WP_REST_Request $request)
        {

            $args = wp_parse_args($request->get_json_params(), [
                'date' => '',
                'salary' => '',
                'user_id' => ''

            ]);

            $user = User::where('id', '=', $args['user_id'])->first();

            $user->salaries()->create([
                'date' => $args['date'],
                'amount_in_eur' => $args['salary']
            ]);

            $salaries = $user->salaries()->limit(12)->orderBy('id', 'desc')->get();

            if ($salaries->count() < 12) {
                wp_send_json_success(['success' => true, 'message' => 'Salary added to db', 'code' => 777]);
            } else {
                $salary_accumulator = 0;
                foreach ($salaries as $salary) {
                    $salary_accumulator = $salary_accumulator + floatval($salary->amount_in_eur);
                }
                /*  wp_send_json_success(['sal' => $salaries, 'message' => $salary->amount_in_eur, 'salaries' => $sallary_accumulator]); */
                $city = City::where('id', $user->city_id)->first();
                $country = $city->country()->first();
                $currency = $country->currencies()->get();
                $average_salary = ($salary_accumulator * $currency[0]->rate_to_eur) / 12;
                $average_salary_eur = $salary_accumulator / 12;
                wp_send_json_success([
                    'message' => 'salary created',
                    'success' => true,
                    'average_salary' => $average_salary,
                    'average_salary_eur' => $average_salary_eur,
                    'condition' => 'salary is displayed in users count currency',
                    'currency' => $currency[0]->name,
                    'currency rate to eur' => floatval($currency[0]->rate_to_eur),
                    'code' => 888
                ]);
            }


        }
        public function logged_content(\WP_REST_Request $request): bool
        {
            return is_user_logged_in();
        }

        public function add_scripts_and_styles()
        {



            #wpwrap{background-color: red!important;}
            $currentScreen = get_current_screen();

            if (is_object($currentScreen) && $currentScreen->id === 'toplevel_page_gambling_rest_api') {

                wp_register_script('gambling_api_script', $this->plugin_directory . '/assets/js/Admin_App.js', [], $this->plugin_version, ['strategy' => 'defer']);
                wp_enqueue_script('gambling_api_script');

                wp_register_style('gambling_api_styles', $this->plugin_directory . '/assets/css/style.css', [], $this->plugin_version, 'all');
                wp_enqueue_style('gambling_api_styles');


            }
        }



        public function gambling_rest_api_menu()
        {



            add_menu_page(
                GAMBLING_REST_API_PRETTY, // html page title 
                GAMBLING_REST_API_PRETTY, // menu item title
                'manage_options', // permision needed for accesing plugin settings page
                GAMBLING_REST_API, // menu slug
                [$this, 'gambling_rest_api_options_page'], // main callback function for menu page
                'dashicons-rest-api', //'dashicons-image-flip-horizontal'
                100 // order in menu
            );
        }
        public function gambling_rest_api_options_page()
        {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            $seed_finished = get_option('gambling_api_seed_finished') === 'true' ? true : false;

            include_once plugin_dir_path(__FILE__) . 'templates/admin-template.php';
        }

        private function getRandomCities($nat, $resultsCount, $country_id)
        {

            // Initialize an array to hold cities data
            $cities = [];

            // Loop through the results and extract city and coordinates
            for ($i = 0; $i < $resultsCount; $i++) {

                $faker = Faker\Factory::create();

                $cities[] = [
                    'name' => $faker->city(),
                    'country_id' => $country_id,
                    'latitude' => $faker->latitude(),
                    'longitude' => $faker->longitude()
                ];
            }

            return $cities;
        }
        public function getRandomCurrencySymbol()
        {
            // List of common currency symbols
            $currencySymbols = [
                '$',   // US Dollar
                '€',   // Euro
                '£',   // British Pound
                '¥',   // Japanese Yen
                '₹',   // Indian Rupee
                '₽',   // Russian Ruble
                '₩',   // South Korean Won
                '฿',   // Thai Baht
                '₫',   // Vietnamese Dong
                '₴',   // Ukrainian Hryvnia
                '₦',   // Nigerian Naira
                '₪',   // Israeli Shekel
                '₲',   // Paraguayan Guarani
                '₡',   // Costa Rican Colón
                '₺',   // Turkish Lira
            ];

            // Pick a random symbol from the array
            return $currencySymbols[array_rand($currencySymbols)];
        }
        function get_country_currencies()
        {
            ini_set('memory_limit', '256M'); // Increase the memory limit to 256MB

            $api_url = "https://restcountries.com/v3.1/all";

            // Initialize cURL session
            $ch = curl_init($api_url);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            // Allow for chunked transfer encoding
            curl_setopt($ch, CURLOPT_ENCODING, "");  // Blank allows automatic detection

            // Execute cURL request
            $response = curl_exec($ch);

            // Check for errors
            if ($response === false) {
                die('Error occurred while fetching data: ' . curl_error($ch));
            }

            // Close the cURL session
            curl_close($ch);

            // Decode the JSON response into a PHP array
            /*    $countries = json_decode($response, true); */
            // Convert the response to UTF-8 if necessary
            $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8');
            // Decode the JSON response into an array
            $countries = json_decode($response, true);

            // Initialize an array to store country and currency info
            $countryies = [];
            $currencies = [];
            // Check if the request was successful
            $faker = Faker\Factory::create();
            if ($countries) {
                // Loop through each country and extract currency information
                foreach ($countries as $index => $country) {
                    $country_country_common_name_name = $country['name']['common'];  // Country name
                    $country_official_name = $country['name']['official'];  // Country name
                    $country_cca2 = $country['cca2'];
                    $country_cca3 = $country['cca3'];
                    if (isset($country['currencies']) && !empty($country['currencies'])) {

                        $curr = $country['currencies'];
                    } else {

                        $currObj[$faker->currencyCode()] = [
                            'name' => $faker->currencyCode(),
                            'symbol' => $this->getRandomCurrencySymbol()
                        ];
                        $curr = $currObj;
                    }


                    $countryies[] = [
                        'id' => $index + 1,
                        'name' => $country_country_common_name_name,
                        'official_name' => $country_official_name,
                        'cca2' => $country_cca2,
                        'cca3' => $country_cca3
                    ];


                    if ($curr) {

                        // Loop through currencies (as some countries might have multiple currencies)
                        foreach ($curr as $iso3 => $currency) {
                            $currencies[] = [
                                'country_id' => $index + 1,
                                'iso3' => $iso3,
                                'name' => $currency['name'],
                                'symbol' => $currency['symbol'],
                                'rate_to_eur' => $faker->randomFloat(2, 0, 1000)
                            ];

                        }

                    }

                }
            } else {
                // If the API call fails
                return "Failed to fetch country data.";
            }


            return ['countries' => $countryies, 'currencies' => $currencies];
        }

        // Example of using the function

        public function getRandomWeather()
        {
            // Define the possible weather options
            $weatherOptions = ['rainy', 'sunny'];

            // Randomly select one of the options
            return $weatherOptions[array_rand($weatherOptions)];
        }
        public function setOrUpdateOption($option_name, $option_value)
        {
            // Check if the option already exists
            if (get_option($option_name) === false) {
                // Option does not exist, so add it
                if (add_option($option_name, $option_value)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                // Option exists, so update it
                if (update_option($option_name, $option_value)) {
                    return true;
                } else {
                    return false;
                }
            }
        }


        public function getCustomDate($daysOffset = 0)
        {
            // Calculate the date based on the current date and the number of days offset
            $date = new DateTime();
            $date->modify("{$daysOffset} days");

            // Return the date in YYYY/MM/DD format
            return $date->format('Y-m-d');
        }

        public function admin_seed_submit()
        {
            if (!isset($_POST['admin_DB_seed_nonce']) || !wp_verify_nonce($_POST['admin_DB_seed_nonce'], 'admin_DB_seed_nonce')) {
                wp_send_json_error('invalid nonce');
            }



            if (!current_user_can('manage_options')) {
                wp_send_json_error('invalid permissions');
            }

            try {
                //if ( false === get_option( 'my_custom_option' ) ) {
                // Add the option with a value
                //     add_option( 'my_custom_option', 'This is my custom value' );
                // }
                //get_option('ocamba_hood_settings', array())
                //update_option('ocamba_hood_settings', array_map('sanitize_text_field', $settings));
                //delete_option('ocamba_hood_settings');
                if (
                    (isset($_POST['seed']) && !empty($_POST['seed']) && ((!isset($_POST['users']) && empty($_POST['users']))))
                ) {

                    $countries_with_currencies = $this->get_country_currencies();
                    /* var_dump($countries_with_currencies);
                    die(); */

                    /* $countries_and_currencies = $this->get_country_names_and_official_names(); */
                    /* var_dump($countries_with_currencies['countries']);
                    die(); */

                    Country::insert($countries_with_currencies['countries']);
                    Currency::insert($countries_with_currencies['currencies']);
                    foreach ($countries_with_currencies['countries'] as $country) {
                        $cities = $this->getRandomCities(nat: $country['cca2'], resultsCount: 5, country_id: $country['id']);
                        City::insert($cities);
                        /*  foreach ($cities['cities'] as $city) {
                             $users = [];
                             $faker = Faker\Factory::create();

                             $users[] = [
                                 'first_name' => $faker->firstName(),
                                 'last_name' => $faker->lastName(),
                                 'address' => $faker->streetAddress(),
                                 'phone' => $faker->phoneNumber(),
                                 'email' => $faker->email(),
                             ];
                         } */
                    }


                    wp_send_json_success(['message' => '✅ Nonce is valid! added countries, currencies and citties to db']);
                } else if ((isset($_POST['seed']) && !empty($_POST['seed'])) && (isset($_POST['users']) && !empty($_POST['users']))) {

                    $cities = City::all();
                    $users = [];
                    $weathers = [];
                    foreach ($cities as $city) {
                        for ($i = 0; $i < 5; $i++) {
                            $faker = Faker\Factory::create();

                            $users[] = [
                                'city_id' => $city->id,
                                'first_name' => $faker->firstName(),
                                'last_name' => $faker->lastName(),
                                'address' => $faker->streetAddress(),
                                'telephone' => $faker->phoneNumber(),
                                'email' => $faker->email(),
                            ];




                        }
                        $currObj[$this->getCustomDate(daysOffset: -7)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: -6)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: -5)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: -4)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: -3)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: -2)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: -1)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: 0)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: 1)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: 2)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: 3)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: 4)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: 5)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: 6)] = $this->getRandomWeather();
                        $currObj[$this->getCustomDate(daysOffset: 7)] = $this->getRandomWeather();
                        $weathers[] = [
                            'city_id' => $city->id,
                            'forecast' => json_encode(value: $currObj)
                        ];

                    }

                    Weather::insert($weathers);
                    User::insert($users);
                    $this->setOrUpdateOption(option_name: 'gambling_api_seed_finished', option_value: 'true');

                    wp_send_json_success(['message' => 'users added to db, weather added to db.']);

                }
            } catch (\Throwable $th) {
                wp_send_json_error(['message' => $th->getMessage(), 'error' => $th->getTraceAsString()]);
            }
        }

        private function define_constants()
        {
            define(constant_name: 'GAMBLING_REST_API_PATH', value: plugin_dir_path(__FILE__));
            define(constant_name: 'GAMBLING_REST_API_URL', value: plugin_dir_url(__FILE__));

            define(constant_name: 'GAMBLING_REST_API', value: 'gambling_rest_api');
            define(constant_name: 'GAMBLING_REST_API_PRETTY', value: 'Gambling REST API');
        }



        public static function activate()
        {

            self::create_database_table();
        }

        public static function deactivate()
        {

        }

        public static function uninstall()
        {
            self::removeOptionIfExists('gambling_api_seed_finished');
            self::remove_database_table();
        }
    }
}

// Register activation, deactivation, and uninstall hooks

if (class_exists('Gambling_REST_API')) {

    // Register activation hook to execute 'activate' method when the plugin is activated
    register_activation_hook(__FILE__, ['Gambling_REST_API', 'activate']);

    // Register deactivation hook to execute 'deactivate' method when the plugin is deactivated
    register_deactivation_hook(__FILE__, ['Gambling_REST_API', 'deactivate']);

    // Register uninstall hook to execute 'uninstall' method when the plugin is uninstalled
    register_uninstall_hook(__FILE__, ['Gambling_REST_API', 'uninstall']);

    $Gambling_REST_API = new Gambling_REST_API();
}