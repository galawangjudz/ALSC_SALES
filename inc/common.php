<?php 

function ftom($value) {
    return number_format($value, 2);
}

function mtof($value) {
    $m_value = str_replace(",", "", $value);
    // Convert to float and format with two decimal places
    return (float)$m_value;
}
function ftoa($f_value) {
    return number_format((float)$f_value, 2, '.', '');
}


$g_site = [];
$g_acro = [];

function get_site_code($acronym, $cnx) {
    global $g_site;

    if (empty($g_site)) {
        $sql = "SELECT c_acronym, c_code FROM t_projects ORDER BY c_acronym ASC";
        $result = $cnx->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $acronym_strip = strtoupper(str_replace('-', '', $row['c_acronym']));
                $g_site[$acronym_strip] = $row['c_code'];
            }
        }
    }

    $acronym_strip = strtoupper(str_replace('-', '', $acronym));
    return array_key_exists($acronym_strip, $g_site) ? $g_site[$acronym_strip] : 0;
}

function get_site_acronym($code, $cnx) {
    global $g_acro;

    if (empty($g_acro)) {
        $sql = "SELECT c_code, c_acronym FROM t_projects ORDER BY c_code ASC";
        $result = $cnx->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $g_acro[$row['c_code']] = $row['c_acronym'];
            }
        }
    }

    return array_key_exists($code, $g_acro) ? $g_acro[$code] : 'none';
}


function get_site_name($code, $conn) {
    // Prepare the SQL query
    $code = str_replace("'", "''", $code);
    $sql = sprintf("SELECT c_name FROM t_projects WHERE c_code = '%s'", $code);
    // Execute the SQL query
    $result = odbc_exec($conn, $sql);
    if (!$result) {
        die("Query execution failed: " . odbc_errormsg($conn));
    }

    // Fetch the results into an array
    $items = [];
    while ($row = odbc_fetch_array($result)) {
        $items[] = $row;
    }
        
    // Close the connection
    odbc_close($conn);

    // Optional: Display the fetched data (for debugging purposes)
    foreach ($items as $item) {
        echo "Code: " . $item['c_code'] . ", Name: " . $item['c_first_name'] . " " . $item['c_middle_initial'] . " " . $item['c_last_name'] . ", Position: " . $item['c_position'] . ", Network: " . $item['c_network'] . ", Division: " . $item['c_division'] . "<br>";
    }


}
?>