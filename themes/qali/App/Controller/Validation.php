<?php

namespace App\Controller;

use DateTime;

class Validation
{
    /**
     * Validation rules with comprehensive handler functions
     *
     * @var array
     */
    private $validationRules = [];

    /**
     * Constructor to initialize all validation rules
     */
    public function __construct()
    {
        $this->initializeValidationRules();
    }

    /**
     * Initialize all comprehensive validation rules
     */
    private function initializeValidationRules()
    {
        $this->validationRules = [
            // Basic Field Validations
            'required' => function ($value) {
                if (is_null($value) || $value === '' || $value === [] || (is_array($value) && count($value) === 0)) {
                    return $value['name'] . ' ' . 'is required.';
                }
            },

            // Email and URL Validations
            'email' => function ($value) {
                return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : 'Invalid email format.';
            },
            'url' => function ($value) {
                return filter_var($value, FILTER_VALIDATE_URL) ? null : 'Invalid URL.';
            },

            // Numeric Validations
            'integer' => function ($value) {
                return filter_var($value, FILTER_VALIDATE_INT) !== false ? null : 'This field must be an integer.';
            },
            'decimal' => function ($value) {
                return filter_var($value, FILTER_VALIDATE_FLOAT) !== false ? null : 'This field must be a decimal number.';
            },
            'numeric' => function ($value) {
                return is_numeric($value) ? null : 'This field must be numeric.';
            },

            // Value Range Validations
            'min_value' => function ($value, $params) {

                if (!isset($params['min'])) {
                    return 'Validation parameter "min" is required.';
                }
                $min = $params['min'];

                return $value >= $min ? null : "Value must be greater than or equal to $min.";
            },
            'max_value' => function ($value, $params) {

                if (!isset($params['max'])) {
                    return 'Validation parameter "max" is required.';
                }
                $max = $params['max'];

                return $value <= $max ? null : "Value must be less than or equal to $max.";
            },
            'range' => function ($value, $params) {

                if (!isset($params['min'])) {
                    return 'Validation parameter "min" is required.';
                }
                $min = $params['min'];


                if (!isset($params['max'])) {
                    return 'Validation parameter "max" is required.';
                }
                $max = $params['max'];

                return ($value >= $min && $value <= $max) ? null : "Value must be between $min and $max.";
            },
            'positive' => function ($value) {
                return $value > 0 ? null : 'Value must be a positive number.';
            },
            'negative' => function ($value) {
                return $value < 0 ? null : 'Value must be a negative number.';
            },

            // Length Validations
            'min_length' => function ($value, $params) {
                $min_length = $params['min_length'] ?? 8;
                return strlen($value) >= $min_length ? null : "Value must be at least $min_length characters long.";
            },
            'max_length' => function ($value, $params) {
                $max_length = $params['max_length'] ?? 255;
                return strlen($value) <= $max_length ? null : "Value must not exceed $max_length characters.";
            },

            // Advanced String Validations
            'regex' => function ($value, $params) {
                return preg_match($params['pattern'], $value) ? null : 'Invalid format.';
            },
            'match' => function ($value, $params) {
                return $value === $params['compare_with'] ? null : 'Values do not match.';
            },

            // Character Requirement Validations
            'contains_uppercase' => function ($value) {
                return preg_match('/[A-Z]/', $value) ? null : 'Value must contain at least one uppercase letter.';
            },
            'contains_number' => function ($value) {
                return preg_match('/\d/', $value) ? null : 'Value must contain at least one numeric character.';
            },
            'contains_special' => function ($value) {
                return preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value) ? null : 'Value must contain at least one special character.';
            },

            // Selection and Array Validations
            'in_array' => function ($value, $params) {
                $allowed_values = $params['allowed_values'] ?? [];
                return in_array($value, $allowed_values) ? null : 'Invalid selection.';
            },
            'empty_array' => function ($value) {
                return is_array($value) && !empty($value);
            },
            'multi_select_count' => function ($value, $params) {

                if (!isset($params['min'])) {
                    return 'Validation parameter "min" is required.';
                }
                $min = $params['min'];


                if (!isset($params['max'])) {
                    return 'Validation parameter "max" is required.';
                }
                $max = $params['max'];

                $count = is_array($value) ? count($value) : 0;
                if ($count < $min) {
                    return "You must select at least $min options.";
                }
                if ($count > $max) {
                    return "You can select up to $max options.";
                }
                return null;
            },
            'unique_select' => function ($value) {
                return count($value) === count(array_unique($value)) ? null : 'Duplicate selections are not allowed.';
            },

            // Date and Time Validations
            'date_format' => function ($value, $params) {
                $format = $params['format'] ?? 'Y-m-d';
                $d = DateTime::createFromFormat($format, $value);
                return $d && $d->format($format) === $value ? null : "Invalid date format, expected $format.";
            },
            'valid_date' => function ($value) {
                $d = DateTime::createFromFormat('Y-m-d', $value);
                return $d && $d->format('Y-m-d') === $value ? null : 'Invalid date.';
            },
            'min_date' => function ($value, $params) {
                $min_date = $params['min_date'] ?? null;
                $input_date = DateTime::createFromFormat('Y-m-d', $value);
                $compare_date = DateTime::createFromFormat('Y-m-d', $min_date);
                return ($input_date >= $compare_date) ? null : "Date must be on or after $min_date.";
            },
            'max_date' => function ($value, $params) {
                $max_date = $params['max_date'] ?? null;
                $input_date = DateTime::createFromFormat('Y-m-d', $value);
                $compare_date = DateTime::createFromFormat('Y-m-d', $max_date);
                return ($input_date <= $compare_date) ? null : "Date must be on or before $max_date.";
            },
            'date_range' => function ($value, $params) {
                $min_date = $params['min_date'] ?? null;
                $max_date = $params['max_date'] ?? null;
                $input_date = DateTime::createFromFormat('Y-m-d', $value);
                $min_compare = DateTime::createFromFormat('Y-m-d', $min_date);
                $max_compare = DateTime::createFromFormat('Y-m-d', $max_date);
                return ($input_date >= $min_compare && $input_date <= $max_compare)
                    ? null
                    : "Date must be between $min_date and $max_date.";
            },
            'future_date' => function ($value) {
                $input_date = DateTime::createFromFormat('Y-m-d', $value);
                $now = new DateTime();
                return ($input_date > $now) ? null : 'Date must be in the future.';
            },
            'past_date' => function ($value) {
                $input_date = DateTime::createFromFormat('Y-m-d', $value);
                $now = new DateTime();
                return ($input_date < $now) ? null : 'Date must be in the past.';
            },
            // Advanced Date Validations
            'age_limit' => function ($value, $params) {
                $min_age = $params['min_age'] ?? 0;
                $max_age = $params['max_age'] ?? PHP_INT_MAX;
                $birth_date = new DateTime($value);
                $now = new DateTime();
                $age = $now->diff($birth_date)->y;
                if ($age < $min_age) {
                    return "Age must be at least $min_age.";
                }
                if ($age > $max_age) {
                    return "Age must be no more than $max_age.";
                }
                return null;
            },
            'specific_day' => function ($value, $params) {
                $day = $params['day'] ?? '';
                $date = new DateTime($value);
                return $date->format('l') === $day ? null : "Date must be on $day.";
            },

            // File Validations
            'file_type' => function ($file, $params) {
                $allowed_types = $params['allowed_types'] ?? [];
                $file_type = pathinfo($file['name'], PATHINFO_EXTENSION);
                return in_array($file_type, $allowed_types) ? null : 'Invalid file type. Allowed types: ' . implode(', ', $allowed_types);
            },
            'file_size' => function ($file, $params) {
                $max_size = $params['max_size'] ?? 2 * 1024 * 1024; // Default 2MB
                return $file['size'] <= $max_size ? null : 'File size exceeds the maximum allowed size of ' . ($max_size / 1024 / 1024) . ' MB.';
            },
            'required_file' => function ($file) {
                return !empty($file['name']) ? null : 'File is required.';
            },
            'file_count' => function ($value, $params) {
                $count = count($value);
                $min = $params['min'] ?? 1;

                if (!isset($params['max'])) {
                    return 'Validation parameter "max" is required.';
                }
                $max = $params['max'];

                if ($count < $min) {
                    return "At least $min files are required.";
                }
                if ($count > $max) {
                    return "You can upload up to $max files.";
                }
                return null;
            },
            'compressed_file' => function ($value) {
                $allowed_types = ['zip', 'rar'];
                $file_type = pathinfo($value['name'], PATHINFO_EXTENSION);
                return in_array($file_type, $allowed_types) ? null : 'File must be a compressed type (zip, rar).';
            },
            'file_content' => function ($value, $params) {
                $required_content = $params['content'] ?? '';
                if (!isset($value['tmp_name']) || !is_uploaded_file($value['tmp_name'])) {
                    return 'Invalid or missing file.';
                }
                $file_content = file_get_contents($value['tmp_name']);

                return strpos($file_content, $required_content) !== false
                    ? null
                    : "File does not contain the required content.";
            },

            // Advanced String Checks
            'contains' => function ($value, $params) {
                $required = $params['required'] ?? [];
                foreach ($required as $char) {
                    if (strpos($value, $char) === false) {
                        return "Value must contain the character '$char'.";
                    }
                }
                return null;
            },
            'not_contains' => function ($value, $params) {
                $banned = $params['banned'] ?? [];
                foreach ($banned as $char) {
                    if (strpos($value, $char) !== false) {
                        return "Value must not contain the character '$char'.";
                    }
                }
                return null;
            },
            'pattern_repetition' => function ($value, $params) {
                $pattern = $params['pattern'] ?? '';
                $max_repeats = $params['max_repeats'] ?? 1;
                preg_match_all($pattern, $value, $matches);
                return count($matches[0]) <= $max_repeats
                    ? null
                    : "The pattern '$pattern' is repeated too many times.";
            },

            // Array and Multi-Value Validations
            'all_in_array' => function ($value, $params) {
                $allowed_values = $params['allowed_values'] ?? [];
                foreach ((array) $value as $item) {
                    if (!in_array($item, $allowed_values)) {
                        return "Invalid value '$item' in selection.";
                    }
                }
                return null;
            },
            'unique_values' => function ($value) {
                return count($value) === count(array_unique($value)) ? null : 'Duplicate values are not allowed.';
            },
            'count_values' => function ($value, $params) {
                $min = $params['min'] ?? 1;

                if (!isset($params['max'])) {
                    return 'Validation parameter "max" is required.';
                }
                $max = $params['max'];

                $count = count($value);
                if ($count < $min) {
                    return "At least $min values are required.";
                }
                if ($count > $max) {
                    return "No more than $max values are allowed.";
                }
                return null;
            },

            // Advanced Technical Validations
            'valid_json' => function ($value) {
                json_decode($value);
                return (json_last_error() === JSON_ERROR_NONE) ? null : 'Invalid JSON.';
            },
            'valid_uuid' => function ($value) {
                return preg_match('/^[a-f0-9\-]{36}$/i', $value) ? null : 'Invalid UUID.';
            },

            // Network and Address Validations
            'ip_v4' => function ($value) {
                return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? null : 'Invalid IPv4 address.';
            },
            'ip_v6' => function ($value) {
                return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? null : 'Invalid IPv6 address.';
            },
            'valid_domain' => function ($value) {
                return preg_match('/^[a-z0-9\-]+\.[a-z]{2,}$/i', $value) ? null : 'Invalid domain.';
            },
            'specific_subdomain' => function ($value, $params) {
                $subdomain = $params['subdomain'] ?? '';
                return strpos($value, "$subdomain.") === 0
                    ? null
                    : "Domain must start with '$subdomain.'.";
            },

            // Color Validations
            'valid_hex' => function ($value) {
                return preg_match('/^#[0-9A-Fa-f]{6}$/', $value) ? null : 'Invalid HEX color.';
            },
            'valid_rgb' => function ($value) {
                return preg_match('/^rgb\((\d{1,3}), (\d{1,3}), (\d{1,3})\)$/', $value)
                    ? null
                    : 'Invalid RGB color.';
            },

            // Boolean validations
            'boolean_true' => function ($value) {
                return $value === true ? null : 'Value must be true.';
            },
            'boolean_false' => function ($value) {
                return $value === false ? null : 'Value must be false.';
            },

            // Conditional fields
            'conditional' => function ($value, $params) {
                $field_value = $params['field_value'] ?? '';
                $expected_value = $params['expected_value'] ?? '';
                return $field_value === $expected_value
                    ? null
                    : "This field depends on another field having the value '$expected_value'.";
            },

            // Advanced Rules
            'strong_password' => function ($value) {
                $errors = [];
                if (strlen($value) < 6) {
                    $errors[] = 'Password must be at least 6 characters long.';
                }
                if (!preg_match('/[A-Z]/', $value)) {
                    $errors[] = 'Password must include at least one uppercase letter.';
                }
                if (!preg_match('/[a-z]/', $value)) {
                    $errors[] = 'Password must include at least one lowercase letter.';
                }
                if (!preg_match('/\d/', $value)) {
                    $errors[] = 'Password must include at least one numeric character.';
                }
                if (!preg_match('/[^a-zA-Z\d]/', $value)) {
                    $errors[] = 'Password must include at least one special character.';
                }
                return empty($errors) ? null : $errors;
            },
            'mobile' => function ($value) {
                return preg_match('/^09[0-9]{9}$/', $value) ? null : 'Invalid mobile number.';
            },
            'persian' => function ($value) {
                return preg_match('/^[\x{0600}-\x{06FF}\s]+$/u', $value) ? null : 'This field must contain only Persian characters.';
            },
            'english' => function ($value) {
                return preg_match('/^[A-Za-z\s]+$/', $value) ? null : 'This field must contain only English characters.';
            },
            'postal_code' => function ($value) {
                return (
                    strlen($value) === 10 &&                  // بررسی طول
                    ctype_digit($value) &&                   // بررسی عددی بودن
                    !preg_match('/[02]/', $value) &&         // عدم وجود 0 و 2
                    substr($value, 0, 4) !== str_repeat($value[0], 4) && // چهار رقم اول مشابه نباشند
                    $value[4] !== '5' &&                     // رقم پنجم 5 نباشد
                    $value !== str_repeat($value[0], 10)     // همه ارقام مشابه نباشند
                ) ? null : 'Postal Code is invalid.';
            },
            'national_code' => function ($value) {
                if (!preg_match('/^\d{10}$/', $value)) {
                    return 'Invalid national code format.';
                }

                $check = (int) $value[9];
                $sum = array_reduce(
                    range(0, 8),
                    fn($carry, $i) => $carry + (int) $value[$i] * (10 - $i),
                    0
                );

                $mod = $sum % 11;

                return ($mod < 2 && $check === $mod) || ($mod >= 2 && $check === 11 - $mod)
                    ? null
                    : 'Invalid national code.';
            },
            'national_id' => function ($value) {
                if (strlen($value) !== 11 || intval($value) === 0) {
                    return 'The national ID must be 11 digits and non-zero.';
                }

                if (intval(substr($value, 3, 6)) === 0) {
                    return 'The middle part of the national ID cannot be all zeros.';
                }

                $controlDigit = intval($value[10]);
                $d = intval($value[9]) + 2;

                $weights = [29, 27, 23, 19, 17];
                $sum = array_reduce(range(0, 9), function ($carry, $i) use ($value, $d, $weights) {
                    return $carry + ($d + intval($value[$i])) * $weights[$i % 5];
                }, 0);

                $remainder = $sum % 11;
                $calculatedDigit = ($remainder === 10 ? 0 : $remainder);

                return $controlDigit === $calculatedDigit ? null : 'The national ID is invalid.';
            },
            'credit_card' => function ($value) {
                if (!ctype_digit($value)) {
                    return 'Invalid credit card number.';
                }

                $sum = array_reduce(
                    array_reverse(str_split($value)),
                    function ($carry, $digit, $index) {
                        $digit = (int) $digit;
                        if ($index % 2 !== 0) {
                            $digit = $digit * 2 > 9 ? $digit * 2 - 9 : $digit * 2;
                        }
                        return $carry + $digit;
                    },
                    0
                );

                return $sum % 10 === 0 ? null : 'Invalid credit card number.';
            },
            'iban' => function ($value) {
                $value = strtolower(str_replace(' ', '', $value));
                if (strlen($value) !== 26 || substr($value, 0, 2) !== 'ir') {
                    return 'Invalid IBAN format.';
                }
                $ibanNumeric = substr($value, 4) . '1827' . substr($value, 2, 2);
                $ibanNumeric = preg_replace_callback('/[a-z]/', function ($match) {
                    return ord($match[0]) - 87;
                }, $ibanNumeric);

                return bcmod($ibanNumeric, '97') === '1' ? null : 'Invalid IBAN.';
            },
            'imei' => function ($value) {
                if (strlen($value) !== 15 || !ctype_digit($value)) {
                    return 'The IMEI number must be 15 digits.';
                }

                $sum = 0;

                for ($i = 0; $i < 15; $i++) {
                    $digit = (int)$value[$i];

                    if ($i % 2 === 1) {
                        $digit = ($digit * 2 > 9) ? $digit * 2 - 9 : $digit * 2;
                    }

                    $sum += $digit;
                }

                return $sum % 10 === 0 ? null : 'The IMEI number is invalid.';
            },
        ];
    }

    /**
     * Validate fields based on specified rules
     *
     * @param array $fields Fields to validate
     * @return array|null Validation errors or null if all fields are valid
     */
    public function validate(array $fields)
    {
        foreach ($fields as $field) {
            $field_name = $field['name'];
            $field_value = $field['value'];
            $rules = $field['rules'] ?? [];

            // First, check if the field is empty; if it's empty and required, return an error
            $errors = [];
            if (empty($field_value)) {
                // Check for required rules for fields that are empty
                foreach ($rules as $rule) {
                    if ($rule['type'] === 'required') {
                        $handler = $this->validationRules[$rule['type']] ?? null;
                        if ($handler) {
                            $error = $handler($field_value, $field_name);
                            if ($error) {
                                $errors[] = $error;
                            }
                        }
                    }
                }
            } else {
                // If the field is not empty, proceed to check other validation rules
                foreach ($rules as $rule) {
                    $rule_type = $rule['type'];
                    $handler = $this->validationRules[$rule_type] ?? null;

                    if ($handler) {
                        $error = $handler($field_value, $rule);
                        if ($error) {
                            $errors[] = $error;
                        }
                    } else {
                        // If the rule is unknown
                        $errors[] = "Unknown validation rule: $rule_type";
                    }
                }
            }

            // If there are any errors, send them back
            if (!empty($errors)) {
                wp_send_json_error([
                    'field' => $field_name,
                    'message' => implode(', ', $errors),
                ]);
            }
        }
    }

    /**
     * Add custom validation rule
     *
     * @param string $rule_name Name of the rule
     * @param callable $handler Validation handler function
     */
    public function addRule(string $rule_name, callable $handler)
    {
        $this->validationRules[$rule_name] = $handler;
    }
}
