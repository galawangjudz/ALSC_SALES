<?php
header('Content-Type: application/json');
include('../../config.php');

if (isset($_GET['id_no'])) {
    $id_no = $_GET['id_no'];
    $qry = $conn->query("SELECT * FROM tbl_user_permissions WHERE user_code = '$id_no'");

    if ($qry->num_rows > 0) {
        $permissions = $qry->fetch_assoc();
        unset($permissions['id'], $permissions['user_code']);

        error_log(json_encode($permissions));
        echo json_encode([
            'status' => 'success',
            'data' => $permissions
        ]);
    } else {
        $columnsQuery = $conn->query("SHOW COLUMNS FROM tbl_user_permissions");
        $defaultPermissions = [];

        while ($column = $columnsQuery->fetch_assoc()) {
            if ($column['Field'] !== 'id' && $column['Field'] !== 'user_code') {
                $defaultPermissions[$column['Field']] = 0;
            }
        }

        echo json_encode([
            'status' => 'success',
            'data' => $defaultPermissions
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID No not provided'
    ]);
}
?>
