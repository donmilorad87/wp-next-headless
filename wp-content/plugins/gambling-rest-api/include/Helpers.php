<?php

trait Helpers
{
    private static function get_page_slug()
    {
        return basename(esc_url(home_url($_SERVER['REQUEST_URI'])));
    }

    private static function create_database_table()
    {
        // Run the SQL queries to create the tables
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $country_table = $wpdb->prefix . GAMBLING_REST_API . '_country';
        if ($wpdb->get_var("SHOW TABLES LIKE '$country_table'") != $country_table) {
            $country_sql = "
                CREATE TABLE IF NOT EXISTS $country_table (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL UNIQUE,
                    official_name VARCHAR(255) NOT NULL,
                    cca2 VARCHAR(2) NOT NULL,
                    cca3 VARCHAR(3) NOT NULL,
                    PRIMARY KEY (id)
                ) $charset_collate;
            ";
            dbDelta($country_sql);
        }

        $currency_table = $wpdb->prefix . GAMBLING_REST_API . '_currency';
        if ($wpdb->get_var("SHOW TABLES LIKE '$currency_table'") != $currency_table) {
            $currency_sql = "
                CREATE TABLE IF NOT EXISTS $currency_table (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    country_id int(11) NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    iso3 VARCHAR(3) NOT NULL,
                    symbol VARCHAR(255) NOT NULL,
                    rate_to_eur FLOAT NOT NULL,
                    PRIMARY KEY (id),
                    FOREIGN KEY (country_id) REFERENCES $country_table(id) ON DELETE CASCADE
                ) $charset_collate;
            ";
            dbDelta($currency_sql);
        }

        $city_table = $wpdb->prefix . GAMBLING_REST_API . '_city';
        if ($wpdb->get_var("SHOW TABLES LIKE '$city_table'") != $city_table) {
            $city_sql = "
                CREATE TABLE IF NOT EXISTS $city_table (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    country_id int(11) NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    latitude FLOAT NOT NULL,
                    longitude FLOAT NOT NULL,
                    PRIMARY KEY (id),
                    FOREIGN KEY (country_id) REFERENCES $country_table(id) ON DELETE CASCADE
                ) $charset_collate;
            ";
            dbDelta($city_sql);
        }

        $weather_table = $wpdb->prefix . GAMBLING_REST_API . '_weather';
        if ($wpdb->get_var("SHOW TABLES LIKE '$weather_table'") != $weather_table) {
            $weather_sql = "
                CREATE TABLE IF NOT EXISTS $weather_table (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    city_id int(11) NOT NULL,
                    forecast JSON NOT NULL,
                    PRIMARY KEY (id),
                    FOREIGN KEY (city_id) REFERENCES $city_table(id) ON DELETE CASCADE
                ) $charset_collate;
            ";
            dbDelta($weather_sql);
        }

        $user_table = $wpdb->prefix . GAMBLING_REST_API . '_user';
        if ($wpdb->get_var("SHOW TABLES LIKE '$user_table'") != $user_table) {
            $user_sql = "
                CREATE TABLE IF NOT EXISTS $user_table (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    city_id int(11) NOT NULL,
                    first_name VARCHAR(255) NOT NULL,
                    last_name VARCHAR(255) NOT NULL,
                    address TEXT NOT NULL,
                    telephone VARCHAR(50),
                    email VARCHAR(255),
                    PRIMARY KEY (id),
                    FOREIGN KEY (city_id) REFERENCES $city_table(id) ON DELETE CASCADE
                ) $charset_collate;
            ";

            dbDelta($user_sql);
        }

        $salary_table = $wpdb->prefix . GAMBLING_REST_API . '_salary';
        if ($wpdb->get_var("SHOW TABLES LIKE '$salary_table'") != $salary_table) {
            $salary_sql = "
                CREATE TABLE IF NOT EXISTS $salary_table (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    user_id int(11) NOT NULL,
                    date DATE NOT NULL,
                    amount_in_eur float NOT NULL,
                    PRIMARY KEY (id),
                    FOREIGN KEY (user_id) REFERENCES $user_table(id) ON DELETE CASCADE
                ) $charset_collate;
            ";

            dbDelta($salary_sql);
        }
    }

    private static function remove_database_table()
    {
        global $wpdb;

        $country_table = $wpdb->prefix . GAMBLING_REST_API . '_country';
        $currency_table = $wpdb->prefix . GAMBLING_REST_API . '_currency';
        $city_table = $wpdb->prefix . GAMBLING_REST_API . '_city';
        $weather_table = $wpdb->prefix . GAMBLING_REST_API . '_weather';
        $people_table = $wpdb->prefix . GAMBLING_REST_API . '_user';
        $salary_table = $wpdb->prefix . GAMBLING_REST_API . '_salary';

        $wpdb->query("DROP TABLE IF EXISTS $salary_table");
        $wpdb->query("DROP TABLE IF EXISTS $people_table");
        $wpdb->query("DROP TABLE IF EXISTS $weather_table");
        $wpdb->query("DROP TABLE IF EXISTS $city_table");
        $wpdb->query("DROP TABLE IF EXISTS $currency_table");
        $wpdb->query("DROP TABLE IF EXISTS $country_table");
    }
    private static function removeOptionIfExists($option_name)
    {
        // Check if the option exists
        if (get_option($option_name) !== false) {
            // Option exists, delete it
            if (delete_option($option_name)) {
                return true;
            } else {
                return false;
            }
        } else {
            // Option does not exist
            return false;
        }
    }
}