<?php
header('Content-Type: application/json');
include('../../config.php');

if (isset($_POST['id_no']) && isset($_POST['permissions'])) {
    $id_no = $_POST['id_no'];
    $permissions = $_POST['permissions'];

    $checkQuery = "SELECT COUNT(*) FROM tbl_user_permissions WHERE user_code = ?";
    $stmt_check = $conn->prepare($checkQuery);
    
    if ($stmt_check === false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to prepare check query: ' . $conn->error
        ]);
        exit;
    }

    $stmt_check->bind_param('i', $id_no);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        $updateQuery = "UPDATE tbl_user_permissions SET ";
        $setClauses = [];
        $types = '';  
        $values = [];
        
        foreach ($permissions as $key => $value) {
            $setClauses[] = "`$key` = ?";
            $types .= 'i'; 
            $values[] = $value;
        }

        $updateQuery .= implode(", ", $setClauses) . " WHERE user_code = ?";
        $stmt_update = $conn->prepare($updateQuery);

        if ($stmt_update === false) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to prepare update statement: ' . $conn->error
            ]);
            exit;
        }

        $values[] = $id_no;
        $stmt_update->bind_param($types . 'i', ...$values);

        if ($stmt_update->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Permissions updated successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update permissions: ' . $stmt_update->error
            ]);
        }
    } else {
        $insertQuery = "INSERT INTO tbl_user_permissions (user_code, ";
        $columns = implode(", ", array_keys($permissions)); 
        $placeholders = str_repeat('?, ', count($permissions) - 1) . '?'; 
        $insertQuery .= $columns . ") VALUES (?, " . $placeholders . ")";
        
        $stmt_insert = $conn->prepare($insertQuery);
        
        if ($stmt_insert === false) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to prepare insert statement: ' . $conn->error
            ]);
            exit;
        }

        $values = array_merge([$id_no], array_values($permissions));
        $types = 'i' . str_repeat('i', count($permissions));

        $stmt_insert->bind_param($types, ...$values);

        if ($stmt_insert->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Permissions added successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to add permissions: ' . $stmt_insert->error
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID No or permissions data not provided'
    ]);
}
?>
